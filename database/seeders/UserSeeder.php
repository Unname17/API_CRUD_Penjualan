<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => Str::ulid(),
            'name' => 'Admin',
            'email' => 'admin@ifump.net',
            'username' => 'admin',
            'password' => Hash::make('password')
        ]);
    }
}
