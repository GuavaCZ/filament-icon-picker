<?php

namespace Guava\IconPicker\Http;

use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Icons\IconSet;
use Guava\IconPicker\Support\IconScope;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Carries a field's icon context (allowed sets + scope) to the picker
 * endpoints. Encrypted with the app key, so issuing and validating it
 * requires no setup - a token is only ever handed to someone who was
 * authorized to render the form it belongs to.
 */
final class PickerToken
{
    /**
     * @param  array<int, string>  $sets
     */
    public function __construct(
        public readonly array $sets,
        public readonly string $scopeId,
    ) {}

    public static function issue(IconPicker $component): string
    {
        return Crypt::encrypt([
            'sets' => $component->getAllowedSets()
                ->map(fn (IconSet $set) => $set->getId())
                ->values()
                ->all(),
            'scope' => IconScope::id($component->getScopedTo()),
            'exp' => now()->add(config('filament-icon-picker.token_lifetime', '12 hours'))->getTimestamp(),
        ]);
    }

    public static function parse(?string $token): ?static
    {
        if (blank($token)) {
            return null;
        }

        try {
            $payload = Crypt::decrypt($token);
        } catch (DecryptException) {
            return null;
        }

        if (
            ! is_array($payload)
            || ! is_array($payload['sets'] ?? null)
            || ! is_string($payload['scope'] ?? null)
            || ! is_int($payload['exp'] ?? null)
            || $payload['exp'] < now()->getTimestamp()
        ) {
            return null;
        }

        return new self($payload['sets'], $payload['scope']);
    }

    public function allowsSet(string $setId): bool
    {
        return in_array($setId, $this->sets, true);
    }
}
