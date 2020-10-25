<?php
namespace app\common\upgrade;
use think\Cache;

class U12{
	public function up(){
	    delete_dir(RUNTIME_PATH.'temp');
	    Cache::clear();
	}
}