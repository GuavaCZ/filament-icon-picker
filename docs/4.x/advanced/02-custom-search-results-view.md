---
title: Custom search results view
---

# Custom search results view

If none of the three built in views fit, you can render the results with your own blade view:

```php
IconPicker::make('icon')
    ->searchResultsView('icon-picker.my-results');
```

You can also pass extra data to it:

```php
IconPicker::make('icon')
    ->searchResultsView('icon-picker.my-results', [
        'columns' => 4,
    ]);
```

The field itself is always passed to the view as `$field`, so you don't have to pass it yourself.

## What you have to work with

The results are rendered by alpine, not by blade, because the search runs in the browser. So your view is a template that alpine fills in.

These are the things you can use:

| Name | What it is |
|------|------------|
| `resultsVisible` | The icons to render right now, an array of `{ id, label, custom }` |
| `state` | The ID of the currently selected icon |
| `isLoading` | Whether the icons of a set are still being loaded |
| `updateState(icon)` | Selects an icon, pass it one entry of `resultsVisible` |
| `setElementIcon($el, id)` | Loads the SVG of an icon into an element |
| `addSearchResultsChunk()` | Renders the next chunk of results |

Icons are not sent to the browser as SVG, only as their ID and label. To draw one, call `setElementIcon` on the element it should end up in. Doing it when the element scrolls into view keeps large sets fast:

```blade
<div x-intersect="setElementIcon($el, icon.id)"></div>
```

## A minimal example

```blade
<div class="grid grid-cols-4 gap-2 max-h-96 overflow-scroll">
    <template x-for="icon in resultsVisible" :key="icon.id">
        <div
            role="button"
            x-show="! isLoading"
            x-on:click.prevent="updateState(icon)"
            x-bind:class="{ 'ring-2': state == icon.id }"
            class="flex flex-col items-center gap-2 p-4 rounded-lg ring-1 ring-gray-950/10 dark:ring-white/20"
        >
            <div x-intersect="setElementIcon($el, icon.id)"></div>
            <span x-text="icon.label" class="text-sm text-gray-500"></span>
        </div>
    </template>

    <div x-intersect="addSearchResultsChunk" class="col-span-full"></div>
</div>
```

The last element is what loads more results as your user scrolls. If you leave it out, only the first chunk is ever rendered.

> [!NOTE]
> Your view lives in your own application, so please make sure its classes are picked up by your filament theme. The `@source` line from the [installation](../02-installation.md) page only covers the views that ship with the package.

If you want to start from one of ours, the built in views are in `resources/views/search-results` in the package.
