<?php

namespace Workbench\App\Filament\Pages;

use Filament\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tables\Columns\IconColumn;
use Workbench\App\Models\Item;

/**
 * The model-backed half of the sandbox: IconColumn, and a picker scoped to the record.
 */
class Items extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Items';

    protected static ?string $title = 'Items';

    protected static ?string $slug = 'items';

    protected static ?int $navigationSort = 2;

    protected string $view = 'workbench::filament.pages.items';

    public function table(Table $table): Table
    {
        return $table
            ->query(Item::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('icon')
                    ->label('Icon (default size)')
                    ->placeholder(Heroicon::OutlinedNoSymbol),
                // Distinct name on purpose: two columns called `icon` collide.
                IconColumn::make('iconSmall')
                    ->label('Icon (small, coloured)')
                    ->state(fn (Item $record): ?string => $record->icon)
                    ->size('sm')
                    ->color('primary'),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema(fn (Item $record): array => [
                        IconPicker::make('icon')
                            // Uploads here are prefixed with a hash of this record.
                            ->scopedTo($record)
                            ->customIconsUploadEnabled()
                            ->listSearchResults(),
                    ]),
            ])
        ;
    }
}
