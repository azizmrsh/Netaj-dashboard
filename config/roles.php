<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roles and Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains custom configurations for managing roles and permissions
    | in the admin panel
    |
    */

    'default_roles' => [
        'super_admin' => [
            'name' => 'Super Admin',
            'description' => 'Has all permissions in the system',
            'color' => 'danger',
            'icon' => 'heroicon-o-shield-exclamation',
        ],
        'admin' => [
            'name' => 'Admin',
            'description' => 'Has permissions to manage users and roles',
            'color' => 'warning',
            'icon' => 'heroicon-o-shield-check',
        ],
        'editor' => [
            'name' => 'Editor',
            'description' => 'Has permissions to edit and create',
            'color' => 'info',
            'icon' => 'heroicon-o-pencil-square',
        ],
        'viewer' => [
            'name' => 'Viewer',
            'description' => 'Has view-only permissions',
            'color' => 'success',
            'icon' => 'heroicon-o-eye',
        ],
    ],

    'permission_groups' => [
        'user' => [
            'name' => 'User Management',
            'description' => 'Permissions for managing users',
            'icon' => 'heroicon-o-users',
        ],
        'role' => [
            'name' => 'Role Management',
            'description' => 'Permissions for managing roles and permissions',
            'icon' => 'heroicon-o-shield-check',
        ],
        'page' => [
            'name' => 'Page Management',
            'description' => 'Permissions for accessing pages',
            'icon' => 'heroicon-o-document',
        ],
        'widget' => [
            'name' => 'Widget Management',
            'description' => 'Permissions for displaying widgets',
            'icon' => 'heroicon-o-squares-2x2',
        ],
    ],

    'permission_actions' => [
        'view_any' => [
            'name' => 'View List',
            'description' => 'Ability to view list of items',
            'color' => 'info',
        ],
        'view' => [
            'name' => 'View Details',
            'description' => 'Ability to view details of a single item',
            'color' => 'info',
        ],
        'create' => [
            'name' => 'Create',
            'description' => 'Ability to create new items',
            'color' => 'success',
        ],
        'update' => [
            'name' => 'Update',
            'description' => 'Ability to update existing items',
            'color' => 'warning',
        ],
        'delete' => [
            'name' => 'Delete',
            'description' => 'Ability to delete a single item',
            'color' => 'danger',
        ],
        'delete_any' => [
            'name' => 'Bulk Delete',
            'description' => 'Ability to delete multiple items',
            'color' => 'danger',
        ],
        'restore' => [
            'name' => 'Restore',
            'description' => 'Ability to restore deleted items',
            'color' => 'success',
        ],
        'restore_any' => [
            'name' => 'Bulk Restore',
            'description' => 'Ability to restore multiple deleted items',
            'color' => 'success',
        ],
        'replicate' => [
            'name' => 'Replicate',
            'description' => 'Ability to replicate items',
            'color' => 'info',
        ],
        'reorder' => [
            'name' => 'Reorder',
            'description' => 'Ability to reorder items',
            'color' => 'warning',
        ],
        'force_delete' => [
            'name' => 'Force Delete',
            'description' => 'Ability to permanently delete items',
            'color' => 'danger',
        ],
        'force_delete_any' => [
            'name' => 'Bulk Force Delete',
            'description' => 'Ability to permanently delete multiple items',
            'color' => 'danger',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the security system behavior
    |
    */

    'security' => [
        'require_confirmation_for_dangerous_actions' => true,
        'log_role_changes' => true,
        'log_permission_changes' => true,
        'prevent_super_admin_deletion' => true,
        'minimum_password_length' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Interface Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the display of roles and permissions in the UI
    |
    */

    'ui' => [
        'show_permission_descriptions' => true,
        'group_permissions_by_resource' => true,
        'show_role_statistics' => true,
        'enable_bulk_actions' => true,
        'items_per_page' => 25,
    ],
];