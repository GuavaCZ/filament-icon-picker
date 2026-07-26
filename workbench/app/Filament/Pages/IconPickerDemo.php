<?php

namespace Workbench\App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Guava\IconPicker\Forms\Components\IconPicker;

/**
 * Every configuration of the field on one page. The Pest suite never boots a panel, so
 * this is the only place the JS <-> Livewire round trip is exercised.
 *
 * @property-read Schema $form
 */
class IconPickerDemo extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Icon picker';

    protected static ?string $title = 'Icon picker';

    protected static ?string $slug = 'icon-picker';

    protected static ?int $navigationSort = 1;

    protected string $view = 'workbench::filament.pages.icon-picker-demo';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'dropdown' => 'heroicon-o-academic-cap',
            'inline' => 'heroicon-o-beaker',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dropdown vs inline')
                    ->description('The default is a dropdown; ->dropdown(false) renders the picker inline.')
                    ->columns(2)
                    ->schema([
                        IconPicker::make('dropdown')
                            ->label('Dropdown (default)'),
                        IconPicker::make('closeOnSelect')
                            ->label('Dropdown, closes on select')
                            ->closeOnSelect(),
                        IconPicker::make('inline')
                            ->label('Inline')
                            ->dropdown(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Search result views')
                    ->description('grid is the default; list adds labels; icons is the compact view.')
                    ->columns(3)
                    ->schema([
                        IconPicker::make('grid')
                            ->label('Grid')
                            ->gridSearchResults(),
                        IconPicker::make('list')
                            ->label('List')
                            ->listSearchResults(),
                        IconPicker::make('icons')
                            ->label('Icons (with tooltips)')
                            ->iconsSearchResults(),
                    ]),

                Section::make('Restrictions')
                    ->columns(2)
                    ->schema([
                        // Heroicons ship with Filament, so this set is always present.
                        IconPicker::make('limitedSets')
                            ->label('Limited to the heroicons set')
                            ->sets(['heroicons']),
                        IconPicker::make('disabled')
                            ->label('Disabled')
                            ->disabled(),
                    ]),

                Section::make('Custom icon uploads')
                    ->description('Unscoped uploads: any record can use them. Scoped uploads live on the Items page.')
                    ->schema([
                        IconPicker::make('customUploads')
                            ->label('Uploads enabled')
                            ->customIconsUploadEnabled(),
                    ]),
            ])
            ->statePath('data')
        ;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('dumpState')
                ->label('Dump state')
                ->icon(Heroicon::OutlinedCodeBracket)
                ->action(function (): void {
                    Notification::make()
                        ->title('Current form state')
                        ->body('<pre class="whitespace-pre-wrap text-xs">' . e(json_encode($this->form->getState(), JSON_PRETTY_PRINT)) . '</pre>')
                        ->persistent()
                        ->send()
                    ;
                }),
        ];
    }
}
