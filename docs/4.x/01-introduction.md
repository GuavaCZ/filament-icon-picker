---
title: Introduction
---

# Introduction

Icon Picker adds an icon picker form field and a corresponding table column to your filament panels. You can pick from any blade-icons kit you have installed. Heroicons work out of the box, since filament ships with them.

This is useful whenever your users should pick an icon themselves. For example to customize the icons rendered on your frontend, to let users choose their own navigation icons, or to add small icons to their models for easier recognition.

Add the field to any schema:

```php
use Guava\IconPicker\Forms\Components\IconPicker;

IconPicker::make('icon');
```

And the column to any table:

```php
use Guava\IconPicker\Tables\Columns\IconColumn;

IconColumn::make('icon');
```

We store the full icon name (such as `heroicon-o-academic-cap`) in your database, which means you can render it anywhere with the regular blade-icons component.

## Version compatibility

| Filament version | Plugin version |
|------------------|:--------------:|
| 2.x              |      1.x       |
| 3.x              |      2.x       |
| 4.x              |      3.x       |
| 5.x              |      4.x       |

For older filament versions, please check the branch of the respective version.
