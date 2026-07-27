<?php

namespace Guava\IconPicker\Forms\Components;

use Filament\Forms\Components\Concerns\CanBeSearchable;
use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Concerns\HasPlaceholder;
use Guava\IconPicker\Actions\UploadCustomIcon;
use Guava\IconPicker\Forms\Components\Concerns\CanBeScopedToModel;
use Guava\IconPicker\Forms\Components\Concerns\CanCloseOnSelect;
use Guava\IconPicker\Forms\Components\Concerns\CanUploadCustomIcons;
use Guava\IconPicker\Forms\Components\Concerns\CanUseDropdown;
use Guava\IconPicker\Forms\Components\Concerns\HasSearchResultsView;
use Guava\IconPicker\Forms\Components\Concerns\HasSets;
use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\Icon;
use Guava\IconPicker\Icons\IconSet;
use Guava\IconPicker\Validation\VerifyIcon;
use Guava\IconPicker\Validation\VerifyIconScope;
use Illuminate\Support\Collection;
use Livewire\Attributes\Renderless;

use function Filament\Support\generate_icon_html;

class IconPicker extends Field
{
    use CanBeScopedToModel;
    use CanBeSearchable;
    use CanCloseOnSelect;
    use CanUploadCustomIcons;
    use CanUseDropdown;
    use HasPlaceholder;
    use HasSearchResultsView;
    use HasSets;

    protected string $view = 'guava-icon-picker::forms.components.icon-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->placeholder(__('filament-icon-picker::icon-picker.placeholder'))
            // Scope first, so a foreign icon reports that and not "does not exist". Attached
            // even without a scope, otherwise unscoped fields accept any scoped icon.
            ->rules(fn (IconPicker $component) => [
                new VerifyIconScope($component->getScopedTo()),
                new VerifyIcon($component),
            ])
        ;
    }

    /**
     * The icon has to exist, come from an allowed set and match the field's scope.
     */
    public function resolveIcon(?string $id): ?Icon
    {
        if (blank($id)) {
            return null;
        }

        $icon = IconManager::getIcon($id, checkScope: true, scope: $this->getScopedTo());

        if (! $icon) {
            return null;
        }

        $allowed = $this->getAllowedSets()
            ->contains(fn (IconSet $set) => $set->getId() === $icon->getSet()->getId())
        ;

        return $allowed ? $icon : null;
    }

    public function getHintActions(): array
    {
        if ($this->isCustomIconsUploadEnabled()) {
            return [
                UploadCustomIcon::make()
                    ->disabled($this->isDisabled()),
            ];
        }

        return parent::getHintActions();
    }

    public function getState(): mixed
    {
        return $this->verifyState(
            parent::getState()
        );
    }

    public function getDisplayName(): ?string
    {
        if ($state = $this->getState()) {
            if ($icon = $this->resolveIcon($state)) {
                return $icon->label;
            }
        }

        return null;
    }

    #[ExposedLivewireMethod]
    #[Renderless]
    public function getSetJs(?string $state = null): ?string
    {
        if ($state) {
            return IconManager::getSetFromIcon($state)?->getId();
        }

        return null;
    }

    #[ExposedLivewireMethod]
    #[Renderless]
    public function getIconsJs(?string $set = null): Collection
    {
        // $set comes from the browser, so list the allowed sets and not every registered one.
        return $this->getAllowedSets()
            ->when(
                $set,
                fn (Collection $sets) => $sets->filter(fn (IconSet $iconSet) => $iconSet->getId() === $set)
            )
            ->map(fn (IconSet $iconSet) => $iconSet->getIcons($this->getScopedTo()))
            ->collapse()
            ->values()
        ;
    }

    #[ExposedLivewireMethod]
    #[Renderless]
    public function getIconSvgJs(?string $id = null): ?string
    {
        if ($this->resolveIcon($id)) {
            return generate_icon_html($id)?->toHtml();
        }

        return null;
    }

    #[ExposedLivewireMethod]
    #[Renderless]
    public function verifyState(?string $state = null): ?string
    {
        if ($state && ! $this->resolveIcon($state)) {
            return null;
        }

        return $state;
    }
}
