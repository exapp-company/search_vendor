<?php

namespace App\Services\Files;

use App\Models\File;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class UploadImage
{
    const DEFAULT_DIRECTORY = 'images/';

    protected UploadedFile $file;
    protected ?string $name = null;
    protected ?string $prefix = null;
    protected string $directory;
    protected ?array $allowedMimeTypes = [];
    protected ?array $resize = [];
    protected ?array $thumbnail = [];

    public function file(UploadedFile $file): static
    {
        $this->file = $file;
        return $this;
    }

    public function name(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function prefix(?string $prefix): static
    {
        $this->prefix = $prefix ? $prefix . '_' : '';
        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = Str::startsWith($directory, '/') ? $directory : static::DEFAULT_DIRECTORY . $directory;
        return $this;
    }

    public function allowedMimeTypes(?array $mimeTypes): static
    {
        $this->allowedMimeTypes = $mimeTypes;
        return $this;
    }

    public function resize(int $width, int $height): static
    {
        $this->resize = ['width' => $width, 'height' => $height];
        return $this;
    }

    public function thumbnail(int $width, int $height): static
    {
        $this->thumbnail = ['width' => $width, 'height' => $height];
        return $this;
    }

    public function store(): false|string
    {
        $this->validateMimeType($this->file, $this->allowedMimeTypes);

        $fileName = $this->generateFileName($this->file);
        $disk = 'public';

        if (!Storage::disk($disk)->exists($this->directory)) {
            Storage::disk($disk)->makeDirectory($this->directory);
        }

        $path = $this->file->storeAs($this->directory, $fileName, $disk);

        if (!empty($this->resize)) {
            $this->resizeImage($path, $this->resize['width'], $this->resize['height'], $disk);
        }

        if (!empty($this->thumbnail)) {
            $this->generateThumbnail($path, $this->thumbnail['width'], $this->thumbnail['height'], $disk);
        }

        return $path;
    }

    protected function validateMimeType(UploadedFile $file, ?array $allowedMimeTypes): void
    {
        if (!empty($allowedMimeTypes) && !in_array($file->getClientMimeType(), $allowedMimeTypes)) {
            throw new Exception('Invalid file type. Only specified mime types are allowed.');
        }
    }

    protected function generateFileName(UploadedFile $file): string
    {
        $prefix = $this->prefix ?? '';

        return $this->name
            ? Str::lower($prefix . $this->name) . '.' . $file->getClientOriginalExtension()
            : $prefix . Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    protected function resizeImage(string $path, int $width, int $height, string $disk = 'public'): string
    {
        $image = Image::load(Storage::disk($disk)->path($path))
            ->width($width)
            ->height($height)
            ->optimize();

        $image->save();

        return $path;
    }

    protected function generateThumbnail(string $path, int $width, int $height, string $disk = 'public'): string
    {
        $thumbnailPath = 'thumbnails/' . $path;
        $thumbnail = Image::load(Storage::disk($disk)->path($path))
            ->width($width)
            ->height($height)
            ->optimize();

        $thumbnail->save(Storage::disk($disk)->path($thumbnailPath));

        return $thumbnailPath;
    }
    public function storeFile(UploadedFile $file)
    {
        $user = request()->user();
        $path = $file->store('public/images/' . $user->id);
        $createdFile = new File([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType()
        ]);
        $createdFile->save();
        return $createdFile;
    }
}
