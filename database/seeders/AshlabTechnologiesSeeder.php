<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Carousel;
use App\Models\News;
use App\Models\Page;
use Illuminate\Database\Seeder;

class AshlabTechnologiesSeeder extends Seeder
{
    /**
     * Run the database seeder for Ashlab Technologies Services.
     * This seeder populates all website content including settings, carousel, news, and pages.
     */
    public function run(): void
    {
        // ============================================
        // SETTINGS
        // ============================================
        $settings = [
            // Brand Colors - Professional tech company colors
            ['key' => 'primary_color', 'value' => '#0066cc'],
            ['key' => 'secondary_color', 'value' => '#00cc66'],
            ['key' => 'accent_color', 'value' => '#0052a3'],

            // About Us
            ['key' => 'about_us', 'value' => 'Ashlab Technologies Services is a leading technology solutions provider registered under the Companies and Allied Matters Act 2020. We specialize in software development, web design, computer repairs, supply, general training, and consultancy services. Our mission is to deliver innovative technology solutions that empower businesses and individuals to achieve their goals.'],

            // Mandate/Services
            ['key' => 'mandate', 'value' => json_encode([
                'Software Development',
                'Web Design & Development',
                'Computer Repairs & Maintenance',
                'IT Equipment Supply',
                'Technology Training',
                'IT Consultancy Services'
            ])],

            // Key Functions/Service Details
            ['key' => 'key_functions', 'value' => json_encode([
                'Software Development: Custom software solutions tailored to your business needs, from web applications to mobile apps and enterprise systems.',
                'Web Design: Modern, responsive, and user-friendly websites that help your business stand out online and engage customers effectively.',
                'Computer Repairs: Professional diagnosis and repair services for all types of computer hardware and software issues with quick turnaround times.',
                'IT Equipment Supply: Sourcing and supplying quality computers, laptops, servers, networking equipment, and other IT hardware at competitive prices.',
                'Technology Training: Comprehensive training programs in software development, web design, computer literacy, and other IT skills for individuals and organizations.',
                'IT Consultancy: Expert advice on technology strategy, digital transformation, system integration, and IT infrastructure planning to optimize your business operations.'
            ])],

            // Director/CEO Information
            ['key' => 'dg_name', 'value' => 'Chief Executive Officer'],
            ['key' => 'dg_title', 'value' => 'CEO, Ashlab Technologies Services'],
            ['key' => 'dg_bio', 'value' => 'Our leadership team brings years of experience in technology solutions, committed to delivering excellence and innovation in every project we undertake.'],
            ['key' => 'dg_image', 'value' => '/storage/images/ceo.jpeg'],

            // Contact Information
            ['key' => 'contact_email', 'value' => 'info@ashlabtech.com'],
            ['key' => 'contact_phone', 'value' => '+234 803 456 7890'],
            ['key' => 'address', 'value' => 'House No 1 Opposite Sir Block Industry Gbeganu, Minna, Niger State, Nigeria'],

            // Social Media
            ['key' => 'fb_link', 'value' => 'https://www.facebook.com/ashlabtech'],
            ['key' => 'instagram_link', 'value' => 'https://www.instagram.com/ashlabtech'],
            ['key' => 'twitter_link', 'value' => 'https://twitter.com/ashlabtech'],
            ['key' => 'linkedin_link', 'value' => 'https://www.linkedin.com/company/ashlabtech'],

            // Site Information
            ['key' => 'site_name', 'value' => 'Ashlab Technologies Services'],
            ['key' => 'site_tagline', 'value' => 'Innovative Technology Solutions for Your Business'],
            ['key' => 'site_description', 'value' => 'Ashlab Technologies Services provides comprehensive IT solutions including software development, web design, computer repairs, equipment supply, training, and consultancy services in Minna, Niger State, Nigeria.'],
            ['key' => 'logo', 'value' => '/storage/images/logo.png'],
            ['key' => 'favicon', 'value' => '/favicon.ico'],

            // Footer
            ['key' => 'footer_text', 'value' => '© 2025 Ashlab Technologies Services. All rights reserved.'],
            ['key' => 'footer_links', 'value' => json_encode([
                ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
                ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
                ['title' => 'Contact Us', 'url' => '/contact']
            ])],

            // About Section - Background
            ['key' => 'about_background', 'value' => 'Ashlab Technologies Services was established with a vision to bridge the technology gap in Niger State and provide world-class IT solutions to businesses and individuals. Registered under the Companies and Allied Matters Act 2020, we have grown from a small startup to a trusted technology partner for numerous clients across Nigeria. Our journey began with a passion for innovation and a commitment to excellence, and today we stand as a testament to what dedication and expertise can achieve in the rapidly evolving technology landscape.'],

            // About Section - Objective
            ['key' => 'about_objective', 'value' => 'Our primary objective is to empower businesses and individuals through innovative technology solutions. We aim to: deliver custom software applications that solve real business problems; create stunning web designs that enhance online presence and drive customer engagement; provide reliable computer repair and maintenance services to keep systems running smoothly; supply quality IT equipment at competitive prices; offer comprehensive training programs that build tech capacity in our community; and provide expert consultancy services that guide strategic technology decisions. Through these services, we strive to be the catalyst for digital transformation in Niger State and beyond.'],

            // About Section - Timeline
            ['key' => 'about_timeline', 'value' => json_encode([
                [
                    'year' => '2020',
                    'title' => 'Company Founded',
                    'description' => 'Ashlab Technologies Services was officially registered under the Companies and Allied Matters Act 2020, marking the beginning of our journey to provide innovative technology solutions.'
                ],
                [
                    'year' => '2021',
                    'title' => 'Service Expansion',
                    'description' => 'Expanded our service offerings to include comprehensive web design, software development, and IT training programs, serving over 50 clients in our first year.'
                ],
                [
                    'year' => '2022',
                    'title' => 'Training Center Launch',
                    'description' => 'Opened our dedicated IT training center in Minna, providing hands-on technology education to students and professionals, training over 200 individuals in various IT skills.'
                ],
                [
                    'year' => '2023',
                    'title' => 'Enterprise Solutions',
                    'description' => 'Launched enterprise-level software solutions and consultancy services, partnering with government agencies and large organizations to deliver complex IT projects.'
                ],
                [
                    'year' => '2024',
                    'title' => 'Regional Recognition',
                    'description' => 'Achieved recognition as one of the leading technology service providers in Niger State, with a growing portfolio of successful projects and satisfied clients.'
                ],
                [
                    'year' => '2025',
                    'title' => 'Innovation & Growth',
                    'description' => 'Continuing to innovate and expand our services, embracing emerging technologies like AI and cloud computing to deliver cutting-edge solutions to our clients.'
                ]
            ])],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        // ============================================
        // CAROUSEL SLIDES
        // ============================================
        $carousels = [
            [
                'title' => 'Welcome to Ashlab Technologies',
                'description' => 'Your trusted partner for innovative technology solutions - Software Development, Web Design, and IT Services',
                'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&h=600&fit=crop',
                'button_text' => 'Our Services',
                'button_link' => '/about',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Custom Software Development',
                'description' => 'Transform your business with tailored software solutions built with cutting-edge technologies',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1920&h=600&fit=crop',
                'button_text' => 'Learn More',
                'button_link' => '/contact',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Professional Web Design',
                'description' => 'Beautiful, responsive websites that engage your customers and grow your business',
                'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=1920&h=600&fit=crop',
                'button_text' => 'Get Started',
                'button_link' => '/contact',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'IT Training & Consultancy',
                'description' => 'Empower your team with expert training and strategic IT consultancy services',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=600&fit=crop',
                'button_text' => 'Contact Us',
                'button_link' => '/contact',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($carousels as $carousel) {
            Carousel::updateOrCreate(
                ['title' => $carousel['title']],
                $carousel
            );
        }

        // ============================================
        // NEWS ARTICLES
        // ============================================
        $newsArticles = [
            [
                'title' => 'Ashlab Technologies Launches New Software Development Services',
                'slug' => 'ashlab-launches-software-development-services',
                'content' => '<p>Ashlab Technologies Services is excited to announce the expansion of our software development services to meet the growing demand for custom technology solutions in Niger State and beyond.</p>

                <p>Our team of experienced developers specializes in creating tailored software applications that address specific business challenges. From web applications to mobile apps and enterprise systems, we deliver solutions that drive efficiency and growth.</p>

                <p>"We are committed to helping businesses leverage technology to achieve their goals," said our CEO. "Our new software development services represent our dedication to innovation and excellence in the IT industry."</p>

                <p>Services include:</p>
                <ul>
                    <li>Custom Web Application Development</li>
                    <li>Mobile App Development (iOS & Android)</li>
                    <li>Enterprise Software Solutions</li>
                    <li>Database Design and Management</li>
                    <li>API Development and Integration</li>
                    <li>Software Maintenance and Support</li>
                </ul>

                <p>Contact us today to discuss your software development needs.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=400&fit=crop',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Professional Web Design Services Now Available',
                'slug' => 'professional-web-design-services-available',
                'content' => '<p>In today\'s digital age, having a professional online presence is crucial for business success. Ashlab Technologies Services is proud to offer comprehensive web design and development services to help businesses establish and enhance their digital footprint.</p>

                <p>Our web design services focus on creating modern, responsive, and user-friendly websites that not only look great but also deliver results. We understand that your website is often the first point of contact with potential customers, and we ensure it makes a lasting impression.</p>

                <p><strong>What We Offer:</strong></p>
                <ul>
                    <li>Responsive Website Design</li>
                    <li>E-commerce Solutions</li>
                    <li>Content Management Systems (CMS)</li>
                    <li>Website Redesign and Modernization</li>
                    <li>SEO Optimization</li>
                    <li>Website Hosting and Maintenance</li>
                </ul>

                <p>Whether you\'re a small business looking for your first website or an established company seeking to refresh your online presence, we have the expertise to bring your vision to life.</p>

                <p>Get in touch with us to start your web design project today!</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1547658719-da2b51169166?w=800&h=400&fit=crop',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Computer Repair and Maintenance Services - Fast and Reliable',
                'slug' => 'computer-repair-maintenance-services',
                'content' => '<p>Is your computer running slow? Experiencing hardware issues? Ashlab Technologies Services provides professional computer repair and maintenance services to keep your systems running smoothly.</p>

                <p>Our experienced technicians can diagnose and fix a wide range of computer problems, from hardware failures to software issues. We offer both on-site and in-shop repair services for your convenience.</p>

                <p><strong>Our Repair Services Include:</strong></p>
                <ul>
                    <li>Hardware Diagnostics and Repair</li>
                    <li>Operating System Installation and Troubleshooting</li>
                    <li>Virus and Malware Removal</li>
                    <li>Data Recovery Services</li>
                    <li>Network Setup and Configuration</li>
                    <li>Laptop Screen Replacement</li>
                    <li>Preventive Maintenance</li>
                </ul>

                <p>We pride ourselves on quick turnaround times and competitive pricing. Most repairs are completed within 24-48 hours.</p>

                <p><strong>Preventive Maintenance Plans:</strong> Keep your computers running at peak performance with our maintenance plans, including regular system updates, cleaning, and optimization.</p>

                <p>Contact us today to schedule a repair or maintenance service.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&h=400&fit=crop',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'IT Training Programs - Empowering the Next Generation of Tech Professionals',
                'slug' => 'it-training-programs-available',
                'content' => '<p>Ashlab Technologies Services is committed to building tech capacity in our community through comprehensive IT training programs. We offer courses designed for beginners, intermediate learners, and professionals looking to upgrade their skills.</p>

                <p>Our training programs are hands-on and practical, ensuring that participants gain real-world skills they can apply immediately in their careers or businesses.</p>

                <p><strong>Available Training Programs:</strong></p>
                <ul>
                    <li>Web Development (HTML, CSS, JavaScript, PHP, Laravel)</li>
                    <li>Mobile App Development</li>
                    <li>Computer Literacy and Microsoft Office</li>
                    <li>Graphic Design (Adobe Photoshop, Illustrator)</li>
                    <li>Database Management</li>
                    <li>Computer Hardware and Networking</li>
                    <li>Digital Marketing</li>
                </ul>

                <p>We offer flexible training schedules including weekend and evening classes to accommodate working professionals and students.</p>

                <p><strong>Corporate Training:</strong> We also provide customized training programs for organizations looking to upskill their workforce in specific technology areas.</p>

                <p>Enroll today and take the first step towards a rewarding career in technology!</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&h=400&fit=crop',
                'published_at' => now()->subDays(30),
            ],
        ];

        foreach ($newsArticles as $article) {
            News::updateOrCreate(
                ['slug' => $article['slug']],
                $article
            );
        }

        // ============================================
        // PAGES
        // ============================================
        $pages = [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h2>Privacy Policy</h2>

                <p><strong>Effective Date:</strong> January 1, 2025</p>

                <p>Ashlab Technologies Services ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.</p>

                <h3>Information We Collect</h3>
                <p>We may collect personal information that you voluntarily provide to us when you:</p>
                <ul>
                    <li>Contact us through our website or email</li>
                    <li>Request a quote or consultation</li>
                    <li>Register for training programs</li>
                    <li>Subscribe to our newsletter</li>
                </ul>

                <h3>How We Use Your Information</h3>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Respond to your inquiries and requests</li>
                    <li>Send you updates and marketing communications (with your consent)</li>
                    <li>Improve our services and customer experience</li>
                </ul>

                <h3>Data Security</h3>
                <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                <h3>Contact Us</h3>
                <p>If you have questions about this Privacy Policy, please contact us at info@ashlabtech.com</p>',
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'content' => '<h2>Terms of Service</h2>

                <p><strong>Effective Date:</strong> January 1, 2025</p>

                <p>Welcome to Ashlab Technologies Services. By accessing our website or using our services, you agree to be bound by these Terms of Service.</p>

                <h3>Services</h3>
                <p>Ashlab Technologies Services provides software development, web design, computer repairs, IT equipment supply, technology training, and consultancy services as described on our website.</p>

                <h3>Service Terms</h3>
                <ul>
                    <li>All services are subject to availability and acceptance by Ashlab Technologies Services</li>
                    <li>Pricing and service details are subject to change without notice</li>
                    <li>Payment terms will be specified in individual service agreements</li>
                    <li>We reserve the right to refuse service to anyone for any reason</li>
                </ul>

                <h3>Intellectual Property</h3>
                <p>Unless otherwise specified in a service agreement, all intellectual property rights in work products created by Ashlab Technologies Services shall be governed by the terms of the specific service contract.</p>

                <h3>Limitation of Liability</h3>
                <p>Ashlab Technologies Services shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services.</p>

                <h3>Governing Law</h3>
                <p>These Terms shall be governed by and construed in accordance with the laws of the Federal Republic of Nigeria.</p>

                <h3>Contact Information</h3>
                <p>For questions about these Terms, please contact us at info@ashlabtech.com</p>',
            ],
            [
                'slug' => 'about',
                'title' => 'About Ashlab Technologies Services',
                'content' => '<h2>About Ashlab Technologies Services</h2>

                <p>Ashlab Technologies Services is a registered business under the Companies and Allied Matters Act 2020, dedicated to providing comprehensive technology solutions to businesses and individuals in Niger State and across Nigeria.</p>

                <h3>Our Mission</h3>
                <p>To deliver innovative, reliable, and cost-effective technology solutions that empower our clients to achieve their business objectives and stay competitive in the digital age.</p>

                <h3>Our Vision</h3>
                <p>To be the leading technology solutions provider in Niger State, recognized for excellence, innovation, and customer satisfaction.</p>

                <h3>Our Services</h3>
                <p>We offer a comprehensive range of IT services including:</p>
                <ul>
                    <li><strong>Software Development:</strong> Custom software solutions tailored to your specific business needs</li>
                    <li><strong>Web Design:</strong> Modern, responsive websites that drive results</li>
                    <li><strong>Computer Repairs:</strong> Professional diagnosis and repair of all computer issues</li>
                    <li><strong>IT Equipment Supply:</strong> Quality computers, laptops, and IT hardware</li>
                    <li><strong>Technology Training:</strong> Comprehensive training programs in various IT skills</li>
                    <li><strong>IT Consultancy:</strong> Expert advice on technology strategy and implementation</li>
                </ul>

                <h3>Why Choose Us?</h3>
                <ul>
                    <li>Experienced and certified professionals</li>
                    <li>Customer-focused approach</li>
                    <li>Competitive pricing</li>
                    <li>Quality assurance</li>
                    <li>Timely delivery</li>
                    <li>Ongoing support and maintenance</li>
                </ul>

                <h3>Our Location</h3>
                <p>We are conveniently located at House No 1 Opposite Sir Block Industry Gbeganu, Minna, Niger State, Nigeria.</p>

                <h3>Get in Touch</h3>
                <p>Ready to transform your business with technology? Contact us today to discuss your project or schedule a consultation.</p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        $this->command->info('✓ Ashlab Technologies website content seeded successfully!');
        $this->command->info('✓ Settings: ' . count($settings) . ' items');
        $this->command->info('✓ Carousel Slides: ' . count($carousels) . ' items');
        $this->command->info('✓ News Articles: ' . count($newsArticles) . ' items');
        $this->command->info('✓ Pages: ' . count($pages) . ' items');
    }
}
