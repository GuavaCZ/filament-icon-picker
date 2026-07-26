<?php

use Guava\IconPicker\Tests\Fixtures\Post;
use Guava\IconPicker\Validation\VerifyIconScope;
use Illuminate\Support\Facades\Validator;

function scopeIdFor(?Post $post): string
{
    return $post === null
        ? 'unscoped'
        : md5("{$post->getMorphClass()}::{$post->getKey()}");
}

function passesScope(string $icon, ?Post $post): bool
{
    return Validator::make(['icon' => $icon], ['icon' => new VerifyIconScope($post)])->passes();
}

beforeEach(function () {
    $this->post = (new Post)->forceFill(['id' => 1]);
    $this->otherPost = (new Post)->forceFill(['id' => 2]);
});

it('ignores icons that are not custom uploads', function () {
    expect(passesScope('heroicon-o-academic-cap', $this->post))->toBeTrue()
        ->and(passesScope('heroicon-o-academic-cap', null))->toBeTrue()
    ;
});

it('accepts a custom icon uploaded for the same record', function () {
    expect(passesScope('_gfic_icons-' . scopeIdFor($this->post) . '.logo', $this->post))->toBeTrue();
});

it('rejects a custom icon belonging to another record', function () {
    expect(passesScope('_gfic_icons-' . scopeIdFor($this->otherPost) . '.logo', $this->post))->toBeFalse();
});

it('rejects an unscoped custom icon when the field is scoped to a record', function () {
    expect(passesScope('_gfic_icons-unscoped.logo', $this->post))->toBeFalse();
});

it('rejects a record-scoped custom icon when the field is unscoped', function () {
    expect(passesScope('_gfic_icons-' . scopeIdFor($this->post) . '.logo', null))->toBeFalse();
});

it('accepts an unscoped custom icon when the field is unscoped', function () {
    expect(passesScope('_gfic_icons-unscoped.logo', null))->toBeTrue();
});
