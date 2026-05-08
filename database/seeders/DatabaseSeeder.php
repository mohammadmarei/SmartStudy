<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'full_name' => 'Test User',
            'username' => 'test.user',
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password'),
            'role_id' => 1,
            'agreed_to_terms' => true,
        ]);
    }
}
