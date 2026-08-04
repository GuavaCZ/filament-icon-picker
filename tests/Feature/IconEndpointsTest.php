<?php

use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tests\Fixtures\Post;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->post = (new Post)->forceFill(['id' => 1]);
    $this->otherPost = (new Post)->forceFill(['id' => 2]);
});

it('refuses the index endpoint without a valid token', function (?string $token) {
    test()->getJson(route('guava-icon-picker.index', array_filter(['token' => $token])))
        ->assertForbidden()
    ;
})->with([
    'missing' => [null],
    'garbage' => ['garbage'],
]);

it('refuses the svg endpoint without a valid token', function () {
    test()->getJson(route('guava-icon-picker.svgs', ['ids' => ['heroicon-o-x-mark']]))
        ->assertForbidden()
    ;
});

it('indexes only the sets offered by the field', function () {
    $index = fetchIconIndex(IconPicker::make('icon')->sets(['heroicons']));

    expect(collect($index['sets'])->pluck('id')->all())->toBe(['heroicons'])
        ->and($index['icons'])->not->toBeEmpty()
    ;
});

it('indexes custom icons only from the field\'s own scope', function () {
    putCustomIcon(scopeOf($this->post), 'mine');
    putCustomIcon(scopeOf($this->otherPost), 'other');

    $index = fetchIconIndex(
        IconPicker::make('icon')
            ->customIconsUploadEnabled()
            ->scopedTo($this->post)
    );

    $ids = collect($index['icons'])->map(fn (array $icon) => $icon[0]);

    expect($ids)->toContain('_gfic_icons-' . scopeOf($this->post) . '.mine')
        ->not->toContain('_gfic_icons-' . scopeOf($this->otherPost) . '.other')
    ;
});

it('returns 304 when the index has not changed', function () {
    $token = IconPicker::make('icon')->sets(['heroicons'])->getPickerToken();

    $etag = test()->getJson(route('guava-icon-picker.index', ['token' => $token]))
        ->assertOk()
        ->headers
        ->get('ETag')
    ;

    expect($etag)->not->toBeNull();

    test()->getJson(route('guava-icon-picker.index', ['token' => $token]), ['If-None-Match' => $etag])
        ->assertStatus(304)
    ;
});

it('caps an svg batch at 50 icons', function () {
    $ids = collect(range(1, 60))->map(fn (int $i) => "heroicon-o-fake-{$i}")->all();

    $svgs = test()->getJson(route('guava-icon-picker.svgs', [
        'token' => IconPicker::make('icon')->getPickerToken(),
        'ids' => $ids,
    ]))->assertOk()->json('svgs');

    expect($svgs)->toHaveCount(50);
});

it('serves several icons in one batch', function () {
    $svgs = test()->getJson(route('guava-icon-picker.svgs', [
        'token' => IconPicker::make('icon')->getPickerToken(),
        'ids' => ['heroicon-o-academic-cap', 'heroicon-o-beaker', 'not-a-registered-set-star'],
    ]))->assertOk()->json('svgs');

    expect($svgs['heroicon-o-academic-cap'])->toContain('<svg')
        ->and($svgs['heroicon-o-beaker'])->toContain('<svg')
        ->and($svgs['not-a-registered-set-star'])->toBeNull()
    ;
});
