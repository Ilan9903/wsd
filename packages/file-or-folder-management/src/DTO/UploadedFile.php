<?php

namespace Hopla\FileOrFolderManagement\DTO;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use JsonException;

class UploadedFile
{
    public FilesystemAdapter $local;

    public string $path;

    public string $infoPath;

    public string $filePath;

    public static string $uploadPath = '/uploads';

    /**
     * @var array<string, mixed>
     */
    private array $metadata;

    public function __construct(
        public readonly string $id,
        string $path,
    ) {
        $this->local = Storage::disk('local');
        $this->path = $path;
        $this->filePath = $path.'/'.$this->id;
        $this->infoPath = $path.'/'.$this->id.'.info';
        $this->metadata = $this->exists() ? $this->loadInfo() : [];
    }

    public function exists(): bool
    {
        return $this->local->fileExists($this->infoPath);
    }

    /**
     * @return array<string, mixed>
     */
    private function loadInfo(): array
    {
        return json_decode($this->local->get($this->infoPath), true);
    }

    /**
     * @return array|mixed[]
     */
    public function &getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function info(): mixed
    {
        $content = $this->local->get($this->infoPath);

        return json_decode($content, true);
    }

    /**
     * @return array|mixed[]
     */
    public function &getInfo(): array
    {
        return $this->metadata;
    }

    /**
     * @param  string  $length
     * @param  array<string, mixed>  $meta
     * @return void
     */
    public function createOnDisk(string $length, array $meta): void
    {
        $this->metadata = [
            'length' => $length,
            'offset' => 0,
            'metadata' => $meta,
        ];
        $this->update();
    }

    public function getUploadId(): ?string
    {
        return $this->metadata['metadata']['upload_id'] ?? null;
    }

    public function removeInfo(): void
    {
        $this->local->delete($this->infoPath);
    }

    /**
     * @return void
     *
     * @throws JsonException
     */
    private function update(): void
    {
        $this->metadata['expire_date'] = now()->addDay()->timestamp;

        $json = json_encode($this->metadata, JSON_THROW_ON_ERROR);

        $this->local->put($this->infoPath, $json);
    }

    public function getName(): string
    {
        return pathinfo($this->metadata['metadata']['name'], PATHINFO_FILENAME);
    }

    public function getBaseName(): string
    {
        return pathinfo($this->metadata['metadata']['name'], PATHINFO_BASENAME);
    }

    public function getExtension(): string
    {
        return pathinfo($this->metadata['metadata']['name'], PATHINFO_EXTENSION);
    }

    public function getMimeType(): string
    {
        return $this->metadata['metadata']['type'];
    }

    public function getLength(): int
    {
        return $this->metadata['length'];
    }

    public function getOffset(): int
    {
        return $this->metadata['offset'];
    }

    public function getSize(): ?int
    {
        return $this->metadata['metadata']['size'] ?? null;
    }

    public function getTempPath(): ?string
    {
        return $this->metadata['metadata']['tempPath'] ?? null;
    }
}
