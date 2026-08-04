<?php

namespace Guava\IconPicker\Icons;

use BladeUI\Icons\Factory as IconFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class IconManager
{
    /**
     * @var Collection<string, IconSet>|null
     */
    private ?Collection $sets = null;

    public function __construct(
        private IconFactory $factory,
    ) {}

    /**
     * @return Collection<string, IconSet>
     */
    public function getSets(): Collection
    {
        // Registered as a singleton, so the sets are built once per request.
        return $this->sets ??= collect($this->factory->all())
            ->map(static fn (array $configuration, string $id) => IconSet::createFromArray($configuration, $id))
        ;
    }

    public function getIcons(null | string | IconSet $set = null, Model | string | null $scope = null, bool $checkScope = true): Collection
    {
        if ($set instanceof IconSet) {
            $set = $set->getId();
        }

        return $this->getSets()
            ->when(
                $set,
                fn (Collection $sets) => $sets->filter(fn (IconSet $iconSet) => $iconSet->getId() === $set)
            )
            ->map(fn (IconSet $is) => $is->getIcons($scope, $checkScope))
            ->collapse()
        ;
    }

    public function getSetByPrefix(string $prefix): ?IconSet
    {
        return $this->getSets()->where(fn (IconSet $set) => $set->getPrefix() === $prefix)->first();
    }

    public function getSetFromIcon(string $id): ?IconSet
    {
        $prefix = str($id)->before('-');

        return $this->getSetByPrefix($prefix);
    }

    public function getIcon(?string $id, bool $checkScope = false, Model | string | null $scope = null): ?Icon
    {
        if ($id === null) {
            return null;
        }

        foreach ($this->getSets() as $set) {
            if (! str($id)->startsWith($set->getPrefix())) {
                continue;
            }

            return $set->findIcon($id, $checkScope, $scope);
        }

        return null;
    }

    /**
     * Drop the cached custom icon listing for a scope, e.g. after an upload.
     */
    public function forgetCustomIcons(Model | string | null $scope = null): void
    {
        $this->getSets()
            ->filter(fn (IconSet $set) => $set->custom)
            ->each(fn (IconSet $set) => $set->forgetCachedIcons($scope))
        ;
    }
}
