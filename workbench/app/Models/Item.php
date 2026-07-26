<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workbench\Database\Factories\ItemFactory;

/**
 * A record that owns an icon, for exercising `IconColumn` and `scopedTo($record)`.
 */
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): ItemFactory
    {
        return ItemFactory::new();
    }
}
