<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class  RecycleController extends AdminController {
    public function index(){
    	$map['status']   = -1;  	
		$list = $this->lists('Songs',$map,'id desc','id,name,album_name,artist_name,genre_name,listens,add_time');
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list);
        $this->meta_title = '回收站管理';
        $this->display();
	}
	
    //还原被删除的数据
    public function permit(){
        /*参数过滤*/
        $ids = I('param.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        /*拼接参数并修改状态*/
        $Model  =   'Songs';
        $map    =   array();
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $this->restore($Model,$map);
    }
		
	// 清空回收站
    public function clear(){
        $res = D('Songs')->remove();
        if($res !== false){
            $this->success('清空回收站成功！');
        }else{
            $this->error('清空回收站失败！');
        }
    }
			
	public function editData(){

		
	}

}