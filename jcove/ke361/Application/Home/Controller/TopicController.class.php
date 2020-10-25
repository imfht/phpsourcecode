<?php
namespace Home\Controller;

use Home\Model\TopicModel;
class TopicController extends HomeController
{
     public function index(){
         $this->setSiteTitle('专题列表-'.$this->site_title);
         
         $this->assign('_list',$this->lists(D('Topic')));
         $this->assign('hot_topic',D('Topic')->hotTopic());
         if(IS_AJAX){
             $result['p']=I('get.p')+1;
             $result['content']=$this->fetch('ajaxtopic');
             $result['errno']=0;
             $this->ajaxReturn($result);
         }
         $this->display();
     }
     public function detail(){
         $id = I('id');
         $where['tid'] = $id;
         $TopicModel = new TopicModel();
         $topicInfo = $TopicModel->info($id);
         if(empty($topicInfo)){
             $this->error('您查看的专题不存在哦！');
         }
         M('Topic')->where('id='.$id)->setInc('hits');
         $this->setSiteTitle($topicInfo['title']);
         $goods = $this->lists(D('Goods'),$where);
         foreach ($goods as $k=>$v){
             $goods[$k]['url'] = U('/goods/'.$v['id']);
         }
         $this->assign('goods',$goods);
         $this->assign('topic',$topicInfo);
         $this->display();
     }
}

?>