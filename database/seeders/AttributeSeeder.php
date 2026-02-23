<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Weight',
                'values' => ['100gms', '250gms', '500gms', '1kg']
            ],
            [
                'name' => 'Packaging',
                'values' => ['Plastic Bag', 'Glass Jar', 'Carton Box']
            ],
            [
                'name' => 'Brand',
                'values' => ['FGX Premium', 'Local Organic']
            ]
        ];

        foreach ($attributes as $index => $attrData) {
            $attribute = Attribute::create([
                'name' => $attrData['name'],
                'slug' => Str::slug($attrData['name']),
                'sorting_order' => ($index + 1) * 10,
                'status' => true
            ]);

            foreach ($attrData['values'] as $vIndex => $value) {
                $attribute->values()->create([
                    'value' => $value,
                    'slug' => Str::slug($value),
                    'sorting_order' => ($vIndex + 1) * 10
                ]);
            }
        }
    }
}
