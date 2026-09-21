<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Reuse an existing seller if you already registered one; otherwise
        // create a demo seller so this seeder works on a fresh database.
        $seller = User::where('role', 'seller')->first() ?? User::create([
            'name' => 'Demo Seller',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'store_name' => 'Amber & Oak',
        ]);

        $products = [
            ['name' => 'Walnut writing desk', 'category' => 'Home & Living', 'price' => 420.00, 'original_price' => 540.00, 'image' => 'images/products/walnut-desk.jpg'],
            ['name' => 'Ceramic pour-over set', 'category' => 'Ceramics', 'price' => 54.00, 'original_price' => 68.00, 'image' => 'images/products/pour-over-set.jpg'],
            ['name' => 'Brass desk lamp', 'category' => 'Lighting', 'price' => 112.00, 'original_price' => 142.00, 'image' => 'images/products/brass-lamp.jpg'],
            ['name' => 'Wool throw blanket', 'category' => 'Textiles', 'price' => 76.00, 'original_price' => 96.00, 'image' => 'images/products/wool-throw.jpg'],
            ['name' => 'Linen napkin set', 'category' => 'Textiles', 'price' => 28.00, 'original_price' => 36.00, 'image' => 'images/products/linen-napkins.jpg'],
            ['name' => 'Leather notebook', 'category' => 'Stationery', 'price' => 22.00, 'original_price' => null, 'image' => 'images/products/leather-notebook.jpg'],
            ['name' => 'Oak side table', 'category' => 'Home & Living', 'price' => 180.00, 'original_price' => 210.00, 'image' => 'images/products/oak-side-table.jpg'],
            ['name' => 'Rattan pendant shade', 'category' => 'Lighting', 'price' => 64.00, 'original_price' => null, 'image' => 'images/products/rattan-shade.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product + ['user_id' => $seller->id]);
        }
    }
}