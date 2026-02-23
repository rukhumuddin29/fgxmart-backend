<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'title' => 'How can I track my recent order status and estimated delivery time accurately?',
                'description' => 'You can track your order by visiting the My Account dashboard and selecting the Orders tab. Here, you will find real-time updates regarding your shipment status and a detailed tracker for delivery.',
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'title' => 'What is your detailed return policy for items that arrive damaged or incorrect?',
                'description' => 'We offer a comprehensive 30-day return policy for all products. If your item arrives damaged or is not what you ordered, please contact our support team immediately to initiate a free replacement.',
                'sort_order' => 2,
                'status' => true,
            ],
            [
                'title' => 'Do you provide international shipping services to customers living outside the main regions?',
                'description' => 'Yes, we provide premium international shipping to over fifty countries worldwide. Shipping costs and delivery timelines vary based on your specific location and the shipping method selected during the checkout process.',
                'sort_order' => 3,
                'status' => true,
            ],
            [
                'title' => 'Are there any specific membership benefits for recurring customers who shop frequently here?',
                'description' => 'Our loyal customers enjoy exclusive benefits through our Rewards Program, including early access to seasonal sales, special birthday discounts, and points for every purchase that can be redeemed for future shopping credit.',
                'sort_order' => 4,
                'status' => true,
            ],
            [
                'title' => 'How secure is my personal payment information when I make a purchase today?',
                'description' => 'We prioritize your security by using industry-leading encryption and secure payment gateways. Your sensitive payment details are never stored on our servers, ensuring a completely safe and protected shopping experience for everyone.',
                'sort_order' => 5,
                'status' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            \App\Models\Faq::create($faq);
        }
    }
}
