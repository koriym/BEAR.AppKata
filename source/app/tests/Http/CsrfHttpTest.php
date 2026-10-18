<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Http;

use MyVendor\MyProject\Interceptor\CsrfGuard;
use Override;
use PHPUnit\Framework\TestCase;

use function dirname;

/**
 * Drives the real front controller over HTTP, so the session cookie, the hidden field
 * emitted by form() and the guard are exercised together exactly as a browser sees them.
 *
 * Pages that render the admin layout need a database, so the login page is the token source.
 */
final class CsrfHttpTest extends TestCase
{
    private const string HOST = '127.0.0.1:8899';
    private const string CONTACT_DEMO = '/admin/contact-demo';
    private const array CONTACT = ['username' => 'koriym', 'mode' => 'Complete'];

    private static BuiltinServer $server;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::$server = new BuiltinServer(self::HOST, dirname(__DIR__, 2) . '/public/index.php', __DIR__ . '/log/server.log');
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    public function testFormCarriesTokenAndSessionCookieIsHardened(): void
    {
        $response = $this->browser()->get('/admin/login');

        $this->assertSame(200, $response->code);
        $this->assertNotEmpty($response->hiddenField(CsrfGuard::FIELD));

        $cookie = $response->header('Set-Cookie');
        $this->assertIsString($cookie);
        $this->assertStringStartsWith('bear-app=', $cookie);
        $this->assertStringContainsString('HttpOnly', $cookie);
        $this->assertStringContainsString('SameSite=Lax', $cookie);
    }

    public function testTokenFromRenderedFormIsAccepted(): void
    {
        $browser = $this->browser();
        $token = $browser->get('/admin/login')->hiddenField(CsrfGuard::FIELD);
        $this->assertIsString($token);

        $response = $browser->post(self::CONTACT_DEMO, [...self::CONTACT, CsrfGuard::FIELD => $token]);

        $this->assertSame(303, $response->code);
        $this->assertSame('/admin/contact-complete-demo', $response->header('Location'));
    }

    public function testMissingTokenIsRejected(): void
    {
        $browser = $this->browser();
        $browser->get('/admin/login');

        $this->assertSame(400, $browser->post(self::CONTACT_DEMO, self::CONTACT)->code);
    }

    public function testForgedTokenIsRejected(): void
    {
        $browser = $this->browser();
        $browser->get('/admin/login');

        $this->assertSame(400, $browser->post(self::CONTACT_DEMO, [...self::CONTACT, CsrfGuard::FIELD => 'forged'])->code);
    }

    /** The classic attack: a token valid in the attacker's session is replayed against the victim's. */
    public function testTokenFromAnotherSessionIsRejected(): void
    {
        $attacker = $this->browser();
        $token = $attacker->get('/admin/login')->hiddenField(CsrfGuard::FIELD);
        $this->assertIsString($token);

        $victim = $this->browser();
        $victim->get('/admin/login');

        $this->assertSame(400, $victim->post(self::CONTACT_DEMO, [...self::CONTACT, CsrfGuard::FIELD => $token])->code);
    }

    public function testRequestWithoutSessionIsRejected(): void
    {
        $token = $this->browser()->get('/admin/login')->hiddenField(CsrfGuard::FIELD);
        $this->assertIsString($token);

        $this->assertSame(400, $this->browser()->post(self::CONTACT_DEMO, [...self::CONTACT, CsrfGuard::FIELD => $token])->code);
    }

    public function testPageWithoutFormIsGuarded(): void
    {
        $browser = $this->browser();
        $browser->get('/admin/login');

        $this->assertSame(400, $browser->post('/admin/logout', [])->code);
    }

    private function browser(): Browser
    {
        return new Browser('http://' . self::HOST);
    }
}
