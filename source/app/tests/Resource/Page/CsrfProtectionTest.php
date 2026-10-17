<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page;

use Aura\Session\Session;
use BEAR\AppMeta\Meta;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Extension\Router\RouterMatch;
use MyVendor\MyProject\Fake\CsrfEnforcedModule;
use MyVendor\MyProject\Injector;
use MyVendor\MyProject\Module\HtmlModule;
use MyVendor\MyProject\TemplateEngine\QiqErrorPage;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qiq\Template;
use Ray\Di\InjectorInterface;
use Ray\WebFormModule\AbstractForm;
use Ray\WebFormModule\Exception\CsrfViolationException;
use Ray\WebFormModule\FormInterface;
use ReflectionProperty;

use function array_keys;
use function iterator_to_array;
use function str_starts_with;
use function strlen;
use function substr;

/** @psalm-suppress PropertyNotSetInConstructor */
final class CsrfProtectionTest extends TestCase
{
    private const string URI = 'page://self/admin/contact-demo';

    private InjectorInterface $injector;
    private ResourceInterface $resource;

    #[Override]
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $this->injector = Injector::getOverrideInstance('html-app', new CsrfEnforcedModule());
        $this->resource = $this->injector->getInstance(ResourceInterface::class);
    }

    #[Override]
    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    public function testMissingTokenIsRejected(): void
    {
        $this->expectException(CsrfViolationException::class);

        $this->post(['username' => 'koriym', 'mode' => 'Complete']);
    }

    public function testForgedTokenIsRejected(): void
    {
        $this->expectException(CsrfViolationException::class);

        $this->post(['username' => 'koriym', 'mode' => 'Complete', '__csrf_token' => 'forged']);
    }

    public function testValidTokenIsAccepted(): void
    {
        $token = $this->injector->getInstance(Session::class)->getCsrfToken()->getValue();

        $ro = $this->post(['username' => 'koriym', 'mode' => 'Complete', '__csrf_token' => $token]);

        $this->assertSame(303, $ro->code);
    }

    public function testCsrfViolationIsRenderedAsBadRequest(): void
    {
        $page = new QiqErrorPage(
            new CsrfViolationException(),
            'Error',
            new RouterMatch(),
            $this->injector->getInstance(Template::class),
        );

        $this->assertSame(400, $page->code);
        $this->assertSame('Bad Request', $page->body['message'] ?? null);
    }

    /** @return iterable<string, array{string}> */
    public static function formNames(): iterable
    {
        $prefix = FormInterface::class . '-';
        $module = new HtmlModule(new Meta('MyVendor\MyProject', 'html-app'));
        foreach (array_keys($module->getContainer()->getContainer()) as $key) {
            if (! str_starts_with($key, $prefix)) {
                continue;
            }

            $name = substr($key, strlen($prefix));

            yield $name => [$name];
        }
    }

    /** Guards against the data provider silently yielding nothing, which PHPUnit only warns about. */
    public function testFormNamesAreEnumerated(): void
    {
        $this->assertNotEmpty(iterator_to_array(self::formNames()));
    }

    #[DataProvider('formNames')]
    public function testEveryFormHasAntiCsrf(string $name): void
    {
        $form = $this->injector->getInstance(FormInterface::class, $name);

        $this->assertNotNull((new ReflectionProperty(AbstractForm::class, 'antiCsrf'))->getValue($form));
    }

    /**
     * Form validation reads $_POST, while the resource method's #[Input] arguments come from the query.
     *
     * @param array<string, string> $post
     */
    private function post(array $post): ResourceObject
    {
        $_POST = $post;

        return $this->resource->post(self::URI, $post + ['__csrf_token' => '']);
    }
}
