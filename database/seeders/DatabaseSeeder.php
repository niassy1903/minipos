<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@minipos.test'],
            ['name' => 'Admin Mini POS', 'password' => Hash::make('password')],
        );

        $shop = Shop::updateOrCreate(
            ['slug' => 'boutique-centre'],
            ['name' => 'Boutique Centre', 'address' => 'Dakar, Sénégal', 'phone' => '+221 77 000 00 00'],
        );
        $shop->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);
        $shop->products()->upsert([
            ['shop_id' => $shop->id, 'name' => 'Café arabica 250g', 'reference' => 'CAF-250', 'price' => 3500, 'stock_quantity' => 24, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['shop_id' => $shop->id, 'name' => 'Thé vert bio', 'reference' => 'THE-001', 'price' => 2800, 'stock_quantity' => 4, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['shop_id' => $shop->id, 'name' => 'Miel de Casamance', 'reference' => 'MIEL-500', 'price' => 6000, 'stock_quantity' => 12, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['shop_id', 'reference'], ['name', 'price', 'stock_quantity', 'active', 'updated_at']);
        $shop->customers()->updateOrCreate(
            ['email' => 'awa@example.com'],
            ['name' => 'Awa Diop', 'phone' => '+221 77 111 22 33'],
        );
        $shop->customers()->updateOrCreate(
            ['email' => 'moussa@example.com'],
            ['name' => 'Moussa Fall', 'phone' => '+221 76 444 55 66'],
        );
    }
}
