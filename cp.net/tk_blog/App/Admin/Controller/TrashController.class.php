<?php
/**
 * 回收站
 */
namespace Admin\Controller;
class TrashController extends AdminBaseController {
    public function index(){
        $ArticleModel = new \Common\Model\ArticleModel();
        $where = array('is_recycle'=>1);
        $this->assign('data',$ArticleModel->getListData(5,$where));
        $this->display();
    }

    /**
     * 回收站
     */
    public function do_Trash(){
        if (IS_AJAX) {
            $aid = I('post.id',0,'intval');
            $type= I('post.type',0,'intval');
            $where = array('aid'=>$aid);
            $model = M('Article');
            if (!$aid || !$type) exit(json_encode(array('status'=>0,'msg'=>'操作失败.ㄒoㄒ~')));
            if ($type == 1) {
                //回收
                if ($model->where($where)->setField(array('is_recycle'=>1))) {
                    exit(json_encode(array('status'=>1,'msg'=>'回收成功.^_^')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'回收失败.ㄒoㄒ~')));
                }
            } else {
                //还原
                if ($model->where($where)->setField(array('is_recycle'=>0))) {
                    exit(json_encode(array('status'=>1,'msg'=>'还原成功.^_^')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'还原失败.ㄒoㄒ~')));
                }
            }
        }
    }
}