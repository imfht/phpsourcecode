<?php
namespace Admin\Controller;
class ContentsController extends AdminBaseController {
    /*
     * 评论管理
     */
    public function comment(){
        $model = M('Comments');
        $where = array('status'=>1);
        $order = " id DESC";
        $limit = 5;
        $count = $model->where($where)->count();
        $Page = new \Think\Page($count,$limit);
        //设置分页显示
        $Page->setConfig('prev','Prev');
        $Page->setConfig('next','Next');
        // 分页显示输出
        $show = $Page->show();
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $model->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $result = array(
            'list' => $list,
            'page' => $show
        );
        $this->assign('data',$result);
        $this->display();
    }

    /*
     * 删除评论
     */
    public function del_comment(){
        if (IS_AJAX) {
            $model = M('Comments');
            $id = I('post.id',0,'intval');
            if ($id) {
                if ($model->where(array('id'=>$id))->delete()) {
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败,请重试...')));
            }
        }
    }

    /*
     * 文章管理
     */
    public function article(){
        $ArticleModel = new \Common\Model\ArticleModel();
        $where = array('is_recycle'=>0);
        $order = " hits DESC";
        $data = $ArticleModel->getListData(5,$where,$order);
        $this->assign('data',$data);
        $this->display();
    }


    /*
     * 标签管理
     */
    public function tags(){
        $TagsModel = new \Common\Model\TagsModel();
        $this->assign('data',$TagsModel->getListData(5));
        $this->display();
    }

    /**
     * 添加 | 编辑 标签
     */
    public function add_tags() {
        $tid = I('get.tid',0,'intval');
        $TagsModel = new \Common\Model\TagsModel();
        if (IS_AJAX) {
            if (!$TagsModel->send_addData()) {
                exit(json_encode(array('status'=>0,'msg'=>$TagsModel->getError())));
            } else {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>U('Contents/tags'))));
            }
        }
        if ($tid) {
            //编辑
            $this->assign('data',$TagsModel->getFindData($tid));
            $this->display('edit_tags');
        } else {
            $this->display('add_tags');
        }
    }

    /**
     * 异步删除  标签信息
     */
    public function del_tags(){
        if (IS_AJAX) {
            $TagsModel = new \Common\Model\TagsModel();
            $tid = I('post.id',0,'intval');
            if ($tid) {
                if ($TagsModel->execDelData($tid)) {
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败,请重试...')));
            }
        }
    }



    /*
     * 留言管理
     */
    public function message(){
        $this->display();
    }

    /*
    * 分类管理
    */
    public function category(){
        $categoryModel = new \Common\Model\CategoryModel();
        $this->assign('data',$categoryModel->getListData(5));
        $this->display();
    }

    /**
     * 添加分类 | 编辑分类
     */
    public function add_cate(){
        $cid = I('get.cid',0,'intval');
        $categoryModel = new \Common\Model\CategoryModel();
        if (IS_AJAX) {
            if (!$categoryModel->send_addData()) {
                exit(json_encode(array('status'=>0,'msg'=>$categoryModel->getError())));
            } else {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>U('Contents/category'))));
            }
        }
        if ($cid) {
            //编辑
            $this->assign('data',$categoryModel->getFindData($cid));
            $this->display('edit_cate');
        } else {
            $this->display('add_cate');
        }
    }

    /**
     * 异步删除分类信息
     */
    public function del_cate(){
        if (IS_AJAX) {
            $categoryModel = new \Common\Model\CategoryModel();
            $cid = I('post.id',0,'intval');
            if ($cid) {
                if ($categoryModel->execDelData($cid)) {
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'请求失败,请重试...')));
            }
        }
    }
}