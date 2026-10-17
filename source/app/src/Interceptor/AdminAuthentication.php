<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use AppCore\Domain\Auth\AdminAuthenticatorInterface;
use AppCore\Domain\Auth\AdminPasswordLocking;
use AppCore\Domain\Auth\MaxAttemptsExceeded;
use AppCore\Domain\Auth\MultipleMatches;
use AppCore\Domain\Auth\ParameterMissingException;
use AppCore\Domain\Auth\PasswordIncorrect;
use AppCore\Domain\Auth\PasswordMissing;
use AppCore\Domain\Auth\UsernameMissing;
use AppCore\Domain\Auth\UsernameNotFound;
use AppCore\Domain\LoggerInterface;
use AppCore\Domain\Session\SessionInterface;
use AppCore\Domain\Throttle\ThrottlingHandlerInterface;
use Aura\Auth\Exception\MultipleMatches as AuraMultipleMatches;
use Aura\Auth\Exception\PasswordIncorrect as AuraPasswordIncorrect;
use Aura\Auth\Exception\PasswordMissing as AuraPasswordMissing;
use Aura\Auth\Exception\UsernameMissing as AuraUsernameMissing;
use Aura\Auth\Exception\UsernameNotFound as AuraUsernameNotFound;
use Aura\Session\CsrfToken;
use BEAR\Resource\NullRenderer;
use BEAR\Resource\ResourceObject;
use DateTimeImmutable;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Annotation\AdminLogin;
use MyVendor\MyProject\Annotation\AdminLogout;
use MyVendor\MyProject\Annotation\AdminVerifyPassword;
use MyVendor\MyProject\InputQuery\Admin\LoginUserInput;
use MyVendor\MyProject\InputQuery\Admin\UserPasswordInput;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;
use Throwable;

use function array_filter;
use function array_shift;
use function assert;
use function call_user_func;
use function is_string;
use function sha1;

