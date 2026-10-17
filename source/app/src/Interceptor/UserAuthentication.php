<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Interceptor;

use AppCore\Domain\Auth\MultipleMatches;
use AppCore\Domain\Auth\ParameterMissingException;
use AppCore\Domain\Auth\PasswordIncorrect;
use AppCore\Domain\Auth\PasswordMissing;
use AppCore\Domain\Auth\UserAuthenticatorInterface;
use AppCore\Domain\Auth\UsernameMissing;
use AppCore\Domain\Auth\UsernameNotFound;
use AppCore\Domain\LoggerInterface;
use AppCore\Domain\Session\SessionInterface;
use AppCore\Domain\User\UserRepositoryInterface;
use Aura\Auth\Exception\MultipleMatches as AuraMultipleMatches;
use Aura\Auth\Exception\PasswordIncorrect as AuraPasswordIncorrect;
use Aura\Auth\Exception\PasswordMissing as AuraPasswordMissing;
use Aura\Auth\Exception\UsernameMissing as AuraUsernameMissing;
use Aura\Auth\Exception\UsernameNotFound as AuraUsernameNotFound;
use Aura\Session\Session as AuraSession;
use BEAR\Resource\NullRenderer;
use BEAR\Resource\ResourceObject;
use DateTimeImmutable;
use Koriym\HttpConstants\StatusCode;
use MyVendor\MyProject\Annotation\UserLogin;
use MyVendor\MyProject\Annotation\UserLogout;
use MyVendor\MyProject\InputQuery\Customer\LoginUserInput;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Named;

use function array_filter;
use function array_shift;
use function assert;
use function call_user_func;

/**
 * User認証
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
readonly class UserAuthentication implements MethodInterceptor
{
    public function __construct(
        private AuraSession $auraSession,
        private UserAuthenticatorInterface $authenticator,
        #[Named('user')]
        private LoggerInterface $logger,
        private SessionInterface $session,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /** @psalm-suppress ArgumentTypeCoercion */
    public function invoke(MethodInvocation $invocation): mixed
    {
        $login = $invocation->getMethod()->getAnnotation(UserLogin::class);
        if ($login instanceof UserLogin) {
            // @phpstan-ignore-next-line
            return $this->login($invocation, $login->onFailure);
        }

        $logout = $invocation->getMethod()->getAnnotation(UserLogout::class);
        if ($logout instanceof UserLogout) {
            // @phpstan-ignore-next-line
            return $this->logout($invocation);
        }

        return $invocation->proceed();
    }

    /** @param MethodInvocation<ResourceObject> $invocation */
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
            try {
                $this->authenticator->login($input->username, $input->password);
                $this->auraSession->getCsrfToken()->regenerateValue();
                $this->logger->log('[logged] ' . $input->username);
            } catch (AuraUsernameMissing $usernameMissing) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new UsernameMissing(
                        $usernameMissing->getMessage(),
                        $usernameMissing->getCode(),
                        $usernameMissing->getPrevious(),
                    ),
                );
            } catch (AuraPasswordMissing $passwordMissing) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new PasswordMissing(
                        $passwordMissing->getMessage(),
                        $passwordMissing->getCode(),
                        $passwordMissing->getPrevious(),
                    ),
                );
            } catch (AuraUsernameNotFound $usernameNotFound) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new UsernameNotFound(
                        $usernameNotFound->getMessage(),
                        $usernameNotFound->getCode(),
                        $usernameNotFound->getPrevious(),
                    ),
                );
            } catch (AuraMultipleMatches $multipleMatches) {
                return call_user_func(
                    [$invocation->getThis(), $onFailure],
                    new MultipleMatches(
                        $multipleMatches->getMessage(),
                        $multipleMatches->getCode(),
                        $multipleMatches->getPrevious(),
                    ),
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

            $user = $this->userRepository->findByUsername($input->username);
            $user = $user->withLoggedIn(new DateTimeImmutable());
            $this->userRepository->store($user);

            $continue = $this->session->get('user:continue', '');
            $this->session->set('user:continue', '');

            $ro = $invocation->proceed();
            assert($ro instanceof ResourceObject);

            $ro->setRenderer(new NullRenderer());
            $ro->code = StatusCode::FOUND;
            $ro->headers = [
                'Location' => $continue === '' ? $this->authenticator->getAuthRedirect() . '?logged' : $continue,
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

    /** @param MethodInvocation<ResourceObject> $invocation */
    private function logout(MethodInvocation $invocation): mixed
    {
        $this->authenticator->logout();

        $ro = $invocation->proceed();
        assert($ro instanceof ResourceObject);

        $ro->setRenderer(new NullRenderer());
        $ro->code = StatusCode::FOUND;
        $ro->headers = ['Location' => $this->authenticator->getUnauthRedirect() . '?logged_out'];
        $ro->view = '';
        $ro->body = [];

        return $ro;
    }
}
