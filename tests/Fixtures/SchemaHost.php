<?php

namespace Guava\IconPicker\Tests\Fixtures;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

/**
 * Filling and validating a schema goes through the Livewire component, so tests need one.
 */
class SchemaHost extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function render(): string
    {
        return '<div></div>';
    }
}
