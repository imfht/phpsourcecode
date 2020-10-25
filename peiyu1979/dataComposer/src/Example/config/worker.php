<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2017/12/30
 * Time: 17:20
 */

return [
	"property" => [
		"tableName" => "install_worker",
		"maxLimit"=>10,
		"where"=>[["id",'>','{$v}']],
		"orderBy"=>[["id","asc"]],
		"fields"=>["id","mobile"],

	],
	"dataSource" => [
		"category" => [
			"property" => [
				"tableName" => "worker_category",
				"relationKey" => ["id" => "worker_id"],
				"fields"=>["id"]
			],
			"dataSource" => [
				"category_one_info"=>[
					"property" => [
						"tableName" => "install_category",
						"relationKey" => ["category_one" => "cate_id"],
						"fields"=>["cate_name"]
					],
				],
				"category_two_info"=>[
					"property" => [
						"tableName" => "install_category",
						"relationKey" => ["category_two" => "cate_id"],
						"fields"=>["cate_name"]
					],
				],
			]
		],
		"area" => [
			"property" => [
				"tableName" => "worker_admin_division",
				"relationKey" => ["id" => "worker_id"],
			]
		],
		'mongods'=>[
			"property" => [
				'connectType'=>'mongo',
				'connectstring'=>'mongo',
				"collection" => "test1",
				"relationKey" => ["id" => "a"],
				"fields"=>["b"],
				"orderBy"=>[["b","desc"]],
			]

		],
		'redisds'=>[
			"property" => [
				'connectType'=>'redis',
				'connectName'=>'default',
				"relationKey" => "id",

			]
		],
		'apids'=>[
			"property" => [
				'connectType'=>'api',
				'url'=>'http://192.168.203.115:10799/test',
				'method'=>'get',
				'options'=>[],
				"relationKey" => ["id" => "a"],
				'callback'=>['end'=>['\App\Http\Controllers\TestController','cb']]
			]
		],
	]


];