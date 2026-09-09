<?php

namespace Guava\IconPicker\Validation;

use Closure;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Contracts\Validation\ValidationRule;

class VerifyIcon implements ValidationRule
{
    public function __construct(protected IconPicker $iconPicker) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Not IconManager::getIcon() - that matches on the prefix alone.
        if (! is_string($value) || ! $this->iconPicker->resolveIcon($value)) {
            $fail(__('guava-icon-picker::validation.icon-does-not-exist'));
        }
    }
}
