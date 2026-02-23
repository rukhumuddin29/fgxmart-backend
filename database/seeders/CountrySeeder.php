<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'United Arab Emirates', 'code' => 'AE'],
            ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'Pakistan', 'code' => 'PK'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'Russia', 'code' => 'RU'],
            ['name' => 'Brazil', 'code' => 'BR'],
            ['name' => 'South Africa', 'code' => 'ZA'],
            ['name' => 'Turkey', 'code' => 'TR'],
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'Nigeria', 'code' => 'NG'],
            ['name' => 'Bangladesh', 'code' => 'BD'],
            ['name' => 'Indonesia', 'code' => 'ID'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['code' => $country['code']], [
                'name' => $country['name'],
                'status' => true
            ]);
        }
    }
}
