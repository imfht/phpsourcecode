<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class ArtistTypeController extends AdminController {
    public function index(){
		$Artisttype	=   D('ArtistType');
        $list = $this->lists($Artisttype,null,'id desc',null);
        $this->assign('list', $list);
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->meta_title = '艺术家类型管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $Artisttype	= D('ArtistType');
            $data = $Artisttype->create();
            if($data){
                $id = $Artisttype->add();
                if($id){
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Artisttype->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
			$this->meta_title = '添加艺术家类型';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $Artisttype	= D('ArtistType');
            $data = $Artisttype->create();
            if($data){
                if($Artisttype->save()!== false){
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Artisttype->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('ArtistType')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改艺术家类型';
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
        if(M('ArtistType')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}