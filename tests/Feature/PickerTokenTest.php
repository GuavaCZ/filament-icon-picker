<?php

use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Http\PickerToken;
use Guava\IconPicker\Support\IconScope;
use Guava\IconPicker\Tests\Fixtures\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

afterEach(fn () => Carbon::setTestNow());

it('round-trips the field context', function () {
    $post = (new Post)->forceFill(['id' => 1]);

    $field = IconPicker::make('icon')
        ->sets(['heroicons'])
        ->scopedTo($post)
    ;

    $token = PickerToken::parse($field->getPickerToken());

    expect($token)->not->toBeNull()
        ->and($token->sets)->toBe(['heroicons'])
        ->and($token->scopeId)->toBe(IconScope::id($post))
    ;
});

it('reports the unscoped scope for a field without one', function () {
    expect(PickerToken::parse(IconPicker::make('icon')->getPickerToken())->scopeId)
        ->toBe(IconScope::UNSCOPED)
    ;
});

it('rejects a missing or malformed token', function () {
    expect(PickerToken::parse(null))->toBeNull()
        ->and(PickerToken::parse(''))->toBeNull()
        ->and(PickerToken::parse('garbage'))->toBeNull()
    ;
});

it('rejects a token with an unexpected payload', function (mixed $payload) {
    expect(PickerToken::parse(Crypt::encrypt($payload)))->toBeNull();
})->with([
    'not an array' => ['just-a-string'],
    'sets not an array' => [['sets' => 'heroicons', 'scope' => 'unscoped', 'exp' => PHP_INT_MAX]],
    'scope missing' => [['sets' => ['heroicons'], 'exp' => PHP_INT_MAX]],
    'exp missing' => [['sets' => ['heroicons'], 'scope' => 'unscoped']],
]);

it('rejects an expired token', function () {
    $token = IconPicker::make('icon')->getPickerToken();

    Carbon::setTestNow(now()->addHours(13));

    expect(PickerToken::parse($token))->toBeNull();
});

it('honors a configured token lifetime', function () {
    config()->set('filament-icon-picker.token_lifetime', '1 hour');

    $token = IconPicker::make('icon')->getPickerToken();

    Carbon::setTestNow(now()->addMinutes(61));

    expect(PickerToken::parse($token))->toBeNull();
});
