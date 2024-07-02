<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'nima',
            'last_name' => 'nima',
            'gender' => '1',
            'email'=>'test@test.com',
            'phone' => '8945612357',
            'password' => bcrypt('123456'),
            'company' => 'test',
            'address' => 'street.er 34',
            'additional_address' => null,
            'postcode' => 12345,
            'city' => 'test',
            'country_id' => 1,
            'role_id' => 1,
        ]);
    }
}
