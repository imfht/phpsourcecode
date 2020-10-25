<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SystemConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        // 插入到数据库中
        \App\Models\SystemConfig::insert([
            [
                'flag' => 'test',
                'title' => '测试配置项',
                'system_config_group' => 'test_group',
                'value' => '20000',
                'description' => '测试配置项',
                'weight' => 10,
                'enable' => 'T',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
