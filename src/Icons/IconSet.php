<?php

namespace Guava\IconPicker\Icons;

use Closure;
use Guava\IconPicker\Support\IconScope;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    public function getIcons(Model | string | null $scopedTo = null, bool $checkScopes = true): Collection
    {
        $scopeId = IconScope::id($scopedTo);

        return collect($this->remember(
            $this->cacheKey($scopeId, $checkScopes),
            fn (): array => $this->listIcons($scopeId, $checkScopes)
        ))->map(fn (array $icon) => new Icon($icon['id'], $icon['name'], $this));
    }

    public function forgetCachedIcons(Model | string | null $scope = null): void
    {
        $scopeId = IconScope::id($scope);

        Cache::forget($this->cacheKey($scopeId, true));
        Cache::forget($this->cacheKey($scopeId, false));
    }

    /**
     * @return array<int, array{id: string, name: string}>
     */
    protected function listIcons(string $scopeId, bool $checkScopes): array
    {
        $icons = [];

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
                    if ((string) $name->before('.') !== $scopeId) {
                        continue;
                    }
                    $name = $name->after('.');
                }

                $icons[] = ['id' => $id, 'name' => (string) $name];
            }
        }

        return $icons;
    }

    /**
     * Cached as plain arrays - Icon objects hold filesystem instances and
     * cannot be serialized. Non-custom listings are scope-independent.
     */
    protected function cacheKey(string $scopeId, bool $checkScopes): string
    {
        $prefix = config('filament-icon-picker.cache.prefix', 'guava-icon-picker');
        $key = "{$prefix}.set.{$this->id}";

        if ($this->custom) {
            $key .= $checkScopes ? ".{$scopeId}" : '.all';
        }

        return $key;
    }

    protected function remember(string $key, Closure $callback): array
    {
        if (! config('filament-icon-picker.cache.enabled', true)) {
            return $callback();
        }

        return Cache::remember(
            $key,
            now()->add(config('filament-icon-picker.cache.duration', '7 days')),
            $callback
        );
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
