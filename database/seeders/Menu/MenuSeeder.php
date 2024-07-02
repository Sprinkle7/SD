<?php

namespace Database\Seeders\Menu;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuCoverImage;
use App\Models\Menu\MenuTranslation;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            ['level' => 1,
                'info' => ['title' => 'advertising stand', 'description' => 'stand', 'language' => 'en'],
                'children' => 'stands'],
            ['level' => 1,
                'info' => ['title' => 'advertising wall', 'description' => 'wall', 'language' => 'en'],
                'children' => 'wall'],
            ['level' => 1,
                'info' => ['title' => 'advertising counter', 'description' => 'counter', 'language' => 'en'],
                'children' => 'counter'],
        ];
////but files not exist
        foreach ($menus as $men) {
            //first level menu
            $menu = Menu::create(['level' => $men['level'], 'thumbnail_image' => 't.jpg']);
            MenuCoverImage::create(['menu_id' => $menu['id'], 'path' => 't.jpg']);
            $men['info']['menu_id'] = $menu['id'];
            MenuTranslation::create($men['info']);
            //child menu
            for ($i = 1; $i < 4; $i++) {
                $child = Menu::create(['level' => 2, 'parent_id' => $menu['id'], 'thumbnail_image' => 't.jpg']);
                $data = $men['info'];
                $data['title'] = $men['children'] . $i;
                $data['menu_id'] = $child['id'];
                MenuCoverImage::create(['menu_id' => $child['id'], 'path' => 't.jpg']);
                MenuTranslation::create($data);
            }
        }
    }
}
