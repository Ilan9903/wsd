<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->hexColor(),
            'background' => fake()->hexColor(),
            'primary_color' => fake()->hexColor(),
            'secondary_color' => fake()->hexColor(),
            'button_primary_color' => fake()->hexColor(),
            'button_secondary_color' => fake()->hexColor(),
            'button_text_primary_color' => '#000000',
            'button_text_secondary_color' => '#000000',
            'text_primary_color' => fake()->hexColor(),
            'text_secondary_color' => fake()->hexColor(),
            'logo_dark_theme' => '',
            'logo_light_theme' => '',
            'status' => false,
        ];
    }
}
