<?php
namespace app\common\upgrade;

class Ups{
	public function up(){
		 write_file(ROOT_PATH.'test.txt','测试升级');
	}
}