<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class GenreController extends AdminController {
    public function index($status = null){
		$Genre	=   D('Genre');
        /* 查询条件初始化 */
        $list = $this->lists($Genre,$map=null,'id desc','id,name,pid,recommend,add_time');
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '曲风管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $Genre	= D('Genre');
            $data = $Genre->create();
            if($data){
                $id = $Genre->add();
                if($id){
                	S('genre',null);
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Genre->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $genres = M('Genre')->field(true)->select();
            $genres = D('Common/Tree')->toFormatTree($genres,$title = 'name');
            $genres = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级曲风')), $genres);
            $this->assign('Genres', $genres);
			$this->meta_title = '添加曲风';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $Genre	= D('Genre');
            $data = $Genre->create();
            if($data){
                if($Genre->save()!== false){
                	S('genre',null);
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Genre->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('Genre')->field(true)->find($id);
            $genres = M('Genre')->field(true)->select();
            $genres = D('Common/Tree')->toFormatTree($genres,$title = 'name');
            $genres = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级曲风')), $genres);
            $this->assign('Genres', $genres);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改曲风';
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
        if(M('Genre')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
	
}
