<?php

namespace Database\Factories;

use App\Models\FileOrFolder;
use App\Models\History;
use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Enums\HistoryModelType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\History
 *
 * @extends Factory<TModel>
 */
class HistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = History::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'link_id' => Link::inRandomOrder()->value('id'),
            'model_type' => $this->modelType(faker()->randomElement(HistoryModelType::cases())),
            'action' => faker()->randomElement(HistoryActionType::cases()),
            'description' => faker()->paragraphs(),
            'ip_address' => faker()->ipv4(),
        ];
    }

    public function forUser(User $user): Factory
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }

    public function forLink(Link $link): Factory
    {
        return $this->state([
            'link_id' => $link->id,
        ]);
    }

    public function modelType($type): string
    {
        switch ($type) {
            case HistoryModelType::USER:
                return User::class;
            case HistoryModelType::LINK:
                return Link::class;
            case HistoryModelType::FILE:
                return FileOrFolder::class;
            default:
                return History::class;
        }
    }
}
