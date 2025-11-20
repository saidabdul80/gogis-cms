<?php

namespace Database\Seeders;

use App\Models\Carousel;
use Illuminate\Database\Seeder;

class CarouselSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $carousels = [
            [
                'title' => 'Welcome to GOGIS',
                'description' => 'Gombe Geographic Information System - Your gateway to efficient land administration and revenue management',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&h=600&fit=crop',
                'button_text' => 'Learn More',
                'button_link' => '/about',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Efficient Invoice Management',
                'description' => 'Generate, track, and manage invoices with ease. Streamline your revenue collection process',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&h=600&fit=crop',
                'button_text' => 'Get Started',
                'button_link' => '/contact',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Secure Payment Processing',
                'description' => 'Process payments securely and efficiently. Multiple payment options available for your convenience',
                'image' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1920&h=600&fit=crop',
                'button_text' => 'Contact Us',
                'button_link' => '/contact',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($carousels as $carousel) {
            Carousel::create($carousel);
        }
    }
}

