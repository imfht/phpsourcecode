<?php
/**
 * 
 * 模块/广告
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class PosterController extends AdminController{
    public function _initialize(){
        $action = array(
                // 'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
                //'allow'=>array('index')
        );
        B('Admin\\Behaviors\\Authenticate', '', $action);
    }
    

    //广告管理
    public function manage(){
        $page_list = D('Poster')->order('id DESC')->select();
        $this->assign('page_list', $page_list);
        $this->display();
    }

    public function posterAdd(){
        if(IS_POST){
            $info = I('post.info');
            $linkurl = I('post.linkurl');
            $imageurl = I('post.imageurl');
            $alt = I('post.alt');

            for($i=0; $i< count($linkurl); $i++){
                if(empty($linkurl[$i]) && empty($imageurl[$i]) && empty($alt[$i])){
                    continue;
                }
                $image_info['linkurl'] = $linkurl[$i];
                $image_info['imageurl'] = $imageurl[$i];
                $image_info['alt'] = $alt[$i];
                $images[] = $image_info;
            }
            // var_dump($setting);exit;
            $info['setting']['images'] = $images;
            $result = D('Poster')->create($info);

            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Poster_manage'));
        }else{
            $this->display('posterEdit');
        }
    }

    public function posterEdit(){
        $id = I('get.id');
        $detail = D('Poster')->getDetailById($id);
        if(!$detail){
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        }
        if(IS_POST){
            $info = I('post.info');
            $linkurl = I('post.linkurl');
            $imageurl = I('post.imageurl');
            $alt = I('post.alt');

            for($i=0; $i< count($linkurl); $i++){
                if(empty($linkurl[$i]) && empty($imageurl[$i]) && empty($alt[$i])){
                    continue;
                }
                $image_info['linkurl'] = $linkurl[$i];
                $image_info['imageurl'] = $imageurl[$i];
                $image_info['alt'] = $alt[$i];
                $images[] = $image_info;
            }
            // var_dump($setting);exit;
            $info['setting']['images'] = $images;
            $result = D('Poster')->update($id, $info);

            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Poster_manage'));
        }else{

            $this->assign('Detail', $detail);
            $this->display();
        }
    }

    public function posterDelete(){
        $id = I('get.id');
        $detail = D('Poster')->getDetailById($id);
        if(!$detail){
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        }
        if(IS_POST){
            $result = D('Poster')->deleteItem($id, $info);
            $this->ajaxReturn(array('statusCode'=>200,'message'=>'保存成功'));
        }
    }
}