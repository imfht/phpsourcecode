<?php
/**
 * 文章操作控制器
 */
namespace Admin\Controller;
class ArticleController extends AdminBaseController {
    //添加文章
    public function add_article(){
        $aid = I('get.aid',0,'intval');
        $ArticleModel = new \Common\Model\ArticleModel();
        if (IS_AJAX) {
            if (!$ArticleModel->send_addData()) {
                //操作失败
                exit(json_encode(array('status'=>0,'msg'=>$ArticleModel->getError())));
            } else {
                //操作成功
                exit(json_encode(array('status'=>1,'msg'=>'操作成功.^_^','url'=>U('Contents/article'))));
            }
        } else {
            $this->assign('tagsData',$ArticleModel->getTagsData());
            $this->assign('cateData',$ArticleModel->getCategoryData());
            if ($aid) {
                //编辑模式
                $this->assign('data',$ArticleModel->getFindData($aid));
                $this->display('edit_article');
            } else {
                //添加模式
                $this->display('add_article');
            }
        }
    }

    //图片上传
    public function send_upload(){
        $upload = $this->uploads();
        if (is_array($upload)) {
            //上传成功 组合Url
            $upload['file']['url'] = substr(C('FILE_ROOT_PATH'),1) . substr($upload['file']['savepath'],1) . $upload['file']['savename'];
            $json = array(
                'status' => 1,
                'msg'    => '上传成功',
                'name'   => $upload['file']['name'],
                'url'    => $upload['file']['url']
            );
            exit(json_encode($json));
        } else {
            //上传失败
            exit(json_encode(array('status'=>0,'msg'=>$upload)));
        }

    }

    //图片删除
    public function del_uploadImg(){
        if (IS_AJAX) {
            $url = trim(I('post.url',''));
            if (!$url) exit(json_encode(array('status'=>0,'msg'=>'删除失败')));
            if (unlink(substr($url,1))) {
                exit(json_encode(array('status'=>1,'msg'=>'删除成功')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'删除失败')));
            }
        }
    }

    //删除文章 信息
    public function del_article(){
        if (IS_AJAX) {
            $ArticleModel = new \Common\Model\ArticleModel();
            $aid = I('post.id',0,'intval');
            if ($aid) {
                if ($ArticleModel->execDelData($aid)) {
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败,请重试...')));
            }
        }
    }
}