<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Hypermedia;

use BEAR\Resource\ResourceObject;
use PHPUnit\Framework\Attributes\Depends;

final class AdminReadsProfileTest extends AbstractWorkflowTestCase
{
    public function testOpensAdminIndex(): ResourceObject
    {
        $index = $this->index->onGet();

        $this->assertSame(200, $index->code);

        return $index;
    }

    #[Depends('testOpensAdminIndex')]
    public function testFollowsProfile(ResourceObject $index): ResourceObject
    {
        return $this->follow($index, 'goAdminProfile', ['id' => $index->body['defaultAdminId']]);
    }

    #[Depends('testFollowsProfile')]
    public function testReturnsTheSameAdmin(ResourceObject $profile): void
    {
        $this->assertSame(1, $profile->body['id']);
        $this->assertSame('primary@example.com', $profile->body['primaryEmail']);
    }
}
