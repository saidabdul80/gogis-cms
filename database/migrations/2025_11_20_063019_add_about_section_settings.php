<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add about section settings
        $settings = [
            [
                'key' => 'about_background',
                'value' => 'The Gombe State Geographic Information Systems (GOGIS) is a strategic initiative aimed at developing a comprehensive geospatial data infrastructure for efficient planning, decision-making, and resource management within Gombe State using renown practice.',
                'type' => 'textarea',
            ],
            [
                'key' => 'about_objective',
                'value' => "The primary objective of this project is to establish and enhance the Gombe State Geographic Information Systems (GOGIS), ensuring accurate mapping, spatial data integration, and accessibility for various government departments and stakeholders.\n\n• Develop and implement a state-of-the-art Geographic Information System tailored to the specific needs and requirements of Gombe State.\n• Integrate geospatial data from various sources to create a unified and comprehensive database.\n• Conduct a thorough geospatial data collection exercise, including satellite imagery, topographical data, land use data, and other relevant information.\n• Create accurate and up-to-date digital maps for Gombe State, incorporating layers such as roads, land parcels, water bodies, and administrative boundaries.\n• Provide comprehensive training sessions for government officials and relevant stakeholders on how to effectively utilize the GOGIS platform for their respective functions.\n• Implement robust data management protocols to ensure the integrity, security, and accessibility of geospatial data.\n• Develop a user-friendly interface for data input, retrieval, and analysis.\n• Develop customized geospatial applications tailored to the specific needs of Gombe State government departments, such as urban planning, agriculture, and disaster management.\n• Strengthen the capacity of the Gombe State Ministry responsible for GIS by providing training and knowledge transfer to relevant staff.",
                'type' => 'textarea',
            ],
            [
                'key' => 'about_timeline',
                'value' => "The process for obtaining Right of Occupancy (RofO) is within two weeks, while the process for obtaining Certificate of Occupancy (CofO) is within thirty working days. The consultant/firm should provide a detailed project timeline, including key milestones and deliverables.",
                'type' => 'textarea',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'about_background',
            'about_objective',
            'about_timeline',
        ])->delete();
    }
};
