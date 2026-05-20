<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => faker()->company(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Group $group) {
            if (is_null($group->owner_id)) {
                $group->owner()->associate(User::inRandomOrder()->first() ?? User::factory()->create());
            }
        });
    }
}
