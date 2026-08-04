---
title: Translations
---

# Translations

The package ships with English, German and Czech translations.

Everything the picker renders goes through them, which includes the placeholder of the field, the labels of the upload action and the validation messages.

## Publishing the translations

If you want to change any of the texts, publish the language files:

```bash
php artisan vendor:publish --tag=filament-icon-picker-translations
```

They end up in `lang/vendor/filament-icon-picker` and take precedence over ours.

## Adding a language

If you translate the package into a language we don't ship yet, a PR is very welcome. Copy `resources/lang/en` to a directory with your locale, translate the three files in it, and open a pull request. See [contributing](../04-contributing.md).

## Changing a single text

For most of the texts you don't need the language files at all, since the options are on the field itself:

```php
IconPicker::make('icon')
    ->placeholder('Pick an icon')
    ->searchPrompt('Search icons by name')
    ->noSearchResultsMessage('No icon matches this name.');
```

Publishing is the better option when you want the change everywhere, without repeating it on every field.
