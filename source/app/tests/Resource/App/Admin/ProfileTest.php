<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Admin;

use MyVendor\MyProject\Fake\FakeAdminEmailQuery;
use MyVendor\MyProject\Fake\FakeAdminPermissionQuery;
use MyVendor\MyProject\Fake\FakeAdminQuery;
use PHPUnit\Framework\TestCase;

use function array_key_exists;

final class ProfileTest extends TestCase
{
    private Profile $profile;

    protected function setUp(): void
    {
        $this->profile = new Profile(
            new FakeAdminQuery(),
            new FakeAdminEmailQuery(),
            new FakeAdminPermissionQuery(),
        );
    }

    public function testOnGetReturnsAdminReadContract(): void
    {
        $ro = $this->profile->onGet(1);

        $this->assertSame(200, $ro->code);
        $this->assertSame(1, $ro->body['id']);
        $this->assertSame('admin', $ro->body['username']);
        $this->assertSame('primary@example.com', $ro->body['primaryEmail']);
        $this->assertSame(1, $ro->body['verifiedEmailCount']);
        $this->assertSame(['admin'], $ro->body['allowedResources']);
        $this->assertFalse(array_key_exists('password', $ro->body));
    }

    public function testOnGetMissingReturns404(): void
    {
        $ro = $this->profile->onGet(999);

        $this->assertSame(404, $ro->code);
        $this->assertSame('Admin not found', $ro->body['message']);
        $this->assertSame(999, $ro->body['id']);
    }
}
