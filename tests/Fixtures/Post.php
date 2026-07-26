<?php

namespace Guava\IconPicker\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * Stands in for a user's model when exercising scoped custom icons. Never hits the
 * database — the scope hash only needs a morph class and a key.
 */
class Post extends Model
{
    protected $guarded = [];
}
