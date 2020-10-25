<?php
class demoApi extends baseApi{
	
	public function getMenu(){
		return array(
					'sort'=>1,
					'title'=>'示例APP',
					'url'=>url('demo/index/demolist'),
					'list'=>array(
						'添加示例APP'=>url('demo/index/demoadd'),
						'示例APP列表'=>url('demo/index/demolist'),
						'配置示例APP'=>url('demo/index/democonfig'),
					)
			);
	}
	
	public function Reply($ppid,$data){
		return '这是示例APP的回复';
	}
	
}