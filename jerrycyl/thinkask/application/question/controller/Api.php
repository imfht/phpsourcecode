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
namespace app\Question\controller;
use app\common\controller\ApiBase;
use think\console\Command;

class Api extends ApiBase
{

    /**
     * [lists 问题列表]
     * @return [type] [description]
     */
	public function lists(){
        $_GET['page'] = (int)input('page');
         $list = $this->getbase->getdb('question')
                      ->alias('q')
                      ->field('q.encry_id,u.user_name,u.uid,u.avatar_file,q.question_id,q.question_content,q.question_detail,q.add_time,q.answer_count,q.answer_users,q.view_count,q.focus_count,q.comment_count,q.category_id,q.agree_count,q.against_count,c.title as c_title')
                      ->join('users u','u.uid = q.uid','left')
                      ->join('category c','q.c_id = c.id','left')
                      ->order('question_id desc')
                      ->paginate(getset('contents_per_question'));
        $newlist = json_decode(json_encode($list),true);
        foreach ($newlist['data'] as &$v) {
                $v['avatar_file'] = get_file_path($v['avatar_file']);
        }

        return returnJson(0,$newlist);

    }

    /**
     * [views 浏览量加1]
     * @return [type] [description]
     */
     public function views(){
        if(!$this->request->isPost()) return;
                model("base")->getinc("question",["where"=>$this->request->only(['question_id'])],"view_count");
               return returnJson(0,"ok");
        }
      /**
       * [collect 收藏]
       * @Author   Jerry
       * @DateTime 2017-05-01
       * @Example  eg:
       * @return   [type]     [description]
       */
    public function collect(){
        if (!$this->request->isPost()) return ;
        if(!parent::getUid()) return returnJson(1001,'','请先登陆');
        $data=$this->request->param();
        $data['time'] = time();
        $data['ip'] = ip2long(fetch_ip());
        $data['uid'] = parent::getUid();
        $data['item_id'] = $data['question_id'];
        $data['type'] = "question";
        if(model('base')->getone("users_collect",["where"=>["item_id"=>$data['item_id'],"type"=>$data['type']]])){
             returnJson(1,'','您已收藏，请不要重复收藏');

        }
        if(model('base')->getadd("users_collect",$data)){
             returnJson(0,'','已收藏');
        }
   }
   /**
    * [uncollect 取消收藏]
    * @Author   Jerry
    * @DateTime 2017-05-01
    * @Example  eg:
    * @return   [type]     [description]
    */
   public function uncollect(){
        if (!$this->request->isPost()) return ;
        if(!parent::getUid()) return returnJson(1001,'','请先登陆');
        $data=$this->request->param();
        $question_id = (int)$data['question_id'];
        $this->getbase->getdel('users_collect',['where'=>["item_id"=>$question_id,'type'=>'question','uid'=>parent::getUid()]]);
    
        returnJson(0,'','已取消收藏');
   }
   /**
    * [accept 采纳回答]
    * @Author   Jerry
    * @DateTime 2017-05-01
    * @Example  eg:
    * @return   [type]     [description]
    */
   public function accept(){
     if (!$this->request->isPost()) return ;
    if(!parent::getUid()) return returnJson(1001,'','请先登陆');
    //是否为本人发布的问题，或者超级管理员
    $question_id = (int)input('question_id');
    $questioninfo = $this->getbase->getone('question',['where'=>['question_id'=>$question_id]]);
    if($questioninfo['uid']==parent::getUid()||in_array(parent::getUid(), config('super_manager'))){
        //是否已经被采纳
        $acceptinfo = $this->getbase->getone('question',['where'=>['question_id'=>$question_id],'field'=>'accept_answer_id']);
        if($acceptinfo['accept_answer_id']){
            returnJson(1,'','同一个问题，不可以重复采纳');
        }else{
           $data['accept_answer_id'] = (int)input('answer_id');
            $data['lock'] = 1;
            if($this->getbase->getedit('question',['where'=>['question_id'=>$question_id]],$data)){
                returnJson(0,'','回复被采纳,问题将被锁定.');
            } 
        }
        
        
    }else{
        returnJson(1,'','只有作者才能采纳回复');
    }
   }
/**
 * [zhan 赞]
 * @Author   Jerry
 * @DateTime 2017-05-03
 * @Example  eg:
 * @return   [type]     [description]
 */
public function zhan(){
   if (!$this->request->isPost()) return;
    if(!parent::getUid()) return returnJson(1001,'','请先登陆');
         $data=$this->request->param();
         $answerinfo = model('base')->getone("answer",["where"=>["answer_id"=>$data['answer_id']]]);
         if(!$answerinfo) returnJson(1,'','此回答不存在!');
         if($answerinfo['uid']==$this->getuid()){
            returnJson(1,'','您不能赞自已的回答!');
         }
         // 是否有赞过，赞过不能再赞
         if(model('base')->getone("answer_vote",["where"=>["answer_id"=>$data['answer_id'],"vote_uid"=>$this->getuid()]])){
            returnJson(1,'','您不能重复赞回答!');
         }

            $data['answer_id'] = $data['answer_id'];
            $data['vote_value'] = 1;
            $data['uid']    = parent::getUid();
            $data['add_time']    = time();
        if(model('base')->getadd("answer_vote",$data)){
             returnJson(0,'','成功点赞');

        }else{
            returnJson(1,'','点赞失败，晚点再试吧！');
        }
 
}
/**
 * [thank 感谢]
 * @Author   Jerry
 * @DateTime 2017-05-03
 * @Example  eg:
 * @return   [type]     [description]
 */
 public function thank(){
    if ($this->request->isAJax()) {
         $data=$this->request->param();
         $answerinfo = model('base')->getone("answer",["where"=>["answer_id"=>$data['answer_id']]]);
         if(!$answerinfo) returnJson(1,'','此回答不存在!');
         if($answerinfo['uid']==$this->getuid()){
            returnJson(1,'','您不能感谢自已!');
         }
         // 是否有感谢过
         if(model('base')->getone("answer_thanks",["where"=>["answer_id"=>$data['answer_id'],"uid"=>$this->getuid()]])){
             returnJson(1,'','您不能重复感谢!');
         }

            $data['answer_id'] = $data['answer_id'];
            $data['uid']    = parent::getUid();
            $data['time']    = time();
        if(model('base')->getadd("answer_thanks",$data)){
            returnJson(0,'','成功点赞!');

        }else{
             returnJson(1,'','点赞失败，晚点再试吧！');
        }
    }
}

/**
 * [comment 保存评论]
 * @Author   Jerry
 * @DateTime 2017-05-03
 * @Example  eg:
 * @return   [type]     [description]
 */
public function comment(){
    if (!$this->request->isPost()) return;
    if(!parent::getUid()) return returnJson(1001,'','请先登陆');
        $comm = $this->request->param();
        $comm['message'] = $comm['message'];
        $comm['time'] = time();
        //获取用户ip
        $comm['ip'] = ip2long(fetch_ip());
        //获取用户uid
        $comm['uid'] = parent::getUid();
        $comm['answer_id'] = (int)$comm['answer_id'];
        //是否填写评论内容
        if (empty($comm['message'])) {
             returnJson(2004,'','您没有填写评论内容哦');
        }

       if (model('base')->getadd("answer_comments",$comm)){
                 returnJson(0,'','评论成功');
            }else{
                 returnJson(2005,'','评论提交失败了,请稍后重试吧!');
            }
    
}
/**
 * [report 举报]
 * @Author   Jerry
 * @DateTime 2017-05-03
 * @Example  eg:
 * @return   [type]     [description]
 */
public function report(){
if (!$this->request->isPost()) return;
    if(!parent::getUid()) return returnJson(1001,'','请先登陆');
        $comm = $this->request->param();
        $comm['reason'] = $comm['reason'];
        $comm['time'] = time();
        //获取用户ip
        $comm['ip'] = ip2long(fetch_ip());
        //获取用户uid
        $comm['uid'] = parent::getUid();
        $comm['answer_id'] = (int)$comm['answer_id'];
        //是否填写评论内容
        if (empty($comm['reason'])) {
             returnJson(2004,'','您没有填写举报理由哦');
        }

       if (model('base')->getadd("answer_report",$comm)){
             returnJson(0,'','举报成功');
        }else{
             returnJson(2005,'','举报成功失败了,请稍后重试吧!');
        }
}
/**
 * [answer       回答]
 * @Author   Jerry
 * @DateTime 2017-05-03
 * @Example  eg:
 * @return   [type]     [description]
 */
public function answer(){
if (!$this->request->isPost()) return;
if(!parent::getUid()) return returnJson(1001,'','请先登陆');
    $comm = $this->request->param();
    //获取问题id
    $comm['question_id'] = (int)$comm['question_id'];
    //获取评论内容
    $comm['answer_content'] = $comm['answer_content'];
    $comm['add_time'] = time();
    //获取用户ip
    $comm['ip'] = ip2long(fetch_ip());
    //获取用户uid
    $comm['uid'] = parent::getUid();
    //是否填写评论内容
    if (empty($comm['answer_content'])) {
        returnJson(2004,'','您没有填写回答内容哦');
    }
    if (is_mobile()) {
        $comm['mobile'] = 'mobile';
    }
    if (empty($comm['question_id'])) {
         returnJson(2000,'','错误的id');
    }
    if (model('base')->getadd("answer",$comm)) {
        //回复+1
        model('base')->getinc('question',['where'=>"question_id=".$comm['question_id']],'answer_count');
        returnJson(0,'','回答成功');
    } else {
        returnJson(2005,'','回答提交失败了,请稍后重试吧!');
    }
        
}
/**
 * [edit description]
 * @Author   Jerry
 * @DateTime 2017-05-07
 * @Example  eg:
 * @return   [type]     [description]
 */
 public function edit()
    {
        if (!$this->request->isPost()) return;
        if(!parent::getUid()) return returnJson(1001,'','请先登陆');
        $id = (int)current($this->request->only(['id']));
        //字段判断
        if ($question_content = $this->request->only(['question_content'])) {
            if (empty($question_content['question_content'])) {
                 return returnJson(1,'','标题不能为空');
            }
        }
         //开启强制分组
         if(getset('must_category')=='Y'){
            if ($category_id = $this->request->only(['category_id'])) {
                if (empty($category_id['category_id']) || $category_id['category_id'] < 1) {
                     return returnJson(1,'','请选择分类');
                }
            } 
         }
        
        $ardb = $this->request->param();
        $ardb['add_time'] = time();
        $ardb['category_id'] = $ardb['category_id'];
        $ardb['published_uid'] = $this->getuid();
        $ardb['question_content']=htmlspecialchars($ardb['question_content']);
        $ardb['question_detail']=htmlspecialchars($ardb['question_detail']);

        if ($id) {
            //修改
            
            if(model('Base')->getedit('question', ['where' => "question_id=$id"], $ardb)!==false){
               return returnJson(0,['url'=>"/question/detail/".encode($id).".html"],'操作成功'); 
            }else{
                return returnJson(1,'','操作失败');
            }
             
        } else {
            // 新加
            $id = model('Base')->getadd('question', $ardb);
            if ($id) {
                $data['post_id'] = $id;
                $data['post_type'] = "question";
                $data['add_time'] = $ardb['add_time'];
                $data['update_time'] = time();
                //是否匿名
                $data['uid'] = $this->getuid();
                // model('Base')->getadd('posts_index', $data);
                 return returnJson(0,['url'=>"/question/detail/".encode($id).".html"],'操作成功');

            }else{
                return returnJson(1,'','操作失败');
            }
        }
    }

}
