<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-21
 * Time: 上午10:36
 */

class ProjectRoleTableSeeder extends Seeder
{
    public function run(){
        DB::table('projectRoles')->delete();

        DB::table('projectRoles')->insert([
            ['name'=>'member', 'label'=>'普通成员'],
            ['name'=>'manager', 'label'=>'管理员']
        ]);
    }
}