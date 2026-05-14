<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\App\Admin;

use AppCore\Infrastructure\Entity\AdminEmailEntity;
use AppCore\Infrastructure\Entity\AdminPermissionEntity;
use AppCore\Infrastructure\Query\AdminEmailQueryInterface;
use AppCore\Infrastructure\Query\AdminPermissionQueryInterface;
use AppCore\Infrastructure\Query\AdminQueryInterface;
use AppCore\Infrastructure\Result\AdminProfile;
use BEAR\ApiDoc\Annotation\Alps;
use BEAR\Resource\Annotation\JsonSchema;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;

use function array_map;
use function count;

#[Alps('AdminProfile')]
class Profile extends ResourceObject
{
    public function __construct(
        private readonly AdminQueryInterface $admin,
        private readonly AdminEmailQueryInterface $emails,
        private readonly AdminPermissionQueryInterface $permissions,
    ) {
    }

    #[Alps('goAdminProfile')]
    #[Link(rel: 'goAdminIndex', href: 'app://self/admin/index')]
    #[JsonSchema('admin_profile.json')]
    public function onGet(int $id): static
    {
        $admin = $this->admin->item($id);
        if ($admin === null) {
            $this->code = Code::NOT_FOUND;
            $this->body = ['message' => 'Admin not found', 'id' => $id];

            return $this;
        }

        $profile = new AdminProfile(
            $admin,
            $this->emails->list($id),
            $this->permissions->list($id),
        );
        $account = $profile->account();

        $this->body = [
            'id' => $profile->adminId(),
            'username' => $account['username'],
            'displayName' => $account['displayName'],
            'active' => $account['active'],
            'primaryEmail' => $profile->primaryEmailAddress(),
            'verifiedEmailCount' => count($profile->verifiedEmails()),
            'emails' => array_map($this->email(...), $profile->emails()),
            'permissions' => array_map($this->permission(...), $profile->permissions()),
            'allowedResources' => $profile->allowedResourceNames(),
            'allowedPermissions' => $profile->allowedPermissionNames(),
        ];

        return $this;
    }

    /** @return array{id: int, emailAddress: string, verified: bool} */
    private function email(AdminEmailEntity $email): array
    {
        return [
            'id' => $email->id,
            'emailAddress' => $email->emailAddress,
            'verified' => $email->verifiedDate !== null,
        ];
    }

    /** @return array{id: int, access: string, resourceName: string, permissionName: string} */
    private function permission(AdminPermissionEntity $permission): array
    {
        return [
            'id' => $permission->id,
            'access' => $permission->access,
            'resourceName' => $permission->resourceName,
            'permissionName' => $permission->permissionName,
        ];
    }
}
