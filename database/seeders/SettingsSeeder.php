<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Brand Colors
            ['key' => 'primary_color', 'value' => '#1c6434'],
            ['key' => 'secondary_color', 'value' => '#fecd07'],
            ['key' => 'accent_color', 'value' => '#c39913'],

            // About Us
            ['key' => 'about_us', 'value' => 'The Gombe Geographic Information System (GOGIS) is the agency responsible for digitalized land administration, cadastral mapping, and land registration in Gombe State, Nigeria. It was established to modernize land governance, resolve land conflicts, and improve revenue generation for the state government.'],

            // Mandate
            ['key' => 'mandate', 'value' => json_encode([
                'Land Administration',
                'Cadastral Mapping',
                'Remote Sensing',
                'Geographic Information Systems (GIS)'
            ])],

            // Key Functions
            ['key' => 'key_functions', 'value' => json_encode([
                'Land Administration: Managing the process of determining, recording, and disseminating information about land acquisition, ownership, value, and management policies.',
                'Conflict Resolution: Putting an end to land conflicts often caused by unauthorized transactions, thereby ensuring peace and economic stability.',
                'Digitalization and Mapping: Transitioning from manual archiving of land documents to a fully automated and secure digital system, including the digitization of manual land files and layouts.',
                'Title Registration and Issuance: Processing and issuing formal land titles, such as Rights of Occupancy (RofO) and Certificates of Occupancy (CofO), with processes aiming for completion within specific timelines (two weeks for RofO, thirty working days for CofO).',
                'Revenue Generation: Contributing significantly to the state\'s Internally Generated Revenue (IGR) through efficient and transparent land transactions and property taxation.',
                'Urban Planning and Development: Supporting planned, sustainable urban development initiatives such as the Gombe Capital Special Development Zone and the Shehu Abubakar District.',
                'Data Management: Ensuring the integrity, security, and accessibility of geospatial data for use by various government departments and stakeholders.'
            ])],

            // Director General Information
            ['key' => 'dg_name', 'value' => 'Dr. Kabiru Usman Hassan'],
            ['key' => 'dg_title', 'value' => 'Director General, GOGIS'],
            ['key' => 'dg_bio', 'value' => 'The current director general, was appointed in 2020 by the governor of Gombe State, His Excellency Alh Muhammadu Inuwa Yahaya, CON. Dr. Hassan was also reappointed to continue spearheading the mandate of the agency on July 14, 2023.'],
            ['key' => 'dg_image', 'value' => '/storage/images/dg.jpeg'],

            // Contact Information
            ['key' => 'contact_email', 'value' => 'info@gogis.gov.gm.ng'],
            ['key' => 'contact_phone', 'value' => '+234 XXX XXX XXXX'],
            ['key' => 'address', 'value' => 'G.R.A Drive, Adjacent Treasury House Gombe, Gombe State'],

            // Social Media
            ['key' => 'fb_link', 'value' => 'https://www.facebook.com/GOGISOFFICIAL'],
            ['key' => 'instagram_link', 'value' => 'https://www.instagram.com/officialgogis'],
            ['key' => 'twitter_link', 'value' => ''],
            ['key' => 'linkedin_link', 'value' => ''],

            // Site Information
            ['key' => 'site_name', 'value' => 'Gombe Geographic Information System'],
            ['key' => 'site_tagline', 'value' => 'Modernizing Land Administration in Gombe State'],
            ['key' => 'site_description', 'value' => 'GOGIS is the agency responsible for digitalized land administration, cadastral mapping, and land registration in Gombe State, Nigeria.'],
            ['key' => 'logo', 'value' => '/storage/images/logo.jpeg'],
            ['key' => 'favicon', 'value' => '/favicon.ico'],

            // Footer
            ['key' => 'footer_text', 'value' => '© 2025 Gombe Geographic Information System (GOGIS). All rights reserved.'],
            ['key' => 'footer_links', 'value' => json_encode([
                ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
                ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
                ['title' => 'Contact Us', 'url' => '/contact']
            ])],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
