<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-18
 * Time: 下午7:58
 */
use Illuminate\Database\Seeder;

class RouteTagTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tags')->delete();

        DB::table('tags')->insert([
            'label' => 'entertaining',
            'name' => '畅玩'
        ]);

        DB::table('tags')->insert([
            'label' => 'watching',
            'name' => '观光'
        ]);

        DB::table('tags')->insert([
            'label' => 'eating',
            'name' => '美食'
        ]);
    }
}