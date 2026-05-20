<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\Client
 *
 * @extends Factory<TModel>
 */
class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => faker()->email(),
            'phone_number' => '04'.faker()->number(11111111, 99999999),
            'address' => faker()->streetAddress(),
            'zipcode' => faker()->postcode(),
            'avatar' => 'default.png',
            'name' => faker()->company(),
            'used_storage' => 0,
            'allocated_storage' => 10,
            'allocated_users' => 10,
            'is_health' => false,
        ];
    }
}
