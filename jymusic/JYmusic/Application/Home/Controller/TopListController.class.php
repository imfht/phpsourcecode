<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;
/**
 * 测试控制器
 */
class TopListController extends HomeController {
    //获取音乐数据	
    public function index(){ 	
    	if (IS_AJAX) {
    		$map['position'] = 1;
			$list=M('Songs')->where($map)->field('id,name,music_url')->limit('20')->order('add_time desc')->select();
			if(count($list) > 0){
				foreach ($list as $v) {
					echo 'DATA("'.$v['name'].'","'.$v['music_url'].'","'.$v['id'].'");';
				}
			}
		}else{
			$this->show('页面出错');
		}		
    }  

}