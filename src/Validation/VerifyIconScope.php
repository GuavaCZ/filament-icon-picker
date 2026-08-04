<?php

namespace Guava\IconPicker\Validation;

use Closure;
use Guava\IconPicker\Icons\IconSet;
use Guava\IconPicker\Support\IconScope;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class VerifyIconScope implements ValidationRule
{
    public function __construct(
        private Model | string | null $scopedTo,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $prefix = str($value)->before('-')->toString();
        $scope = str($value)->after('-')->before('.')->toString();

        if ($prefix !== IconSet::CUSTOM_PREFIX) {
            return;
        }

        // Custom icon without scope - should not be possible
        if (empty($scope)) {
            $fail('Scope missing for custom icon.');
        }
        if (IconScope::id($this->scopedTo) !== $scope) {
            $fail('Unauthorized icon scope.');
        }
    }
}
