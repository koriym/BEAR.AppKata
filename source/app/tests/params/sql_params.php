<?php

declare(strict_types=1);

return [
    'admins/admin_selection_list.sql' => ['active' => 1],
    'admins/admin_update.sql' => [
        'id' => 1,
        'username' => 'admin',
        'displayName' => 'Admin User',
        'active' => 1,
        'updatedDate' => '2026-01-02 00:00:00',
    ],
    'admins/admin_delete_delete.sql' => [
        'adminId' => 1,
        'deletedDate' => '2026-01-02 00:00:00',
    ],
];
