<?php

namespace Database\Seeders\Tenant;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Theme::factory()->create([
            'name' => 'xefi',
            'background' => 'linear-gradient(180deg, #000000 0%, #E10D1A 100%)',
            'primary_color' => fake()->hexColor(),
            'secondary_color' => '##c5c6c7',
            'button_primary_color' => '#ff0000',
            'button_secondary_color' => '#ff0000',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => '000000',
            'text_secondary_color' => '#3d3846',
            'logo_light_theme' => 'https://www.solutions-numeriques.com/wp-content/uploads/2020/06/logo-xefi.png',
        ]);

        Theme::factory()->create([
            'name' => 'utguard',
            'background' => 'linear-gradient(180deg, #000000 0%, #E6BB3E 100%)',
            'primary_color' => '#CCC3C3',
            'secondary_color' => '##c5c6c7',
            'button_primary_color' => '#E6BB3E',
            'button_secondary_color' => '#E6BB3E',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => 'E6BB3E',
            'text_secondary_color' => 'E6BB3E',
            'logo_light_theme' => 'https://utguard.com/wp-content/themes/idcomweb/img/logo-utguard.svg',
        ]);

        Theme::factory()->create([
            'name' => 'hopla',
            'background' => 'radial-gradient(18% 28% at 24% 50%, #CEFAFFFF 7%, #073AFF00 100%),radial-gradient(18% 28% at 18% 71%, #FFFFFF59 6%, #073AFF00 100%),radial-gradient(70% 53% at 36% 76%, #73F2FFFF 0%, #073AFF00 100%),radial-gradient(42% 53% at 15% 94%, #FFFFFFFF 7%, #073AFF00 100%),radial-gradient(42% 53% at 34% 72%, #FFFFFFFF 7%, #073AFF00 100%),radial-gradient(18% 28% at 35% 87%, #FFFFFFFF 7%, #073AFF00 100%),radial-gradient(31% 43% at 7% 98%, #FFFFFFFF 24%, #073AFF00 100%),radial-gradient(21% 37% at 72% 23%, #D3FF6D9C 24%, #073AFF00 100%),radial-gradient(35% 56% at 91% 74%, #8A4FFFF5 9%, #073AFF00 100%),radial-gradient(74% 86% at 67% 38%, #6DFFAEF5 24%, #073AFF00 100%),linear-gradient(125deg, #4EB5FFFF 1%, #4C00FCFF 100%)',
            'primary_color' => '#0088CA',
            'secondary_color' => '#F2FAFFCC',
            'button_primary_color' => '#0088CA',
            'button_secondary_color' => '#F2FAFF',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => '#303030',
            'text_secondary_color' => '#0088CA',
            'logo_light_theme' => 'https://www.hopla.cloud/wp-content/themes/idcomweb/img/logo-hopla-footer.svg',
            'status' => true,
        ]);

    }
}
