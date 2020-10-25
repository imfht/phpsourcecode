<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class ServerController extends AdminController {
    public function index($status = null){
		$Server =   D('Server');
		$map['status'] = '1';
        //缓存服务器组
        $list = S('serverList');
        if (!$list){
        	$list = $this->lists($Server,$map,'id desc',null);
        }
        $this->assign('list', $list);
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->meta_title = '服务器管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $server	= D('Server');
            $data = $server->create();
            if($data){
                $id = $server->add();
                if($id){
                	S('serverList',null);
                    $this->success('新增成功',U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($$server->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
			$this->meta_title = '添加服务器';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $server	= D('Server');
            $data = $server->create();
            if($data){
                if($server->save()!== false){
                	S('serverList',null);
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($server->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('Server')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改服务器';
			$this->display('add');
        }
	}
	
	/**
     * 删除
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Server')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            S('serverList',null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    /*设置默认服务器*/
    
    function defaultServer() {
    	$id =  I('get.id');
        $data['value'] = $id;
    	$map['name'] = 'DT_SERVER_ID';
    	$data = M('Config')->where($map)->save($data);
        if($data){
        	S('DB_CONFIG_DATA',null);
        	//记录行为
        	action_log('update_config','config',$data['id'],UID);
   			$this->success('设置成功', Cookie('__forward__'));
    	} else {
        	$this->error('设置失败');
    	}    	
    }
}