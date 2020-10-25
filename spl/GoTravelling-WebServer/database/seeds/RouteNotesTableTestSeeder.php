<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-2
 * Time: 下午3:20
 */

use Illuminate\Database\Seeder;

class RouteNotesTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('routeNotes')->delete();

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('毕业游')),
            'content' => '我们的毕业游第一站',
            'images' => ['image1.png', 'image2.png'],
            'loc_name' => '大东海',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [18.229114, 109.531836]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('毕业游')),
            'content' => '非请勿扰，亚龙湾',
            'images' => ['image3.png'],
            'loc_name' => '亚龙湾热带森林公园',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [18.236153, 109.654556]
            ],
            'created_at' => $this->getTestTime()
        ]);
        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('北京游')),
            'content' => '帝都雾霾',
            'images' => ['image4.png'],
            'loc_name' => '天安门广场',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [39.912733, 116.404015]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('北京游')),
            'content' => '找到度娘的总部了',
            'images' => [],
            'loc_name' => '百度大厦',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [40.056968, 116.307689]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('随便游')),
            'content' => '随便就来到了鹅厂总部',
            'images' => [],
            'loc_name' => '腾讯大厦',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [39.982809, 116.343643]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('广州一天随便游')),
            'content' => '随便地走走',
            'images' => [],
            'loc_name' => '太古汇',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [23.139931, 113.337713]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('随便游')),
            'content' => '我不是来看月亮的',
            'images' => [],
            'loc_name' => '月亮湾公园',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [20.071631, 110.327799]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('广州二日游')),
            'content' => '长隆真好玩',
            'images' => ['image1.png', 'image2.png'],
            'loc_name' => '长隆水上乐园-正门入口',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [23.005748, 113.330555]
            ],
            'created_at' => $this->getTestTime()
        ]);

        DB::table('routeNotes')->insert([
            'route_id' => head(\App\Route::getRouteIdOnName('广州二日游')),
            'content' => '等车等了很久',
            'images' => ['image1.png', 'image2.png'],
            'loc_name' => '海珠湖北门',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [23.082943, 113.328659]
            ],
            'created_at' => $this->getTestTime()
        ]);
    }

    private function getTestTime()
    {
        static $timeOffset = 0;

        $time = time() + $timeOffset;
        $timeOffset += 1;
        return $time;
    }
}