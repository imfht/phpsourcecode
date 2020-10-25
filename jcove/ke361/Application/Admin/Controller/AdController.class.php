<?php
namespace Admin\Controller;

use Admin\Model\AdModel;
use Think\Model;
use Admin\Model\AdPositionModel;
class AdController extends AdminController
{
    public function index(){
        
        $list = $this->lists(D('Ad'));
        foreach ($list as $k=>$v){
            if($v['type']==1){
                $list[$k]['code'] ="<img alt='".$v['title']."' src='".get_image_url($v['pic_url'])."' style='width:120px;'>";
            }
        }
        $this->assign('_list',$list);
        $this->display();
    }
    public function edit(){
        if(IS_POST){
            $ad['id']       =   I('post.id');
            $ad['title']    =   I('post.title');
            $ad['code']  =   I('post.code');        
            $ad['link']     =   I('post.link');
            $ad['template']     =   I('post.template');
            $ad['position']     =   I('post.position');
            $ad['object_id']     =   I('post.object_id');
            $ad['width']     =   I('post.width');
            $ad['type']     =   I('post.type');
            $ad['height']     =   I('post.height');
            $ad['pic_url']  = I('post.pic_url');
            if($ad['type']==1){
                if(empty($ad['pic_url'])){
                    $this->error('请上传图片');
                }
            }
            $AdModel        =   new AdModel();
            $res            =   $AdModel->addAd($ad);
            if($res){
                $this->success('添加成功',U('Ad/index'));
            }else {
                $this->error($AdModel->getError());
            }
        }else{
            $id =   I('get.id');
            $AdModel = new AdModel();
            $defaultAdPosition = D('AdPosition')->getAdPositionByTemplate(1);
            $this->assign('ad',$AdModel->info($id));
            $this->assign('default_ad_position',$defaultAdPosition);
            $this->display();
        }
    }
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('Ad')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
    public function adPositionList(){
        $list = $this->lists(D('AdPosition'));
        $this->assign('_list',$list);
        $this->display();
    }
    public function editAdPosition(){
        if(IS_POST){
            $ad['id']       =   I('post.id');
            $ad['name']    =   I('post.name');
            $ad['template']  =   I('post.template');
            $ad['title']  =   I('post.title');
            $AdPostionModel        =   new AdPositionModel();
            $res            =   $AdPostionModel->addAdPosition($ad);
            if($res){
                $this->success('添加成功',U('Ad/AdPositionList'));
            }else {
                $this->error('添加失败，'.$AdPostionModel->getError());
            }
        }else{
            $id =   I('get.id');
            $AdPostionModel = new AdPositionModel();
            $this->assign('ad',$AdPostionModel->info($id));
            $this->display();
        }
    }
    public function delAdPostion(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('AdPosition')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
    /**
     * 获取模版对应的广告位
     */
    public function ajaxAdPosition(){
        $template = I('post.template','0');
        $result = array(
            'status'=>0,
            'content'=>'',
            'message'=>''
        );
        if($template > 0){
            $where['template'] = $template;
            $where['status']    = 1;
            $res = D('AdPosition')->where($where)->select();
            if(is_array($res)){
                foreach ($res as $row){
                    $result['content'].="<option value='".$row['id']."'>".$row['title']."</option>";
                }
                $result['status']=1;
            }else {
                $result['message'] = '未找到相应的广告位';
            }
        }else{
            $result['message'] = "请选择有效模板";
        }
        $this->ajaxReturn($result);
    }
}

?>