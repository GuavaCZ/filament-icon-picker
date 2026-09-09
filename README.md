<img src="https://github.com/GuavaCZ/filament-icon-picker/raw/main/.github/banner.png" alt="filament-icon-picker Banner" class="filament-hidden">

# Icon Picker for your filament panels

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guava/filament-icon-picker.svg?style=flat-square)](https://packagist.org/packages/guava/filament-icon-picker)
[![Total Downloads](https://img.shields.io/packagist/dt/guava/filament-icon-picker.svg?style=flat-square)](https://packagist.org/packages/guava/filament-icon-picker)

This plugin adds a new icon picker form field and a corresponding table column. You can use it to select from any blade-icons kit that you have installed. By default, heroicons are supported since it is shipped with Filament.

This can be useful for when you want to customize icons rendered on your frontend, if you want your users to be able to customize navigation icons, add small icons to their models for easy recognition and similar.

## Documentation

The full documentation is available at [guava.cz](https://guava.cz/developers/packages/filament-icon-picker?ref=github&utm_campaign=icon-picker).

## Version compatibility

| Filament version | Plugin version |
|------------------|:--------------:|
| 2.x              |      1.x       |
| 3.x              |      2.x       |
| 4.x              |      3.x       |
| 5.x              |    4.x, 5.x    |

Both 4.x and 5.x run on filament 5. 5.x is the recommended version, it contains several performance improvements. See the [upgrade guide](https://guava.cz/developers/packages/filament-icon-picker/5.x/upgrading?ref=github&utm_campaign=icon-picker).

For older filament versions, please check the branch of the respective version.

## Installation

You can install the package via composer:

```bash
composer require guava/filament-icon-picker
```

Make sure to publish the package assets using:

```bash
php artisan filament:assets
```

Finally, make sure you have a **custom filament theme** (read [here](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) how to create one) and add the following to your **theme.css** file so the CSS is properly built:

```css
@source '../../../../vendor/guava/filament-icon-picker/resources/**/*';
```

The package serves icons through two automatically registered endpoints (`/_icon-picker/index` and `/_icon-picker/svgs`), secured by an encrypted per-field token. To tweak caching, the route prefix or middleware, publish the config file:

```bash
php artisan vendor:publish --tag=filament-icon-picker-config
```

For the remaining setup steps, please see the [installation docs](https://guava.cz/developers/packages/filament-icon-picker/5.x/installation?ref=github&utm_campaign=icon-picker).

## Usage

Add the icon picker to any form schema:

```php
use Guava\IconPicker\Forms\Components\IconPicker;

IconPicker::make('icon');
```

And the column to any table:

```php
// Make sure this is the correct import, not the filament one
use Guava\IconPicker\Tables\Columns\IconColumn;

IconColumn::make('icon');
```

Everything else, including search result views, limiting icon sets and letting your users upload their own icons, is covered in the [documentation](https://guava.cz/developers/packages/filament-icon-picker).

## Translations

Translations use the `guava-icon-picker` namespace to avoid collisions with other icon-picker packages.
Publish them with:

```bash
php artisan vendor:publish --tag=guava-icon-picker-translations
```

To customize translations, edit `lang/vendor/guava-icon-picker/{locale}` (or
`resources/lang/vendor/guava-icon-picker/{locale}` if your application uses that language directory).
If you previously published Guava translations under `vendor/filament-icon-picker`, copy your Guava
overrides to `vendor/guava-icon-picker`. The old namespace and publishing tag remain registered for
existing application translation calls, but the package's own components now use the unique namespace.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lukas Frey](https://github.com/lukas-frey)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
