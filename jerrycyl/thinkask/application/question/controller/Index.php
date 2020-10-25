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
use app\common\controller\Base;
class Index extends Base
{
  /**
   * [index 列表]
   * @Author   Jerry
   * @DateTime 2017-04-27T16:25:26+0800
   * @Example  eg:
   * @return   [type]                   [description]
   */
  
  public function index()
    {

      switch ($status = input('status','','htmlspecialchars')) {
        case 'unresponsive':##待回复
          $order = "answer_count asc";
          break;
         case 'hot':##最热
          $order = "view_count desc";
          break;
         case 'recommend':##推荐
          $order = "is_recommend desc";
          break;
        
        default:
          $order = "add_time desc";
          break;
      }
      $this->assign('status',$status);
         $list = $this->getbase->getdb('question')
                      ->alias('q')
                      ->field('u.user_name,u.uid,u.avatar_file,q.question_id,q.question_content,q.question_detail,q.add_time,q.answer_count,q.answer_users,q.view_count,q.focus_count,q.comment_count,q.category_id,q.agree_count,q.against_count,c.title as c_title')
                      ->join('users u','u.uid = q.uid','left')
                      ->join('category c','q.c_id = c.id','left')
                      ->order($order)
                      ->paginate(getset('contents_per_question'));
        $this->assign('list',$list);

        //右边的用户
        $users = $this->getbase->getall('users',['limit'=>12,'field'=>'uid,user_name,avatar_file','order'=>'uid desc']);
        $this->assign('users',$users);
        //右边的文章
        $article = $this->getbase->getall('article',['limit'=>20]);
        $this->assign('article',$article);

        return $this->fetch('question/question_list');  

    }
    /**
     * [detail 详情]
     * @Author   Jerry
     * @DateTime 2017-04-27T16:25:15+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function detail(){
      $question_id = (int)decode(input('encry_id'));
      if(!$data = cache('question_detail_data'.$question_id)){
          $data = $this->getbase->getdb('question')
                              ->alias('q')
                              ->where(["q.question_id"=>$question_id])
                              ->join('users u','u.uid = q.uid','left')
                              ->field('u.user_name,u.uid,u.avatar_file,q.*')
                              ->find();
        $data['focus'] = $this->getbase->getcount('question_focus',['where'=>['question_id'=>$data['detail']['question_id']]]);
        $data['thanks'] = $this->getbase->getcount('question_thanks',['where'=>['question_id'=>$data['detail']['question_id']]]);
        $data['uninterested'] = $this->getbase->getcount('question_uninterested',['where'=>['question_id'=>$data['detail']['question_id']]]);
        cache('question_detail_data'.$question_id,$data);
      }
      

      //当前用户是否收藏  
      $this->assign('is_collect',$this->getbase->getcount('users_collect',['where'=>['item_id'=>$data['question_id'],'uid'=>parent::getUid(),'type'=>'question']]));
      
      //显示回复 
      $answer = $this->getbase->getdb('answer')
                               ->where(["a.question_id"=>$data['question_id']])
                               ->join('users u','u.uid = a.uid','left')
                               ->alias('a')
                               ->field('u.user_name,u.uid,u.avatar_file,a.*')
                               ->order('a.answer_id desc')
                               ->select();
      foreach ($answer as &$v) {
        //如果评论达到举报次数就UNSET掉
        $reportcount = $this->getbase->getcount('answer_report',['where'=>['answer_id'=>$v['answer_id']]]);
        if($reportcount>getset('accusation_hidden')) unset($v);
          if($uid = parent::getUid()>0){
              ##当前用户是否评论
              // if($this->getbase->getcount('answer_comments',['where'=>['answer_id'=>$v['answer_id'],'uid'=>$uid]])) $v['commentsed'] = "checked";
              ##当前用户是否有感谢
              if($this->getbase->getcount('answer_thanks',['where'=>['answer_id'=>$v['answer_id'],'uid'=>$uid]])>0) $v['thanksed'] = "checked";
              ##当前用户是否有赞
              if($this->getbase->getcount('answer_vote',['where'=>['answer_id'=>$v['answer_id'],'uid'=>$uid]])>0) $v['voteed'] = "checked";
              ##当前用户是否有举报
              if($this->getbase->getcount('answer_report',['where'=>['answer_id'=>$v['answer_id'],'uid'=>$uid]])>0) $v['reported'] = "checked";
          }


      }
      //被采纳
       $accept = $this->getbase->getdb('answer')
                               ->where(["a.answer_id"=>$data['accept_answer_id']])
                               ->join('users u','u.uid = a.uid','left')
                               ->alias('a')
                               ->field('u.user_name,u.uid,u.avatar_file,a.*')
                               ->find();

      $this->assign('accept',$accept);
      $this->assign($data);
      $this->assign('answer',$answer);
      return $this->fetch('question/question'); 
    }
   

}
