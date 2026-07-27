<?php

namespace Guava\IconPicker\Icons;

use Guava\IconPicker\Validation\VerifyIconScope;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class IconSet
{
    // The set uploaded icons live in. The service provider, the upload action and $custom
    // below all have to agree - if the id drifts, nothing is custom and scope checks pass.
    public const CUSTOM_ID = 'icon-picker-icons';

    public const CUSTOM_DIRECTORY = 'icon-picker-icons';

    public const CUSTOM_PREFIX = '_gfic_icons';

    public string $label;

    private Filesystem $filesystem;

    private FilesystemFactory $disks;

    public function __construct(
        protected string $id,
        protected ?string $prefix,
        protected ?string $fallback,
        protected ?string $class,
        protected array $attributes = [],
        protected array $paths = [],
        protected ?string $disk = null,
        public bool $custom = false,
    ) {
        $this->filesystem = app(Filesystem::class);
        $this->disks = app(FilesystemFactory::class);

        $this->label = $this->custom
            ? 'Custom icons'
            : str($this->id)->headline()->lower()->ucfirst();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getIcons(?Model $scopedTo = null, bool $checkScopes = true): Collection
    {
        $icons = collect();
        foreach ($this->paths as $path) {
            $files = $this->filesystem($this->disk)->allFiles($path);
            foreach ($files as $file) {
                if (is_string($file)) {
                    $file = new \SplFileInfo($file);
                }

                $name = str($file->getPathname())
                    ->after($path)
                    ->trim(DIRECTORY_SEPARATOR)
                    ->beforeLast(".{$file->getExtension()}")
                    ->replace(DIRECTORY_SEPARATOR, '.')
                ;

                $id = "$this->prefix-$name";

                if ($this->custom && $checkScopes) {
                    if (! Validator::make(['icon' => $id], ['icon' => new VerifyIconScope($scopedTo)])->passes()) {
                        continue;
                    }
                    $name = $name->after('.');
                }

                //                if ($allowedIcons && !in_array($filename, $allowedIcons)) {
                //                    continue;
                //                }
                //                if ($disallowedIcons && in_array($filename, $disallowedIcons)) {
                //                    continue;
                //                }

                $icons->push(new Icon($id, $name, $this));
            }
        }

        return $icons;
    }

    private function filesystem(?string $disk = null): \Illuminate\Contracts\Filesystem\Filesystem | Filesystem
    {
        return $disk ? $this->disks->disk($disk) : $this->filesystem;
    }

    public static function createFromArray(array $configuration, string $id): static
    {
        return app(static::class, [
            'id' => $id,
            'prefix' => $configuration['prefix'] ?? null,
            'fallback' => $configuration['fallback'] ?? null,
            'class' => $configuration['class'] ?? null,
            'attributes' => $configuration['attributes'] ?? [],
            'paths' => $configuration['paths'] ?? [],
            'disk' => $configuration['disk'] ?? null,
            'custom' => $id === static::CUSTOM_ID,
        ]);
    }
}
