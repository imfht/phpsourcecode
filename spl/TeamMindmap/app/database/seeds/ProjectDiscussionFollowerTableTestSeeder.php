<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 14-12-25
 * Time: 上午10:50
 */

/**
 * 项目讨论指定关注人(关注人范围为当前项目),被指定关注人被希望参与本次i讨论
 * Class ProjectDiscussionFollowerTableTest
 */
class ProjectDiscussionFollowerTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('projectDiscussion_follower')->insert([
            ['projectDiscussion_id' => 1, 'follower_id' => 2],
            ['projectDiscussion_id' => 2, 'follower_id' => 1],
            ['projectDiscussion_id' => 3, 'follower_id' => 2]
        ]);
    }

}