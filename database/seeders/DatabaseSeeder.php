<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Language;
use App\Models\EmailTemplate;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PlanSeeder::class,
            LegalPagesSeeder::class,
            LanguageSeeder::class,
            EmailTemplateSeeder::class,
            DS_LandingPageComponentSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();
        $en = Language::where('code', 'en')->first();


        if (!User::where('email', 'admin@dropsaas.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@dropsaas.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'language_id' => $en?->id,
                'is_active' => true,
            ]);
        }

        if (!User::where('email', 'user@dropsaas.com')->exists()) {
            User::create([
                'name' => 'User',
                'email' => 'user@dropsaas.com',
                'password' => Hash::make('password'),
                'role_id' => $userRole?->id,
                'language_id' => $en?->id,
                'is_active' => true,
            ]);
        }
    }
}
