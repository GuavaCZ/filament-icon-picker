<?php

namespace Guava\IconPicker\Forms\Components;

use Filament\Forms\Components\Concerns\CanBeSearchable;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasPlaceholder;
use Guava\IconPicker\Actions\UploadCustomIcon;
use Guava\IconPicker\Forms\Components\Concerns\CanBeScopedToModel;
use Guava\IconPicker\Forms\Components\Concerns\CanCloseOnSelect;
use Guava\IconPicker\Forms\Components\Concerns\CanUploadCustomIcons;
use Guava\IconPicker\Forms\Components\Concerns\CanUseDropdown;
use Guava\IconPicker\Forms\Components\Concerns\HasSearchResultsView;
use Guava\IconPicker\Forms\Components\Concerns\HasSets;
use Guava\IconPicker\Http\PickerToken;
use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\Icon;
use Guava\IconPicker\Icons\IconSet;
use Guava\IconPicker\Support\IconScope;
use Guava\IconPicker\Validation\VerifyIcon;
use Guava\IconPicker\Validation\VerifyIconScope;

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

    public function verifyState(?string $state = null): ?string
    {
        if ($state && ! $this->resolveIcon($state)) {
            return null;
        }

        return $state;
    }

    /**
     * Authorizes the picker endpoints for this field's sets and scope.
     */
    public function getPickerToken(): string
    {
        return PickerToken::issue($this);
    }

    /**
     * Stable identity of the field's icon context. Pickers sharing it share
     * one client-side index fetch; the token itself differs on every render.
     */
    public function getClientCacheKey(): string
    {
        $sets = $this->getAllowedSets()
            ->map(fn (IconSet $set) => $set->getId())
            ->sort()
            ->implode(',')
        ;

        return md5($sets . '|' . IconScope::id($this->getScopedTo()));
    }
}
