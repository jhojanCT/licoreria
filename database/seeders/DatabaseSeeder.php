<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    private const DEFAULT_ADMIN_NAME = 'Administrador';
    private const DEFAULT_ADMIN_EMAIL = 'admin@licoreria.test';
    private const DEFAULT_ADMIN_PASSWORD = 'password';
    private const DEFAULT_ADMIN_ROLE = 'administrador';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => config('seeder.admin.email', self::DEFAULT_ADMIN_EMAIL)],
            [
                'name' => config('seeder.admin.name', self::DEFAULT_ADMIN_NAME),
                'password' => Hash::make(
                    config('seeder.admin.password', self::DEFAULT_ADMIN_PASSWORD)
                ),
            ],
        );

        $admin->syncRoles([config('seeder.admin.role', self::DEFAULT_ADMIN_ROLE)]);
    }
}
