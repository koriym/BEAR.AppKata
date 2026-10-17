<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page;

use Aura\Session\CsrfToken;
use BEAR\Package\Provide\Error\Status;
use BEAR\Resource\ResourceInterface;
use MyVendor\MyProject\Exception\CsrfViolationException;
use MyVendor\MyProject\Fake\FakeSessionModule;
use MyVendor\MyProject\Injector;
use MyVendor\MyProject\Interceptor\CsrfGuard;
use MyVendor\MyProject\Module\Matcher\PageResourceMatcher;
use MyVendor\MyProject\Module\Matcher\UnsafeMethodMatcher;
use MyVendor\MyProject\Resource\App\Cache\AdminDashboard;
use MyVendor\MyProject\Resource\Page\Admin\ContactDemo;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ray\Di\InjectorInterface;
use ReflectionClass;
use ReflectionMethod;

use function sprintf;

/** @psalm-suppress PropertyNotSetInConstructor */
final class CsrfGuardTest extends TestCase
{
    private const string CONTACT_DEMO = 'page://self/admin/contact-demo';

    private InjectorInterface $injector;
    private ResourceInterface $resource;

    #[Override]
    protected function setUp(): void
    {
        $_SESSION = [];
        $this->injector = Injector::getOverrideInstance('html-app', new FakeSessionModule());
        $this->resource = $this->injector->getInstance(ResourceInterface::class);
    }

    #[Override]
    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testMissingTokenIsRejected(): void
    {
        $this->expectException(CsrfViolationException::class);

        $this->resource->post(self::CONTACT_DEMO, ['username' => 'koriym', 'mode' => 'Complete']);
    }

    public function testForgedTokenIsRejected(): void
    {
        $this->expectException(CsrfViolationException::class);

        $this->resource->post(self::CONTACT_DEMO, ['username' => 'koriym', 'mode' => 'Complete', CsrfGuard::FIELD => 'forged']);
    }

    public function testValidTokenIsAccepted(): void
    {
        $ro = $this->resource->post(self::CONTACT_DEMO, ['username' => 'koriym', 'mode' => 'Complete', CsrfGuard::FIELD => $this->token()]);

        $this->assertSame(303, $ro->code);
    }

    public function testSafeMethodNeedsNoToken(): void
    {
        $ro = $this->resource->get(self::CONTACT_DEMO);

        $this->assertSame(200, $ro->code);
    }

    /** The guard runs before authentication, so a page without a form is protected as well. */
    public function testPageWithoutFormIsGuarded(): void
    {
        $this->expectException(CsrfViolationException::class);

        $this->resource->post('page://self/admin/logout');
    }

    /** The admin layout needs an authenticated context to render, so the user login page is checked instead. */
    public function testRenderedFormCarriesToken(): void
    {
        $html = $this->resource->get('page://self/user/login')->toString();

        $this->assertStringContainsString(
            sprintf('name="%s" value="%s"', CsrfGuard::FIELD, $this->token()),
            $html,
        );
    }

    public function testViolationIsBadRequest(): void
    {
        $this->assertSame(400, (new Status(new CsrfViolationException()))->code);
    }

    public function testPageResourceMatcher(): void
    {
        $matcher = new PageResourceMatcher();

        $this->assertTrue($matcher->matchesClass(self::reflect(ContactDemo::class), []));
        $this->assertFalse($matcher->matchesClass(self::reflect(AdminDashboard::class), []));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function methods(): iterable
    {
        yield 'onGet' => ['onGet', false];
        yield 'onPost' => ['onPost', true];
        yield 'onPostValidationFailed' => ['onPostValidationFailed', false];
    }

    #[DataProvider('methods')]
    public function testUnsafeMethodMatcher(string $method, bool $expected): void
    {
        $this->assertSame(
            $expected,
            (new UnsafeMethodMatcher())->matchesMethod(new ReflectionMethod(ContactDemo::class, $method), []),
        );
    }

    /**
     * @param class-string $class
     *
     * @return ReflectionClass<object>
     */
    private static function reflect(string $class): ReflectionClass
    {
        return new ReflectionClass($class);
    }

    private function token(): string
    {
        return $this->injector->getInstance(CsrfToken::class)->getValue();
    }
}