/**
 * Admin認証
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
readonly class AdminAuthentication implements MethodInterceptor
{
    public function __construct(
        #[Named('admin_auth_attempt_interval')]
        private string $attemptInterval,
        private AdminAuthenticatorInterface $authenticator,
        private CsrfToken $csrfToken,
        #[Named('admin')]
        private LoggerInterface $logger,
        #[Named('admin_auth_max_attempts')]
        private int $maxAttempts,
        private SessionInterface $session,
        private ThrottlingHandlerInterface $throttlingHandler,
    ) {
    }

    /** @psalm-suppress ArgumentTypeCoercion */
    public function invoke(MethodInvocation $invocation): mixed
    {
        $method = $invocation->getMethod();

        $login = $method->getAnnotation(AdminLogin::class);
        if ($login instanceof AdminLogin) {
            // @phpstan-ignore-next-line
            return $this->login($invocation, $login->onFailure);
        }

        $logout = $method->getAnnotation(AdminLogout::class);
        if ($logout instanceof AdminLogout) {
            // @phpstan-ignore-next-line
            return $this->logout($invocation);
        }

        $verifyPassword = $method->getAnnotation(AdminVerifyPassword::class);
        if ($verifyPassword instanceof AdminVerifyPassword) {
            // @phpstan-ignore-next-line
            return $this->verifyPassword($invocation, $verifyPassword->onFailure);
        }

        return $invocation->proceed();
    }

    /**
     * @param MethodInvocation<ResourceObject> $invocation
     *
     * @psalm-suppress ArgumentTypeCoercion
     * @SuppressWarnings("PHPMD.ElseExpression")
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    private function login(MethodInvocation $invocation, string $onFailure): mixed
    {
        $args = $invocation->getNamedArguments();
        $array = array_filter(
            $args->getArrayCopy(),
            static fn ($arg) => $arg instanceof LoginUserInput,
        );
        if (empty($array)) {
            return call_user_func(
                [$invocation->getThis(), $onFailure],
                new ParameterMissingException(),
            );
        }

        $input = array_shift($array);

        if ($input->isValid()) {
            $throttleKey = sha1($input->username);

            if ($this->throttlingHandler->isExceeded($throttleKey)) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new MaxAttemptsExceeded(),
                );
            }

            try {
                if ($input->remember === 'yes') {
                    $this->authenticator->rememberLogin($input->username, $input->password);
                } else {
                    $this->authenticator->login($input->username, $input->password);
                }
            } catch (Throwable $throwable) {
                $class = match ($throwable::class) {
                    AuraUsernameMissing::class => UsernameMissing::class,
                    AuraPasswordMissing::class => PasswordMissing::class,
                    AuraUsernameNotFound::class => UsernameNotFound::class,
                    AuraMultipleMatches::class => MultipleMatches::class,
                    AuraPasswordIncorrect::class => PasswordIncorrect::class,
                    default => null,
                };

                if ($class === null) {
                    throw $throwable;
                }

                $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
                $this->throttlingHandler->countUp($throttleKey, $remoteIp, $this->attemptInterval, $this->maxAttempts);

                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new $class(
                        $throwable->getMessage(),
                        $throwable->getCode(),
                        $throwable->getPrevious(),
                    ),
                );
            }

            $this->logger->log('[logged] ' . $input->username);
            $this->csrfToken->regenerateValue();

            $this->throttlingHandler->clear($throttleKey);

            $continue = $this->session->get('admin:continue', '');
            $expire = (new DateTimeImmutable())->modify('+5 min')->getTimestamp();
            $this->session->set('admin:continue', '');
            $this->session->set('admin:protect:continue', '');
            $this->session->set('admin:protect:locking', AdminPasswordLocking::Unlocked->name);
            $this->session->set('admin:protect:expire', (string) $expire);

            $ro = $invocation->proceed();
            assert($ro instanceof ResourceObject);

            $ro->setRenderer(new NullRenderer());
            $ro->code = StatusCode::FOUND;
            $ro->headers = [
                'Location' => $continue === '' ?
                    $this->authenticator->getAuthRedirect() . '?logged' :
                    $continue,
            ];
            $ro->view = '';
            $ro->body = [];

            return $ro;
        }

        $ro = $invocation->getThis();

        $ro->setRenderer(new NullRenderer());
        $ro->code = StatusCode::FOUND;
        $ro->headers = ['Location' => $this->authenticator->getUnauthRedirect() . '?parameter_error'];
        $ro->view = '';
        $ro->body = [];

        return $ro;
    }

    /**
     * @param MethodInvocation<ResourceObject> $invocation
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function logout(MethodInvocation $invocation): mixed
    {
        $this->session->set('admin:continue', '');
        $this->session->set('admin:protect:continue', '');
        $this->session->set('admin:protect:locking', AdminPasswordLocking::Locked->name);
        $this->session->set('admin:protect:expire', '0');

        $ro = $invocation->proceed();
        assert($ro instanceof ResourceObject);

        $this->authenticator->logout();

        $ro->setRenderer(new NullRenderer());
        $ro->code = StatusCode::FOUND;
        $ro->headers = ['Location' => $this->authenticator->getUnauthRedirect() . '?logged_out'];
        $ro->view = '';
        $ro->body = [];

        return $ro;
    }

    /**
     * @param MethodInvocation<ResourceObject> $invocation
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function verifyPassword(MethodInvocation $invocation, string $onFailure): mixed
    {
        $args = $invocation->getNamedArguments();

        $input = null;
        foreach ($args as $value) {
            if ($value instanceof UserPasswordInput) {
                $input = $value;

                break;
            }
        }

        if ($input === null) {
            return call_user_func(
                [$invocation->getThis(), $onFailure],
                new ParameterMissingException(),
            );
        }

        $userName = $this->authenticator->getUserName();

        if (is_string($userName)) {
            try {
                $this->authenticator->verifyPassword(
                    $userName,
                    $input->password,
                );
            } catch (AuraPasswordIncorrect $passwordIncorrect) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new PasswordIncorrect(
                        $passwordIncorrect->getMessage(),
                        $passwordIncorrect->getCode(),
                        $passwordIncorrect->getPrevious(),
                    ),
                );
            }

            $this->csrfToken->regenerateValue();
            $continue = $this->session->get('admin:protect:continue', '');
            $expire = (new DateTimeImmutable())->modify('+5 min')->getTimestamp();
            $this->session->set('admin:protect:continue', '');
            $this->session->set('admin:protect:locking', AdminPasswordLocking::Unlocked->name);
            $this->session->set('admin:protect:expire', (string) $expire);

            $ro = $invocation->proceed();
            assert($ro instanceof ResourceObject);

            $ro->setRenderer(new NullRenderer());
            $ro->code = StatusCode::FOUND;
            $ro->headers = [
                'Location' => $continue === '' ?
                    $this->authenticator->getAuthRedirect() :
                    $continue,
            ];
            $ro->view = '';
            $ro->body = [];

            return $ro;
        }

        return call_user_func(
            [$invocation->getThis(), $onFailure],
            new PasswordIncorrect(),
        );
    }
}
