<?php

return [
	"property" => [
		"tableName" => "install_worker",
		"maxLimit"=>10,
		"where"=>[["id",'>',23]],
		"orderBy"=>[["id","asc"]],
		"fields"=>["id","mobile"]

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
				'connectName'=>'mongo',
				"collection" => "test1",
				"relationKey" => ["id" => "a"],
				"fields"=>["b"],
				"orderBy"=>[["b","desc"]],
				"maxLimit"=>10,
			]

		],
		'redisds'=>[
			"property" => [
				'connectType'=>'redis',
				'connectName'=>'redis',
				"relationKey" => "id",

			]
		],
		'excelds'=>[
			"property" => [
				'connectType'=>'excel',
				'fullFileName'=>'/home/www/dctest_la/t_excel.xlsx',
				"relationKey" => ["id" => "id"],

			]
		],
	]


];