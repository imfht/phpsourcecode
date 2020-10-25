<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-23
 * Time: 下午9:34
 */
use Illuminate\Database\Seeder;

class CollectionTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('collections')->delete();

        DB::table('collections')->insert([
            'creator_id' => \App\User::first()['_id'],
            'province' => 'guangdongsheng',
            'city' => 'guangzhoushi',
            'zone' => 'tianhequ',
            'name' => 'scnu',

            'loc' => ['type' => 'Point', 'coordinates' => [40, 5]],
            'address' => 'guangdongsheng-guangzhoushi-tianhequ-scnu',
            'label' => 'mySchool'
        ]);

// 测试特填数据－－－－

        DB::table('collections')->insert([
            'creator_id' => \App\User::where('username', 'test')->first()->toArray()['_id'],
            'province' => '广东',
            'city' => '广州',
            'zone' => '番禺',
            'name' => '长隆水上乐园-正门入口',

            'loc' => ['type' => 'Point', 'coordinates' => [23.005748, 113.330555]],
            'address' => '广州市番禺区迎宾路长隆旅游度假区长隆水上乐园内',
            'label' => '长隆'
        ]);

        DB::table('collections')->insert([
            'creator_id' => \App\User::where('username', 'test')->first()->toArray()['_id'],
            'province' => '广东',
            'city' => '广州',
            'zone' => '番禺',
            'name' => '玛丽莲甜品第三金碧店',

            'loc' => ['type' => 'Point', 'coordinates' => [23.071389, 113.294706]],
            'address' => '第三金碧花园74/79幢030铺',
            'label' => '甜品店'
        ]);

        DB::table('collections')->insert([
            'creator_id' => \App\User::where('username', 'test')->first()->toArray()['_id'],
            'province' => '广东',
            'city' => '深圳',
            'zone' => '福田',
            'name' => '欢乐谷-入口',

            'loc' => ['type' => 'Point', 'coordinates' => [22.545575, 113.985906]],
            'address' => '深圳市南山区深圳欢乐谷内',
            'label' => '欢乐谷'
        ]);
   
    }
}