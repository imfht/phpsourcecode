<?php


namespace tests\Cmd;


class MpPlatformModel_Test extends \tests\Cmd\MpPlatformTable
{

	const CON_A = 'a';
	const CON_B = 'b';

	/****** user define columns ******/
	protected static $columns = [
		'id'          => [],
		'parent_id'   => ['edit' => 'select',],
		'access_name' => ['edit' => 'text','require' => 1,'unique' => 1,],
		'name'        => ['edit' => 'text','require' => 1,'options' => [0 => self::CON_A,1 => self::CON_B,],],
		'created_at'  => ['showTable' => false,],
		'updated_at'  => ['showTable' => false,],
	];
	/****** user define columns ******/

}