<?php

use Guava\IconPicker\Actions\UploadCustomIcon;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Tests\Fixtures\Post;
use Guava\IconPicker\Validation\VerifyIcon;
use Guava\IconPicker\Validation\VerifyIconScope;
use Illuminate\Support\Facades\Storage;

/**
 * Exposes the action's protected slug helpers.
 */
function slugs(): object
{
    return new class('upload-custom-icon') extends UploadCustomIcon
    {
        public function iconName(?string $label): string
        {
            return $this->getIconName($label);
        }

        public function bladeIconId(string $label, $scope): string
        {
            return $this->getBladeIconId($label, $scope);
        }
    };
}

beforeEach(function () {
    Storage::fake('public');

    $this->post = (new Post)->forceFill(['id' => 1]);
    $this->otherPost = (new Post)->forceFill(['id' => 2]);
});

it('strips path separators out of the icon name', function (string $label) {
    expect(slugs()->iconName($label))
        ->not->toContain('/')
        ->not->toContain('\\')
        ->not->toContain('.')
    ;
})->with([
    '../../evil',
    'a/../../evil',
    'foo/bar',
    '..',
    'foo.svg',
    '<script>alert(1)</script>',
]);

it('keeps a crafted label inside its own scope', function () {
    $id = slugs()->bladeIconId('../' . scopeOf($this->otherPost) . '/pwned', $this->post);

    expect($id)->toBe('_gfic_icons-' . scopeOf($this->post) . '.' . scopeOf($this->otherPost) . 'pwned');
});

it('resolves an ordinary label unchanged', function () {
    expect(slugs()->iconName('My Nice Icon'))->toBe('my-nice-icon')
        ->and(slugs()->bladeIconId('My Nice Icon', null))->toBe('_gfic_icons-unscoped.my-nice-icon')
    ;
});

it('does not resolve a custom icon that has no file behind it', function () {
    expect(IconManager::getIcon('_gfic_icons-unscoped.does-not-exist'))->toBeNull();
});

it('resolves a custom icon that does exist', function () {
    putCustomIcon('unscoped', 'logo');

    expect(IconManager::getIcon('_gfic_icons-unscoped.logo'))->not->toBeNull();
});

it('refuses to render another record\'s custom icon', function () {
    putCustomIcon(scopeOf($this->otherPost), 'secret');

    $field = IconPicker::make('icon')
        ->customIconsUploadEnabled()
        ->scopedTo($this->post)
    ;

    $id = '_gfic_icons-' . scopeOf($this->otherPost) . '.secret';

    expect(fetchIconSvg($field, $id))->toBeNull()
        ->and($field->verifyState($id))->toBeNull()
        ->and($field->resolveIcon($id))->toBeNull()
    ;
});

it('renders a custom icon from its own scope', function () {
    putCustomIcon(scopeOf($this->post), 'mine');

    $field = IconPicker::make('icon')
        ->customIconsUploadEnabled()
        ->scopedTo($this->post)
    ;

    $id = '_gfic_icons-' . scopeOf($this->post) . '.mine';

    expect($field->resolveIcon($id))->not->toBeNull()
        ->and(fetchIconSvg($field, $id))->toContain('<svg')
    ;
});

it('refuses a scoped custom icon on a field with no scope', function () {
    putCustomIcon(scopeOf($this->post), 'mine');

    $field = IconPicker::make('icon')->customIconsUploadEnabled();
    $id = '_gfic_icons-' . scopeOf($this->post) . '.mine';

    expect($field->resolveIcon($id))->toBeNull()
        ->and(fetchIconSvg($field, $id))->toBeNull()
    ;
});

it('attaches the scope rule even when the field has no scope', function () {
    $rules = collect(IconPicker::make('icon')->getValidationRules());

    expect($rules->contains(fn ($rule) => $rule instanceof VerifyIconScope))->toBeTrue()
        ->and($rules->contains(fn ($rule) => $rule instanceof VerifyIcon))->toBeTrue()
    ;
});

it('refuses an icon from a set the field does not offer', function () {
    $field = IconPicker::make('icon')->sets(['not-a-registered-set']);

    expect($field->resolveIcon('heroicon-o-academic-cap'))->toBeNull()
        ->and(fetchIconSvg($field, 'heroicon-o-academic-cap'))->toBeNull()
        ->and($field->verifyState('heroicon-o-academic-cap'))->toBeNull()
    ;
});

it('does not list icons from a set the field does not offer', function () {
    $field = IconPicker::make('icon')->sets(['not-a-registered-set']);

    expect(fetchIconIndex($field)['icons'])->toBeEmpty();
});

it('does not offer custom icons while uploads are disabled', function () {
    putCustomIcon('unscoped', 'logo');

    $field = IconPicker::make('icon');

    // The custom flag is the fourth element of each index tuple.
    expect(collect(fetchIconIndex($field)['icons'])->contains(fn (array $icon) => $icon[3] === 1))->toBeFalse()
        ->and($field->resolveIcon('_gfic_icons-unscoped.logo'))->toBeNull()
    ;
});

it('offers custom icons once uploads are enabled', function () {
    putCustomIcon('unscoped', 'logo');

    $field = IconPicker::make('icon')->customIconsUploadEnabled();

    expect($field->resolveIcon('_gfic_icons-unscoped.logo'))->not->toBeNull();
});
