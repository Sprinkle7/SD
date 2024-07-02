<?php

namespace Database\Seeders\Product;

use App\Models\Menu\Menu;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;

class MenuPtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::where('type', 2)->pluck('id', 'id')->toArray();
        $menus = Menu::where('level', 2)->pluck('id')->toArray();
        foreach ($menus as $menu) {
            $rndproductsIndex = array_rand($products, 15);
            if (!is_array($rndproductsIndex)) {
                $rndproductsIndex = [$rndproductsIndex];
            }
            Menu::find($menu)->products()->sync($rndproductsIndex);
        }
    }
}
