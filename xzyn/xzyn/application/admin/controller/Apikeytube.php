<?php
namespace app\admin\controller;

use expand\Str;

class Apikeytube extends Common {
    public function initialize(){
        parent::initialize();
//		p('Index');

    }

    public function index() {
    	$t = [3323,'tttt','5445ggfg'];



		$r = Str::autoCharset($t);

		p($r);

//      return $this->fetch();
    }

    public function create() {	//新增
    	$t = [3323,'tttt','5445ggfg'];

		$r = Str::autoCharset($t);

		p($r);

//      return $this->fetch();
    }

    public function edit() {	//编辑
    	$t = [3323,'tttt','5445ggfg'];

		$r = Str::autoCharset($t);

		p($r);

//      return $this->fetch();
    }

    public function delete() {	//删除
    	$t = [3323,'tttt','5445ggfg'];

		$r = Str::autoCharset($t);

		p($r);

//      return $this->fetch();
    }


}
