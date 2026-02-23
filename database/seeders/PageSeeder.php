<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'position' => 'header',
                'status' => true,
                'content' => [
                    [
                        'type' => 'HeroSection',
                        'data' => [
                            'title' => 'Welcome to FGX Store',
                            'subtitle' => 'Providing premium quality products to our global community since 2020.'
                        ]
                    ],
                    [
                        'type' => 'RichText',
                        'data' => [
                            'html' => '<p>Our mission is to deliver excellence and build trust with every purchase. We source the finest materials and partner with ethical manufacturers to ensure you get the best.</p>'
                        ]
                    ]
                ],
                'meta_title' => 'About FGX Store - Our Story',
                'meta_description' => 'Learn more about FGX Store, our mission, and our values.'
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'position' => 'footer',
                'status' => true,
                'content' => [
                    [
                        'type' => 'HeroSection',
                        'data' => [
                            'title' => 'Privacy Policy',
                            'subtitle' => 'How we handle and protect your personal information.'
                        ]
                    ],
                    [
                        'type' => 'RichText',
                        'data' => [
                            'html' => '<h3>Your Privacy Matters</h3><p>We collect minimal data to provide you with the best shopping experience. Your information is encrypted and never sold to third parties.</p>'
                        ]
                    ]
                ],
                'meta_title' => 'Privacy Policy - FGX Store',
                'meta_description' => 'Read our privacy policy to understand how we protect your data.'
            ]
        ];

        foreach ($pages as $page) {
            \App\Models\Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
