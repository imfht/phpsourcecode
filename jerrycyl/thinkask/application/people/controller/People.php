<?php
/**
 * 
 */
namespace app\people\controller;
use app\common\controller\Base;
use app\common\model\UserFocus;
class People extends Base
{
  public function index(){
    $uid = (int)decode(input('encode_id'));
    if($uid>0){
        $uid = $uid;
    }else{
      parent::auth('ucenter')->is_login();
        //当前用户的相关信息
        $uid = parent::getUid();  
    }
    $user = $this->getbase->getone('users',['where'=>['uid'=>$uid]]);
    if(!$user)$this->error('没有此用户');
    //发表的问题
    $question = $this->getbase->getall('question',['where'=>['uid'=>$uid],'field'=>'question_content,question_id,add_time,view_count,answer_count']);
    $this->assign('question',$question);
    // show($question);

    $answer = $this->getbase->getdb('answer')->where(['a.uid'=>$uid])
                                             ->alias('a')
                                             ->join('question q','q.question_id = a.question_id')
                                             ->field('q.question_id,question_content,answer_content,a.add_time')
                                             ->select();
                                             // show($answer);
    $this->assign('question',$question);
    $this->assign('answer',$answer);
    $this->assign('user',$user);
    return $this->fetch('people/index');
  }
  /**
   * [auth 认证管理]
   * @Author   Jerry
   * @DateTime 2017-05-13
   * @Example  eg:
   * @return   [type]     [description]
   */
  public function auths(){

    return $this->fetch('people/auths');
  }
  /**
   * [passwd 密码设置]
   * @Author   Jerry
   * @DateTime 2017-05-13
   * @Example  eg:
   * @return   [type]     [description]
   */
  public function passwd(){
    return $this->fetch('people/passwd');
  }
/**
 * [top description]
 * @Author   Jerry
 * @DateTime 2017-05-14
 * @Example  eg:
 * @return   [type]     [description]
 */
  public function top(){
    return $this->fetch('people/public/top');
  }
  /**
   * [question description]
   * @Author   Jerry
   * @DateTime 2017-05-14
   * @Example  eg:
   * @return   [type]     [description]
   */
  public function question(){

    if(input('type')=="answer"){
      $this->assign('question',$this->getbase->getdb('question')->alias('q')->join('users u ','u.uid=q.uid','left')->field('q.*,user_name,avatar_file')->paginate());
    }else{
      $this->assign('question',$this->getbase->getdb('question')->alias('q')->join('users u ','u.uid=q.uid','left')->where("q.uid = ".parent::getUid())->field('q.*,user_name,avatar_file')->paginate());
    }
    // show($this->getbase->getdb('question')->alias('q')->join('users u ','u.uid=q.uid','left')->paginate());
    
     return $this->fetch('people/question');
  }
  

	
}