<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-21
 * Time: 下午12:20
 */

class ProjectTableTestSeeder extends Seeder{
	public function run()
    {
        DB::table('projects')->delete();

		DB::table('projects')->insert([
			['name'=>'example', 'cover'=>'fa-paper-plane', 'introduction'=>'example and example', 'creater_id'=>'1', 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')],
		]);

        DB::table('projects')->insert(
          ['name'=>'example2', 'cover'=>'fa-gear', 'introduction'=>'example2 and example2', 'creater_id'=>'2', 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')]
        );

	}


}