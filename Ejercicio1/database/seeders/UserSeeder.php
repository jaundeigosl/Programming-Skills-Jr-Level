<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name'=>'admin',
            'surname'=>'batman',
            'email'=>'admin@gmail.com',
            'password'=>'testroot',
            'city'=>'valencia',
            'country'=>'venezuela',
            'birth'=>'2014-1-1'
        ]);

        DB::table('users')->insert([
            'name'=>'Noadmin',
            'email'=>'noadmin@gmail.com',
            'password'=>'noroot',
            'city'=>'valencia',
            'country'=>'venezuela',
            'birth'=>'2014-1-1'
        ]);
    }
}
