<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\App\Models\Item;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The panel auto-authenticates as this user, see AutoLogin middleware.
        User::factory()->create([
            'name' => 'Workbench User',
            'email' => 'workbench@example.com',
        ]);

        Item::factory()->count(5)->create();
        Item::factory()->withoutIcon()->create(['name' => 'Item without an icon']);
    }
}
