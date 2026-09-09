<?php

namespace Guava\IconPicker\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\IconSet;
use Guava\IconPicker\Support\IconScope;
use Guava\IconPicker\Support\SvgSanitizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadCustomIcon extends Action
{
    // The label becomes the filename, so no separators. Same set as the client side filter.
    protected const LABEL_PATTERN = '/^[a-zA-Z0-9][a-zA-Z0-9\s]*$/';

    protected const MAX_FILE_SIZE = 512;

    public static function getDefaultName(): ?string
    {
        return 'upload-custom-icon';
    }

    public function configure(): static
    {
        return $this
            ->label(__('guava-icon-picker::actions.upload-custom-icon.label'))
            ->icon('heroicon-c-arrow-up-tray')
            ->modal()
            ->modalIcon(fn (UploadCustomIcon $action) => $action->getIcon())
            ->schema(fn (IconPicker $component) => [
                FileUpload::make('file')
                    ->label(__('guava-icon-picker::actions.upload-custom-icon.schema.file.label'))
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->maxSize(static::MAX_FILE_SIZE)
                    ->disk('public')
                    ->directory(fn (): string => IconSet::CUSTOM_DIRECTORY . DIRECTORY_SEPARATOR . IconScope::id($component->getScopedTo()))
                    ->getUploadedFileNameForStorageUsing(
                        fn (Get $get): string => $this->getIconName($get('label')) . '.svg'
                    )
                    // A valid SVG could still carry a script, and mimetypes alone wouldn't catch it.
                    ->saveUploadedFileUsing(function (FileUpload $component, TemporaryUploadedFile $file): ?string {
                        $contents = app(SvgSanitizer::class)->sanitize((string) $file->get());

                        if ($contents === null) {
                            return null;
                        }

                        $path = trim(
                            $component->getDirectory() . DIRECTORY_SEPARATOR . $component->getUploadedFileNameForStorage($file),
                            DIRECTORY_SEPARATOR
                        );

                        $disk = $component->getDisk();
                        $disk->put($path, $contents);

                        if ($component->getVisibility() === 'public') {
                            rescue(fn () => $disk->setVisibility($path, 'public'), report: false);
                        }

                        return $path;
                    })
                    ->rules([
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            foreach (Arr::wrap($value) as $upload) {
                                if (! $upload instanceof TemporaryUploadedFile) {
                                    continue;
                                }

                                if (app(SvgSanitizer::class)->sanitize((string) $upload->get()) === null) {
                                    $fail(__('guava-icon-picker::validation.invalid-svg'));
                                }
                            }
                        },
                    ])
                    ->required(),

                TextInput::make('label')
                    ->label(__('guava-icon-picker::actions.upload-custom-icon.schema.label.label'))
                    ->extraAlpineAttributes([
                        'x-on:input' => '$event.target.value = $event.target.value.replace(/[^a-zA-Z0-9\s]/g, \'\')',
                    ])
                    ->rules([
                        'regex:' . static::LABEL_PATTERN,
                        fn (): Closure => function (string $attribute, $value, Closure $fail) use ($component) {
                            $id = $this->getBladeIconId($value, $scope = $component->getScopedTo());

                            if (IconManager::getIcon($id, checkScope: true, scope: $scope)) {
                                $fail(__('guava-icon-picker::validation.icon-already-exists'));
                            }
                        },
                    ])
                    ->validationMessages([
                        'regex' => __('guava-icon-picker::validation.invalid-label'),
                    ])
                    ->required(),
            ])
            ->after(function (array $data, IconPicker $component): void {
                IconManager::forgetCustomIcons($component->getScopedTo());

                $component->state($this->getBladeIconId(
                    data_get($data, 'label'),
                    $component->getScopedTo()
                ));
                $component->callAfterCustomIconUploaded();
            })
        ;
    }

    /**
     * Used for both the filename and the icon id, so the two stay in sync.
     */
    protected function getIconName(?string $label): string
    {
        return (string) str($label ?? '')
            ->replaceMatches('/[^a-zA-Z0-9\s]/', '')
            ->lower()
            ->kebab()
        ;
    }

    protected function getBladeIconId(string $label, ?Model $scope): string
    {
        return str($this->getIconName($label))
            ->prepend(IconScope::id($scope) . '.')
            ->prepend(IconSet::CUSTOM_PREFIX . '-')
        ;
    }
}
