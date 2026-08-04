<?php

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
