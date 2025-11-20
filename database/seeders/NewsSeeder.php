<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'title' => 'INSIDEGOGIS: DG Hosts GOSUPDA Team as Agencies Move to Implement Executive Order 007 at Shehu Abubakar District',
            'slug' => 'dg-hosts-gosupda-team-executive-order-007',
            'content' => '<p>The Director-General of Gombe Geographic Information Systems (GOGIS), Dr Kabiru Usman Hassan, alongside technical staff from the agency earlier hosted a team from the Gombe State Urban Planning and Development Authority (GOSUPDA) at the agency\'s headquarters for a strategic meeting ahead of the implementation of Executive Order 007 at the Shehu Abubakar District. The exercise is scheduled to officially begin on 24th November 2025.</p>

<p>We may recall that the Executive Order 007, signed by His Excellency, Governor Muhammadu Inuwa Yahaya (CON, Danmajen Gombe), revoked all former Rights of Occupancy in the former BAP-4 Layout, now Shehu Abubakar District, to restore order, eliminate distortions, and pave the way for a fully regulated and environmentally secure redevelopment of the area.</p>

<p>This collaborative effort between GOGIS and GOSUPDA demonstrates the state government\'s commitment to modernizing land administration and ensuring sustainable urban development in Gombe State.</p>',
            'cover_image' => null,
            'published_at' => now()->subDays(2),
        ]);

        News::create([
            'title' => 'GOGIS Achieves Milestone in Digital Land Registration',
            'slug' => 'gogis-achieves-milestone-digital-land-registration',
            'content' => '<p>The Gombe Geographic Information System (GOGIS) has successfully digitized over 10,000 land records, marking a significant milestone in the state\'s journey towards complete digital land administration.</p>

<p>This achievement represents a major step forward in the agency\'s mission to modernize land governance, resolve land conflicts, and improve revenue generation for the state government.</p>

<p>The Director-General, Dr. Kabiru Usman Hassan, expressed his satisfaction with the progress, noting that the digitization process has significantly reduced processing times for land titles and improved transparency in land transactions.</p>',
            'cover_image' => null,
            'published_at' => now()->subDays(7),
        ]);
    }
}
