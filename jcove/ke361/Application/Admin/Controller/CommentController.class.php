<?php
namespace Admin\Controller;

use Admin\Controller\AdminController;
use Admin\Model\DocumentModel;

class CommentController extends AdminController
{
    public function index(){
        $list = $this->lists('Comment');
        $DocumentM = new DocumentModel();
        foreach ($list as $k=>$v){
            $list[$k]['nickname'] = get_nickname($v['uid']);
            $list[$k]['title'] = $DocumentM->getTitle($v['object_id']);
        }
        $this->assign('_list',$list);
        $this->display();
    }
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('Comment')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
}

?>