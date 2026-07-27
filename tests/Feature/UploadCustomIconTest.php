<?php

use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tests\Fixtures\Post;
use Guava\IconPicker\Tests\Fixtures\SchemaHost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

const VALID_SVG = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0"/></svg>';

function upload(string $contents): TemporaryUploadedFile
{
    $file = UploadedFile::fake()->createWithContent('icon.svg', $contents);
    $name = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);

    Storage::disk(FileUploadConfiguration::disk())
        ->putFileAs(FileUploadConfiguration::directory(), $file, $name)
    ;

    return TemporaryUploadedFile::createFromLivewire($name);
}

/**
 * Builds the upload action's schema the same way the field does.
 */
function uploadSchema(?Post $scope = null): Schema
{
    $livewire = new SchemaHost;

    $field = IconPicker::make('icon')
        ->customIconsUploadEnabled()
        ->scopedTo($scope)
    ;

    $field = Schema::make($livewire)->components([$field])->getComponents()[0];

    return $field->getHintActions()[0]
        ->schemaComponent($field)
        ->getSchema(Schema::make($livewire)->statePath('data'))
    ;
}

function fill(Schema $schema, string $label, TemporaryUploadedFile $file): Collection
{
    $schema->fill(['label' => $label]);

    $components = collect($schema->getComponents())->keyBy(fn ($component) => $component->getName());

    // FileUpload state is a map of uuid => upload, which `fill()` does not build for us.
    $components['file']->state(['upload' => $file]);

    return $components;
}

function submit(Schema $schema, string $label, TemporaryUploadedFile $file): string
{
    $components = fill($schema, $label, $file);

    $components['file']->saveUploadedFiles();

    return $components['file']->getState();
}

/**
 * @return array<string, array<string>>
 */
function errorsFor(Schema $schema, string $label, TemporaryUploadedFile $file): array
{
    fill($schema, $label, $file);

    try {
        $schema->validate();
    } catch (ValidationException $exception) {
        return $exception->errors();
    }

    return [];
}

beforeEach(function () {
    Storage::fake('public');
    Storage::fake(FileUploadConfiguration::disk());

    $this->post = (new Post)->forceFill(['id' => 1]);
    $this->otherPost = (new Post)->forceFill(['id' => 2]);
});

it('writes only sanitized markup to disk', function () {
    $malicious = '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">'
        . '<script>alert(document.cookie)</script>'
        . '<path d="M0 0"/>'
        . '</svg>';

    $path = submit(uploadSchema(), 'My Icon', upload($malicious));

    $stored = Storage::disk('public')->get($path);

    expect($stored)
        ->not->toContain('script')
        ->not->toContain('onload')
        ->not->toContain('alert')
        ->toContain('d="M0 0"')
    ;
});

it('stores the icon under its own scope directory', function () {
    $path = submit(uploadSchema($this->post), 'My Icon', upload(VALID_SVG));

    $scopeId = md5("{$this->post->getMorphClass()}::{$this->post->getKey()}");

    expect($path)->toBe("icon-picker-icons/{$scopeId}/my-icon.svg");
});

it('cannot be talked into writing outside its scope directory', function () {
    $otherScope = md5("{$this->otherPost->getMorphClass()}::{$this->otherPost->getKey()}");

    $path = submit(uploadSchema($this->post), "../{$otherScope}/pwned", upload(VALID_SVG));

    $scopeId = md5("{$this->post->getMorphClass()}::{$this->post->getKey()}");

    expect($path)->toStartWith("icon-picker-icons/{$scopeId}/")
        ->and(Storage::disk('public')->allFiles("icon-picker-icons/{$otherScope}"))->toBeEmpty()
    ;
});

it('rejects a label that is not letters, numbers and spaces', function (string $label) {
    expect(errorsFor(uploadSchema(), $label, upload(VALID_SVG)))->toHaveKey('data.label');
})->with([
    '../../evil',
    'foo/bar',
    'foo.svg',
    '<script>',
    '..',
]);

it('accepts an ordinary label', function () {
    expect(errorsFor(uploadSchema(), 'My Nice Icon 2', upload(VALID_SVG)))->toBeEmpty();
});

it('rejects a label whose icon already exists in this scope', function () {
    submit(uploadSchema($this->post), 'My Icon', upload(VALID_SVG));

    expect(errorsFor(uploadSchema($this->post), 'My Icon', upload(VALID_SVG)))
        ->toHaveKey('data.label')
    ;
});

it('allows the same label in a different scope', function () {
    submit(uploadSchema($this->post), 'My Icon', upload(VALID_SVG));

    expect(errorsFor(uploadSchema($this->otherPost), 'My Icon', upload(VALID_SVG)))->toBeEmpty();
});

it('rejects a file that is not usable as an svg', function () {
    expect(errorsFor(uploadSchema(), 'My Icon', upload('<html><body>nope</body></html>')))
        ->toHaveKey('data.file')
    ;
});

it('makes the uploaded icon resolvable through the field', function () {
    submit(uploadSchema($this->post), 'My Icon', upload(VALID_SVG));

    $scopeId = md5("{$this->post->getMorphClass()}::{$this->post->getKey()}");
    $id = "_gfic_icons-{$scopeId}.my-icon";

    $field = IconPicker::make('icon')
        ->customIconsUploadEnabled()
        ->scopedTo($this->post)
    ;

    expect($field->resolveIcon($id))->not->toBeNull()
        ->and($field->getIconSvgJs($id))->toContain('<svg')
    ;
});
