<?php

namespace Database\Factories;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\FileOrFolder
 *
 * @extends Factory<TModel>
 */
class FileOrFolderFactory extends Factory
{
    private $mimeExtensions = [
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'text/plain' => 'txt',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = FileOrFolder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => faker()->name(),
            'parent_id' => null,
            'size' => faker()->number(0, 5000000),
            'type' => faker()->randomElement(FileOrFolderType::cases()),
            'path' => '/',
            'user_id' => User::inRandomOrder()->value('id'),
            'mime_type' => null,
            'link_id' => Link::inRandomOrder()->value('id'),
        ];
    }

    public function forUser(User $user): Factory
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }

    /**
     * State for folders.
     */
    public function folder(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => FileOrFolderType::Folder,
                'size' => 0,
            ];
        });
    }

    /**
     * State for files.
     */
    public function file(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => FileOrFolderType::File,
                'name' => faker()->name(),
                'mime_type' => faker()->randomElement($this->mimeExtensions),
            ];
        });
    }

    public function withParentFolder(): Factory
    {
        return $this->state(function () {
            return [
                'parent_id' => FileOrFolder::factory()->folder()->create()->id,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (FileOrFolder $f) {

            if (! $f->parent_id) {
                $f->path = '/';
                $f->save();

                return;
            }

            $parent = FileOrFolder::where('id', $f->parent_id)->first();

            if (! $parent || $parent->type !== FileOrFolderType::Folder) {
                throw new \Exception('Parent must be a folder!');
            }

            $f->path = rtrim($parent->path, '/').'/'.$parent->name.'/';
            $f->save();
        });
    }
}
