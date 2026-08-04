<?php

namespace Guava\IconPicker\Http\Controllers;

use Guava\IconPicker\Http\PickerToken;
use Guava\IconPicker\Icons\Facades\IconManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use function Filament\Support\generate_icon_html;

class IconSvgController
{
    /**
     * Upper bound per request - the client chunks its queue to match.
     */
    public const MAX_ICONS = 50;

    public function __invoke(Request $request): JsonResponse
    {
        $token = PickerToken::parse($request->query('token'));

        abort_unless((bool) $token, 403);

        $svgs = collect((array) $request->query('ids', []))
            ->filter(fn ($id) => is_string($id) && filled($id))
            ->take(static::MAX_ICONS)
            ->mapWithKeys(fn (string $id) => [$id => $this->render($id, $token)])
            ->all()
        ;

        return response()->json(['svgs' => $svgs], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    protected function render(string $id, PickerToken $token): ?string
    {
        // Same gate as IconPicker::resolveIcon, driven by the token's context.
        $icon = IconManager::getIcon($id, checkScope: true, scope: $token->scopeId);

        if (! $icon || ! $token->allowsSet($icon->getSet()->getId())) {
            return null;
        }

        // Custom icons can change after upload; bundled sets are immutable, so
        // their rendered markup is worth caching.
        if ($icon->getSet()->custom || ! config('filament-icon-picker.cache.enabled', true)) {
            return $this->toHtml($id);
        }

        $prefix = config('filament-icon-picker.cache.prefix', 'guava-icon-picker');

        return Cache::remember(
            "{$prefix}.svg." . md5($id),
            now()->add(config('filament-icon-picker.cache.duration', '7 days')),
            fn () => $this->toHtml($id)
        );
    }

    protected function toHtml(string $id): ?string
    {
        return rescue(fn () => generate_icon_html($id)?->toHtml(), report: false);
    }
}
