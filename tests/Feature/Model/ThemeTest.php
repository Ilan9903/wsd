<?php

namespace Tests\Feature\Model;

use App\Models\Theme;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\Datasets\Test\EmptyDataSet;
use Tests\Utils\TenancyTestCase;

#[Group('tenant')]
#[Group('model')]
class ThemeTest extends TenancyTestCase
{
    private Theme $theme;

    private Theme $theme2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theme = Theme::factory()->create([
            'name' => 'Test 22',
        ]);
        $this->theme2 = Theme::factory()->create([
            'name' => 'Test Theme',
        ]);

    }

    #[Test]
    public function it_can_create_a_theme()
    {
        Theme::create([
            'name' => 'xefi',
            'background' => 'linear-gradient(180deg, #000000 0%, #E10D1A 100%)',
            'primary_color' => '#c5c6c7',
            'secondary_color' => '#c5c6c7',
            'button_primary_color' => '#ff0000',
            'button_secondary_color' => '#ff0000',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => '000000',
            'text_secondary_color' => '#3d3846',
            'logo_light_theme' => 'logo-xefi.png',
        ]);
        $this->assertDatabaseHas('themes', [
            'name' => 'xefi',
            'background' => 'linear-gradient(180deg, #000000 0%, #E10D1A 100%)',
            'primary_color' => '#c5c6c7',
            'secondary_color' => '#c5c6c7',
            'button_primary_color' => '#ff0000',
            'button_secondary_color' => '#ff0000',
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => '000000',
            'text_secondary_color' => '#3d3846',
            'logo_light_theme' => 'logo-xefi.png',
        ]);
    }

    #[Test]
    public function it_can_update_a_theme()
    {
        $this->theme->update([
            'name' => 'update',
        ]);
        $this->assertDatabaseHas('themes', [
            'name' => 'update',
        ]);
    }

    #[Test]
    public function it_can_get_a_theme()
    {
        $theme = $this->theme;
        $this->assertSame($this->theme, $theme);
    }

    public function test_only_one_theme_can_be_active_at_a_time()
    {
        Theme::activateTheme($this->theme->id);

        $this->theme->refresh();
        $this->theme2->refresh();

        $this->assertTrue($this->theme->status, 'Le statut du premier thème devrait être activé.');
        $this->assertFalse($this->theme2->status, 'Le statut du second thème devrait être désactivé.');

        Theme::activateTheme($this->theme2->id);
        $this->theme->refresh();
        $this->theme2->refresh();

        $this->assertTrue($this->theme2->status, 'Le statut du second thème devrait être activé.');
        $this->assertFalse($this->theme->status, 'Le statut du premier thème devrait être désactivé.');
    }

    #[Test]
    public function it_can_deactivate_a_theme()
    {
        $this->theme2->deactivate();
        $this->theme2->refresh();

        $this->assertFalse($this->theme2->status);
    }

    #[Test]
    public function it_can_delete_a_theme()
    {
        $this->theme->delete();
        $this->assertDatabaseMissing('themes', [
            'name' => 'update',
        ]);
    }

    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }

    protected function tearDown(): void
    {
        $this->theme->delete();
        $this->theme2->delete();
        parent::tearDown();

    }
}
