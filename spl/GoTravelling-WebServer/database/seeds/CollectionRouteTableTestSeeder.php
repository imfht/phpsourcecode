<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-11
 * Time: 上午10:58
 */
use Illuminate\Database\Seeder;

class CollectionRouteTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('collectionRoutes')->delete();

        $this->executeInsert(2, 1, '毕业游');
        $this->executeInsert(2, 1, '广州一天随便游');
        $this->executeInsert(2, 1, '随便游');
    }

    /**
     * 辅助方法，通过下标获取用户的id
     * @param int $index 目标用户在列表中的位置下标
     * @return mixed 
     */
    protected function getSeedUserId($index)
    {
        return \App\User::all()->toArray()[$index - 1]['_id'];
    }

    /**
     * 辅助方法，进行数据插入
     * @param int $ownerIndex 收藏者的下标
     * @param int $creatorIndex 路线创建者的下标
     * @param string $routeName 被收藏的路线的名称
     */
    protected function executeInsert($ownerIndex, $creatorIndex, $routeName)
    {
        $seedRoute = head(\App\Route::getRoutesOnKeyword($routeName));
        DB::table('collectionRoutes')->insert([
            'owner_id' => $this->getSeedUserId($ownerIndex),
            'route_id' => $seedRoute['_id'],
            'creator_id' => $this->getSeedUserId($creatorIndex),
            'name' => $seedRoute['name'],
            'description' => $seedRoute['description'],
            'created_at' => time()
        ]);
    }
}