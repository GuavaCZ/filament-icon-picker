<?php

use Filament\Schemas\Schema;
use Guava\IconPicker\Actions\UploadCustomIcon;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tests\Fixtures\SchemaHost;
use Guava\IconPicker\Validation\VerifyIcon;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    app('translator')->addNamespace('filament-icon-picker', __DIR__ . '/../Fixtures/colliding-translations');
});

it('renders its own translations when another picker owns the legacy namespace', function (string $locale, string $placeholder, string $allIcons) {
    app()->setLocale($locale);

    $field = Schema::make(new SchemaHost)->components([IconPicker::make('icon')])->getComponents()[0];

    expect($field->getPlaceholder())->toBe($placeholder);

    $html = view('guava-icon-picker::components.search', [
        'field' => $field,
        'sets' => [],
        'searchPrompt' => 'Search',
    ])->render();

    expect($html)->toContain($allIcons)
        ->not->toContain('filament-icon-picker::icon-picker.all-icons')
        ->and(__('filament-icon-picker::icon-picker.all_sets'))->toBe('Other picker sets')
    ;
})->with([
    ['en', 'No icon selected', 'All icons'],
    ['de', 'Kein Icon ausgewählt', 'Alle Icons'],
    ['cs', 'Není vybrána žádná ikona', 'Všechny ikony'],
]);

it('keeps upload actions and validation messages translated during a namespace collision', function () {
    expect(UploadCustomIcon::make()->getLabel())->toBe('Upload custom icon');

    $validator = Validator::make(['icon' => 'missing-icon'], [
        'icon' => [new VerifyIcon(IconPicker::make('icon'))],
    ]);

    expect($validator->errors()->first('icon'))->toBe('This icon does not exist.');
});

it('loads application overrides without losing package defaults or the other picker translations', function () {
    app('translation.loader')->addPath(__DIR__ . '/../Fixtures/translation-overrides');

    expect(__('guava-icon-picker::icon-picker.all-icons'))->toBe('Choose from every icon')
        ->and(IconPicker::make('icon')->getPlaceholder())->toBe('No icon selected')
        ->and(__('filament-icon-picker::icon-picker.all_sets'))->toBe('Other picker sets')
    ;
});
