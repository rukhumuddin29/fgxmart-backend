<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
        ['email' => 'rukhumuddin.md@gmail.com'],
        [
            'name' => 'Rukhumuddin',
            'password' => 'Password@123',
        ]
        );

        $admin->assignRole('super-admin');
    }
}
