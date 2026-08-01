<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // ─── Permissions ────────────────────────────────────

        $permissionsByGroup = [
            'hotels' => [
                'hotel.view-any',
                'hotel.view',
                'hotel.create',
                'hotel.update',
                'hotel.delete',
                'hotel.restore',
                'hotel.force-delete',
                'hotel.toggle-status',
            ],
            'rooms' => [
                'room.view-any',
                'room.view',
                'room.create',
                'room.update',
                'room.delete',
                'room.toggle-status',
                'room.manage-images',
            ],
            'room-types' => [
                'room-type.view-any',
                'room-type.view',
                'room-type.create',
                'room-type.update',
                'room-type.delete',
                'room-type.toggle-status',
            ],
            'reservations' => [
                'reservation.view-any',
                'reservation.view',
                'reservation.create',
                'reservation.update',
                'reservation.delete',
                'reservation.confirm',
                'reservation.check-in',
                'reservation.check-out',
                'reservation.cancel',
            ],
            'reviews' => [
                'review.view-any',
                'review.view',
                'review.create',
                'review.delete',
                'review.approve',
                'review.reject',
                'review.reply',
                'review.restore',
            ],
            'payments' => [
                'payment.view-any',
                'payment.view',
                'payment.create',
                'payment.update',
                'payment.delete',
            ],
            'galleries' => [
                'gallery.view-any',
                'gallery.view',
                'gallery.create',
                'gallery.update',
                'gallery.delete',
                'gallery.manage-images',
            ],
            'users' => [
                'user.view-any',
                'user.view',
                'user.create',
                'user.update',
                'user.delete',
                'user.restore',
                'user.force-delete',
            ],
            'amenities' => [
                'amenity.view-any',
                'amenity.view',
                'amenity.create',
                'amenity.update',
                'amenity.delete',
                'amenity.toggle-status',
                'amenity.assign-rooms',
            ],
            'dashboard' => [
                'dashboard.view',
                'dashboard.view-reports',
            ],
            'settings' => [
                'settings.view',
                'settings.update',
            ],
        ];

        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, 'web');
            }
        }

        // ─── Roles ──────────────────────────────────────────

        // Super Admin — all permissions
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->givePermissionTo(Permission::all());

        // Admin — most permissions except force-delete & settings
        $admin = Role::findOrCreate('admin', 'web');
        $admin->givePermissionTo([
            'hotel.view-any', 'hotel.view', 'hotel.create', 'hotel.update', 'hotel.delete', 'hotel.restore', 'hotel.toggle-status',
            'room.view-any', 'room.view', 'room.create', 'room.update', 'room.delete', 'room.toggle-status', 'room.manage-images',
            'room-type.view-any', 'room-type.view', 'room-type.create', 'room-type.update', 'room-type.delete', 'room-type.toggle-status',
            'reservation.view-any', 'reservation.view', 'reservation.create', 'reservation.update', 'reservation.delete',
            'reservation.confirm', 'reservation.check-in', 'reservation.check-out', 'reservation.cancel',
            'review.view-any', 'review.view', 'review.delete', 'review.approve', 'review.reject', 'review.reply', 'review.restore',
            'payment.view-any', 'payment.view', 'payment.create', 'payment.update', 'payment.delete',
            'gallery.view-any', 'gallery.view', 'gallery.create', 'gallery.update', 'gallery.delete', 'gallery.manage-images',
            'user.view-any', 'user.view', 'user.create', 'user.update', 'user.delete',
            'amenity.view-any', 'amenity.view', 'amenity.create', 'amenity.update', 'amenity.delete', 'amenity.toggle-status', 'amenity.assign-rooms',
            'dashboard.view', 'dashboard.view-reports',
        ]);

        // Owner — owns specific hotels, manages their content
        $owner = Role::findOrCreate('owner', 'web');
        $owner->givePermissionTo([
            'hotel.view', 'hotel.update',
            'room.view', 'room.update', 'room.manage-images',
            'room-type.view', 'room-type.update',
            'reservation.view', 'reservation.update', 'reservation.cancel',
            'review.view', 'review.reply',
            'gallery.view', 'gallery.update', 'gallery.manage-images',
            'dashboard.view',
        ]);

        // Guest — standard registered user
        $guest = Role::findOrCreate('guest', 'web');
        $guest->givePermissionTo([
            'reservation.create',
            'review.create',
            'dashboard.view',
        ]);
    }
}
