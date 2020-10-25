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
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Hook;

class Question extends Controller
{
    protected $request;
    public function _initialize()
    {
      $this->request = Request::instance();

    }
    public function index()
    {

        $id = $this->request->only(['id']);
        $status = $this->request->only(['status']);
        $status = $status['status'];
        $id = (int) $id['id'];

        if(!$id){
            // $list = model('Question')->getList($status);
            switch ($status) {
                case 'hot':
                   $order = "views_count desc";
                    break;
                case 'recommend':
                   $order ="is_recommend desc";   
                    break;
               case 'unresponsive':
                   $order ="a.answer_count asc";   
                    break;
                default:
                    $order="published_uid desc";
                    break;
            }

            // 列表
             $list = model('base')->getpages('question',['page'=>getset('contents_per_question'),'join'=>[[config('database.prefix').'users us','a.published_uid=us.uid']],'alias'=>'a','field'=>'a.*,us.user_name,us.avatar_file','order'=>$order]);
             //topic
             foreach ($list as $key => $v) {
                 $topics[$v['question_id']]= model('base')->getall('topic_relation',['join'=>[[config('database.prefix').'topic tpc','tpc.topic_id=tpc_rela.topic_id']],'alias'=>'tpc_rela','where'=>['tpc_rela.item_id'=>"{$v['question_id']}",'type'=>"question"]]);
             }
             //热门用户
             // $hostuser = model('base')->query('select * from '.config('database.prefix').'question')
             
             $this->assign('topics',$topics);
            $this->assign('status',$status);
            $this->assign('list',$list);
        }else{
            // 内容和发表人
            $this->assign($question = model('Question')->getDetailById($id));
            //话题
            $this->assign('topic',$topic = model('Question')->getTopicById($id));
            //回答
            $this->assign('answer',$answer=model('base')->getall('question_comments',['where'=>['question_id'=>$id],'join'=>[[config('database.prefix').'users us','qucmes.uid=us.uid']],'alias'=>'qucmes','field'=>'qucmes.*,us.user_name,us.avatar_file','order'=>'time desc']));
            // show($answer);
        }
       $setting = cache('system_setting');
        $tpl=$id?"question":'question_list';
        //seo
        $seo['title'] = $id>1?$question['question_content']."-".unserialize($setting[1]['value']):"问题列表-".unserialize($setting[1]['value']);
        $seo['description'] = $id>1?msubstr(strip_tags($question['question_detail']),0,50):unserialize($setting[2]['value']);
        $seo['keywords'] = $id>1?msubstr(strip_tags($question['question_detail']),0,50):unserialize($setting[3]['value']);
        $this->assign('seo',$seo);
       return $this->fetch('index/'.$tpl);  
        
    }

}
