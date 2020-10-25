<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class AlbumController extends AdminController {
    public function index($status = null,$title = null){
		$Album  =  D('Album');
        /* 查询条件初始化 */
        if(isset($title)){
            $map['name']   =   array('like', '%'.$title.'%');
        }
        if(isset($status)){
            $map['status']  =   $status;
        }else{
            $map['status']  =   array('in', '0,1,2');
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        //$map['pid'] = 0;
        $list = $this->lists($Album,$map,'id desc','id,name,type_name,artist_name,genre_name,hits,sort,recommend,rater,add_time,status');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '专辑管理';
        $this->display();
	}
	public function add(){
		if(IS_POST){
            $Album= D('Album');
            $data = $Album->create();
            if($data){
                $id = $Album->add();
                if($id){
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Album->getError());
            }
        } else {
            $this->assign('Genres', get_genre_tree());
			$this->meta_title = '添加专辑';
			$this->display();
        }

	}
	
	public function mod($id = 0){
        if(IS_POST){
            $Album= D('Album');
            $data = $Album->create();
            if($data){
                if($Album->save()!== false){
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Album->getError());
            }
        } else {
            $data = array();
            /* 获取数据 */
            $data = M('Album')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('Genres', get_genre_tree());
            $this->assign('data', $data);
			$this->meta_title = '修改专辑';
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
        if(M('Album')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }    
}