<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class MsSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_setting')->insert([
            'ms_setting_id' => "1",
            'accommodation_flg' => '1'
        ]);
    }
}