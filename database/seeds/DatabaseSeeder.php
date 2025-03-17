<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MsUsersTableSeeder::class);
        $this->call(MskubunTableSeeder::class);
        $this->call(MsSettingTableSeeder::class);
    }
}
