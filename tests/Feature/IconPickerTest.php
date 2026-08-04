<?php

use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Icons\IconSet;

it('keeps a state that resolves to a real icon', function () {
    expect(IconPicker::make('icon')->verifyState('heroicon-o-academic-cap'))
        ->toBe('heroicon-o-academic-cap')
    ;
});

it('discards a state whose icon does not exist', function () {
    expect(IconPicker::make('icon')->verifyState('not-a-registered-set-star'))->toBeNull();
});

it('passes a null state through untouched', function () {
    expect(IconPicker::make('icon')->verifyState(null))->toBeNull();
});

it('renders as a dropdown by default', function () {
    expect(IconPicker::make('icon')->isDropdown())->toBeTrue()
        ->and(IconPicker::make('icon')->dropdown(false)->isDropdown())->toBeFalse()
    ;
});

it('stays open on select by default', function () {
    expect(IconPicker::make('icon')->shouldCloseOnSelect())->toBeFalse()
        ->and(IconPicker::make('icon')->closeOnSelect()->shouldCloseOnSelect())->toBeTrue()
    ;
});

it('has custom icon uploads disabled by default', function () {
    expect(IconPicker::make('icon')->isCustomIconsUploadEnabled())->toBeFalse()
        ->and(IconPicker::make('icon')->customIconsUploadEnabled()->isCustomIconsUploadEnabled())->toBeTrue()
    ;
});

it('uses the grid search results view by default', function () {
    expect(IconPicker::make('icon')->getSearchResultsView())
        ->toBe('guava-icon-picker::search-results.grid')
    ;
});

it('switches the search results view', function (string $method, string $view) {
    expect(IconPicker::make('icon')->{$method}()->getSearchResultsView())->toBe($view);
})->with([
    ['gridSearchResults', 'guava-icon-picker::search-results.grid'],
    ['listSearchResults', 'guava-icon-picker::search-results.list'],
    ['iconsSearchResults', 'guava-icon-picker::search-results.icons'],
]);

it('passes the tooltip flag to the icons view', function () {
    expect(IconPicker::make('icon')->iconsSearchResults(withTooltips: false)->getSearchResultsViewData())
        ->toHaveKey('withTooltips', false)
    ;
});

it('always exposes the field itself to the search results view', function () {
    $field = IconPicker::make('icon');

    expect($field->getSearchResultsViewData())->toHaveKey('field', $field);
});

it('offers every registered set when none are restricted', function () {
    expect(IconPicker::make('icon')->getAllowedSets()->keys())->toContain('heroicons');
});

it('restricts the offered sets with sets()', function () {
    $allowed = IconPicker::make('icon')->sets(['heroicons'])->getAllowedSets();

    expect($allowed->every(fn (IconSet $set) => $set->getId() === 'heroicons'))->toBeTrue();
});

it('offers nothing when restricted to a set that is not registered', function () {
    expect(IconPicker::make('icon')->sets(['not-registered'])->getAllowedSets())->toBeEmpty();
});

it('lists icons through the index endpoint', function () {
    $index = fetchIconIndex(IconPicker::make('icon')->sets(['heroicons']));

    expect($index['icons'])->not->toBeEmpty()
        ->and(collect($index['sets'])->pluck('id')->all())->toBe(['heroicons'])
    ;
});

it('renders svg markup for a known icon through the svg endpoint', function () {
    expect(fetchIconSvg(IconPicker::make('icon'), 'heroicon-o-academic-cap'))
        ->toContain('<svg')
    ;
});

it('renders no markup for an unknown icon through the svg endpoint', function () {
    expect(fetchIconSvg(IconPicker::make('icon'), 'not-a-registered-set-star'))->toBeNull();
});
