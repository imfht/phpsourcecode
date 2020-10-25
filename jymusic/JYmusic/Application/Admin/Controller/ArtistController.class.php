<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class ArtistController extends AdminController {
    public function index($status = null,$title = null){
		$Artist	=   D('Artist');
        /* 查询条件初始化 */
        $map = null;
        if(isset($title)){
            $map['name']   =   array('like', '%'.$title.'%');
        }
        //只查询pid为0的文章
        //$map['pid'] = 0;
        $list = $this->lists($Artist,$map,'id desc','id,name,type_name,region,hits,sort,recommend,rater,add_time,status');
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '歌手管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $Artist	= D('Artist');
            $data = $Artist->create();
            if($data){
                $id = $Artist->add();
                if($id){
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Artist->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
			$this->meta_title = '添加歌手';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $Artist	= D('Artist');
            $data = $Artist->create();
            if($data){
                if($Artist->save()!== false){
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Artist->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('Artist')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改歌手';
			$this->display('add');
        }
	}
	
	/**
     * 删除
     */
    public function del(){
        $id = array_unique((array)I('ids',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Artist')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}