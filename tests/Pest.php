<?php

use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tests\Fixtures\Post;
use Guava\IconPicker\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Feature', 'Unit');

function putCustomIcon(string $scopeId, string $name): void
{
    Storage::disk('public')->put("icon-picker-icons/{$scopeId}/{$name}.svg", '<svg xmlns="http://www.w3.org/2000/svg"/>');
}

function scopeOf(Post $post): string
{
    return md5("{$post->getMorphClass()}::{$post->getKey()}");
}

/**
 * Fetches one icon's svg through the batch endpoint, as the client would.
 */
function fetchIconSvg(IconPicker $field, string $id): ?string
{
    $response = test()->getJson(route('guava-icon-picker.svgs', [
        'token' => $field->getPickerToken(),
        'ids' => [$id],
    ]))->assertOk();

    return $response->json('svgs')[$id] ?? null;
}

/**
 * Fetches the field's icon index through the endpoint, as the client would.
 */
function fetchIconIndex(IconPicker $field): array
{
    return test()->getJson(route('guava-icon-picker.index', [
        'token' => $field->getPickerToken(),
    ]))->assertOk()->json();
}
