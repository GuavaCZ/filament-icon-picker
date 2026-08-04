<?php

namespace Guava\IconPicker\Support;

use Illuminate\Database\Eloquent\Model;

class IconScope
{
    public const UNSCOPED = 'unscoped';

    /**
     * The scope segment embedded in custom icon ids and storage paths.
     * Accepts an already-computed id so callers without a model (the
     * HTTP endpoints) can pass the id from the picker token through.
     */
    public static function id(Model | string | null $scope): string
    {
        if (is_string($scope)) {
            return $scope;
        }

        if ($scope === null) {
            return static::UNSCOPED;
        }

        return md5("{$scope->getMorphClass()}::{$scope->getKey()}");
    }
}
