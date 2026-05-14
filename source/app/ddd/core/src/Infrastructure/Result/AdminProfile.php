<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Result;

use AppCore\Domain\AccessControl\Access;
use AppCore\Infrastructure\Entity\AdminEmailEntity;
use AppCore\Infrastructure\Entity\AdminEntity;
use AppCore\Infrastructure\Entity\AdminPermissionEntity;

use function array_filter;
use function array_map;
use function array_values;
use function in_array;

final readonly class AdminProfile
{
    /**
     * @param list<AdminEmailEntity>      $emails
     * @param list<AdminPermissionEntity> $permissions
     */
    public function __construct(
        private AdminEntity $admin,
        private array $emails,
        private array $permissions,
    ) {
    }

    public function adminId(): int
    {
        return $this->admin->id;
    }

    /** @return array{username: string, displayName: string, active: bool} */
    public function account(): array
    {
        return [
            'username' => $this->admin->username,
            'displayName' => $this->admin->displayName,
            'active' => $this->admin->active === 1,
        ];
    }

    /** @return list<AdminEmailEntity> */
    public function emails(): array
    {
        return $this->emails;
    }

    public function primaryEmailAddress(): string|null
    {
        return $this->primaryEmail()?->emailAddress;
    }

    /** @return list<AdminEmailEntity> */
    public function verifiedEmails(): array
    {
        return array_values(array_filter(
            $this->emails,
            static fn (AdminEmailEntity $email): bool => $email->verifiedDate !== null,
        ));
    }

    /** @return list<AdminPermissionEntity> */
    public function permissions(): array
    {
        return $this->permissions;
    }

    /** @return list<string> */
    public function allowedResourceNames(): array
    {
        $resources = [];
        foreach ($this->allowedPermissions() as $permission) {
            if (in_array($permission->resourceName, $resources, true)) {
                continue;
            }

            $resources[] = $permission->resourceName;
        }

        return $resources;
    }

    /** @return list<string> */
    public function allowedPermissionNames(): array
    {
        return array_map(
            static fn (AdminPermissionEntity $permission): string => $permission->permissionName,
            $this->allowedPermissions(),
        );
    }

    private function primaryEmail(): AdminEmailEntity|null
    {
        return $this->verifiedEmails()[0] ?? $this->emails[0] ?? null;
    }

    /** @return list<AdminPermissionEntity> */
    private function allowedPermissions(): array
    {
        return array_values(array_filter(
            $this->permissions,
            static fn (AdminPermissionEntity $permission): bool => $permission->access === Access::Allow->value
                && self::isKnownPermissionName($permission->permissionName),
        ));
    }

    private static function isKnownPermissionName(string $permissionName): bool
    {
        return match ($permissionName) {
            'privilege',
            'read',
            'write' => true,
            default => false,
        };
    }
}
