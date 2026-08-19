<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',

            // News
            'view news', 'create news', 'edit news', 'delete news', 'publish news',

            // Categories
            'view categories', 'create categories', 'edit categories', 'delete categories',

            // Promotions
            'view promotions', 'create promotions', 'edit promotions', 'delete promotions', 'publish promotions',

            // Videos
            'view videos', 'create videos', 'edit videos', 'delete videos', 'publish videos',

            // Podcasts
            'view podcasts', 'create podcasts', 'edit podcasts', 'delete podcasts', 'publish podcasts',

            // Songs / Artists / Charts
            'view songs', 'create songs', 'edit songs', 'delete songs',
            'view artists', 'create artists', 'edit artists', 'delete artists',
            'view charts', 'create charts', 'edit charts', 'delete charts',

            // Radio / Streaming / Programs
            'view schedule', 'create schedule', 'edit schedule', 'delete schedule',
            'view programs', 'create programs', 'edit programs', 'delete programs',
            'view presenters', 'create presenters', 'edit presenters', 'delete presenters',
            'manage streaming',

            // Advertising
            'view banners', 'create banners', 'edit banners', 'delete banners',
            'view advertisers', 'create advertisers', 'edit advertisers', 'delete advertisers',
            'view ad reports',

            // Communication
            'view messages', 'manage messages',
            'view newsletter', 'manage newsletter',

            // Appearance
            'manage appearance',
            'manage homepage',
            'manage menus',
            'manage footer',

            // System
            'view users', 'create users', 'edit users', 'delete users',
            'manage roles',
            'manage settings',
            'manage seo',
            'view logs',
            'manage backups',
            'manage maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'super-admin' => $permissions,
            'admin' => $permissions,
            'editor' => [
                'view dashboard',
                'view news', 'create news', 'edit news', 'delete news', 'publish news',
                'view categories', 'create categories', 'edit categories',
                'view promotions', 'create promotions', 'edit promotions', 'publish promotions',
                'view videos', 'create videos', 'edit videos', 'publish videos',
                'view podcasts', 'create podcasts', 'edit podcasts', 'publish podcasts',
                'view songs', 'create songs', 'edit songs',
                'view artists', 'create artists', 'edit artists',
                'view charts', 'create charts', 'edit charts',
                'view schedule', 'view programs', 'view presenters',
                'view messages', 'manage messages',
                'view newsletter',
            ],
            'journalist' => [
                'view dashboard',
                'view news', 'create news', 'edit news', 'publish news',
                'view categories',
                'view promotions', 'create promotions', 'edit promotions',
            ],
            'commercial' => [
                'view dashboard',
                'view banners', 'create banners', 'edit banners',
                'view advertisers', 'create advertisers', 'edit advertisers',
                'view ad reports',
                'view newsletter', 'manage newsletter',
            ],
            'presenter' => [
                'view dashboard',
                'view schedule', 'view programs', 'view presenters',
                'view news', 'view podcasts', 'view songs', 'view videos',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if ($roleName !== 'super-admin') {
                $role->syncPermissions($rolePermissions);
            } else {
                $role->syncPermissions($permissions);
            }
        }
    }
}