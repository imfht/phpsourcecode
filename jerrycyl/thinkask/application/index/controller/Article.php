<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\index\controller;
use app\common\controller\Base;


class Article extends base
{
    public function _initialize()
    {
        $this->assign('hotarticle',model('base')->getall('article',['limit'=>'20','order'=>'views desc']));
        // show(model('base')->getall('topic',['limit'=>'25','order'=>'topic_id desc']));
        $this->assign('hotuser',model('base')->getall('users',['limit'=>'5','field'=>'user_name,uid,avatar_file','order'=>'uid desc']));
        $this->assign('hottag',model('base')->getall('topic',['limit'=>'25','order'=>'topic_id desc','field'=>'topic_id,topic_title']));
    
    }
    public function index()
    {
        $id = $this->request->only(['id']);
        $status = $this->request->only(['status']);
        $status = $status['status'];
        // show($this->request->param());
        $id = (int) $id['id'];
        if(!$id){
            switch ($status) {
                case 'hot':
                   $order = "views desc";
                    break;
                case 'recommend':
                   $order ="is_recommend desc";   
                    break;

                default:
                    $order="id desc";
                    break;
            }
            
            $list = model('base')->getpages('article',['page'=>getset('contents_per_article'),'join'=>[[config('database.prefix').'users us','a.uid=us.uid']],'alias'=>'a','order'=>$order]);
            $this->assign('status',$status);
            $this->assign('list',$list);
        }else{
            //最新
            $this->assign($ar = model('Article')->getArById($id));
            //评论
            $comment=model('base')->getall('article_comments',['where'=>['article_id'=>$id],'join'=>[[config('database.prefix').'users us','qucmes.uid=us.uid']],'alias'=>'qucmes','field'=>'qucmes.*,us.user_name,us.avatar_file','order'=>'add_time desc']);
             if($this->getuid()>0){
                foreach ($comment as $key => $v) {
                    $where['item_id'] = $v['id'];
                    $where['uid'] = $this->getuid();
                   if(model('base')->getone('article_vote',['where'=>$where])){
                        $comment[$key]['zhan'] = "Y";
                   }else{
                        $comment[$key]['zhan'] = "N";
                   }
                }
             }
            $this->assign('comment',$comment);
           
             
        }
        $setting = cache('system_setting');
        $tpl=$id?"article":'article_list';
        $seo['title'] = $id>1?$ar['title']."-".unserialize($setting[1]['value']):"文章列表-".unserialize($setting[1]['value']);
        $seo['description'] = $id>1?msubstr(strip_tags($ar['message']),0,50):unserialize($setting[2]['value']);
        $seo['keywords'] = $id>1?msubstr(strip_tags($ar['message']),0,50):unserialize($setting[3]['value']);
        $this->assign('seo',$seo);
        return $this->fetch('index/'.$tpl); 
        
    }

}
