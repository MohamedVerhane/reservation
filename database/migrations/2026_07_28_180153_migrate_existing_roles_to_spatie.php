<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'owner', 'guard_name' => 'web'],
            ['name' => 'guest', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                $role
            );
        }

        $adminRole = DB::table('roles')->where('name', 'admin')->value('id');
        $ownerRole = DB::table('roles')->where('name', 'owner')->value('id');
        $guestRole = DB::table('roles')->where('name', 'guest')->value('id');

        $assignRole = function (string $roleColumnValue, int $roleId) {
            DB::table('users')->where('role', $roleColumnValue)->get()->each(function ($user) use ($roleId) {
                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ]);
            });
        };

        $assignRole('admin', $adminRole);
        $assignRole('owner', $ownerRole);
        $assignRole('guest', $guestRole);
    }

    public function down(): void
    {
        DB::table('model_has_roles')->where('model_type', 'App\Models\User')->delete();
        DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'owner', 'guest'])->delete();
    }
};
