<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @template TModel of \App\Models\Link
 *
 * @extends Factory<TModel>
 */
class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => Str::random(70),
            'password' => Hash::make('password'),
            'is_active' => true,
            'expired_at' => null,
            'message_subject' => null,
            'message' => null,
            'has_receipt' => false,
            'has_watermark' => false,
            'recipients_email_addresses' => null,
            'src_folder_id' => null,
        ];
    }

    /**
     * @return self
     */
    public function withoutPassword(): self
    {
        return $this->state(fn () => [
            'password' => null,
        ]);
    }

    /**
     * @return self
     */
    public function expired(): self
    {
        return $this->state(fn () => [
            'expired_at' => now()->subDays(60),
        ]);
    }
}
