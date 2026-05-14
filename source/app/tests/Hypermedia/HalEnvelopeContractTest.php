<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Hypermedia;

final class HalEnvelopeContractTest extends AbstractWorkflowTestCase
{
    public function testAdminProfileHasChoreographyLinks(): void
    {
        $profile = $this->profile->onGet(1);

        $this->assertRelExists($profile, 'goAdminIndex');
        $this->assertSame(1, $profile->body['id']);
    }
}
