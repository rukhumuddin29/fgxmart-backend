<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Spices',
            'Dry Fruits',
            'Pulses',
            'Masalas'
        ];

        foreach ($categories as $index => $name) {
            \App\Models\Category::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sorting_order' => ($index + 1) * 10,
                'status' => true
            ]);
        }
    }
}
