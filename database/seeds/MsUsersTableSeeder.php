<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class MsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('ms_user')->insert([
            'username' => "スマート",
            'tel' => '(555)-555-5555',
            'email' => "noreply.sun.sun33@gmail.com",
            'gender' => 'female',
            'birth_year' => '2020',
            'password' => bcrypt('pass'),
            'user_type' => config('const.auth.permission.ADMIN')
        ]);

        // for ($i=0; $i < 100; $i++) { 
        //     DB::table('ms_user')->insert([
        //         'username' => Str::random(10),
        //         'tel' => '(555)-555-5555',
        //         'email' => Str::random(10).'@gmail.com',
        //         'gender' => 'female',
        //         'birth_year' => '2020',
        //         'password' => bcrypt('pass'),
        //         //'user_type' => config('const.auth.permission.ADMIN')
        //     ]);
        // }


    }
}
