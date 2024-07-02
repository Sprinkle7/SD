<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //1
        $id = DB::table('roles')->insertGetId([]);
        DB::table('role_translations')->insert([
            'role_id' => $id,
            'title' => 'super admin',
            'language' => 'en',
        ]);

        //2
        $id = DB::table('roles')->insertGetId([]);
        DB::table('role_translations')->insert([
            'role_id' => $id,
            'title' => 'admin',
            'language' => 'en',
        ]);

        //3
        $id = DB::table('roles')->insertGetId([]);
        DB::table('role_translations')->insert([
            'role_id' => $id,
            'title' => 'customer',
            'language' => 'en',
        ]);


    }
}
