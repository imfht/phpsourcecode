<?php

namespace Xbkc\Controller;
use Think\Page;
use Think\Controller;

/**
 * 校本课程学生管理控制器
 * @author水月居 <singliang@163.com>
 */
class StudentController extends Controller {

    /**
     * 用户管理首页
    */
    public function index(){
		
		//echo I('class');
		$sclass = I('class')?I('class'):"请选择班级" ; //设置学生班级
		$where=I('class')?array("class"=>I('class'),"status"=>"1"):array("status"=>"1");
        $XbStudent=D("XbStudent");
        //dump($XbStudent);
        $list=$XbStudent->relation(true)->order("sid asc")->where($where)->limit(45)->select();
		//$list=$XbStudent->field("sid, grade, class, code, name, sex, cid, status")->order("sid asc")
		//		->where($where)->limit(45)->select();
		$class=M('XbClass')->field("class")->select();
	    $this->assign('_sclass',$sclass);
		$this->assign('_class', $class);
        $this->assign('_list', $list);

        //dump($list);
        $this->meta_title = '学生信息';
        $this->display();
    }
    /*
    **查看未报名学生名单*
    */
    public function showXbStudent0(){
    //获取课程信息
        $curriculum=D("Curriculum");
        $clist=$curriculum->field("cid, cname")->select();
        //获取学生信息
         $map['cid'] = 0;//获取cid
        $XbStudent=D('XbStudent');
        $order=array('grade'=>'desc','sid'=>'asc');       
        $stlist=$XbStudent->field("sid,grade,code,class,name,sex,update_time")->order($order)->where($map)->limit('30')->select();
        //$stlist=$XbStudent->where($map)->select();
        //echo $curriculum->getLastSql();
        //dump($stlist);
        //获取当前课程信息
        $cur=$curriculum->where($map)->find();
        // dump($cur);
        $this->assign('_cur', $cur);
        $this->assign('_curriculum', $clist);
        $this->assign('XbStudent', $stlist);
        //输出sseion
        //dump($_SESSION);
        $this->display('showXbStudent');  

    }
    /*查看学生报名情况*/
	    public function showXbStudent(){
    //获取课程信息
        $curriculum=D("XbCurriculum");
        $clist=$curriculum->field("cid, cname")->select();
        //获取学生信息
         $map['cid'] = I('cid');//获取cid
        $Student=D('XbStudent');
        $order=array('grade'=>'desc','sid'=>'asc');
       
        $stlist=$XbStudent->field("sid,grade,code,class,name,sex,update_time")->order($order)->where($map)->limit('30')->select();
        //$stlist=$XbStudent->where($map)->select();
        //echo $curriculum->getLastSql();
        //dump($stlist);
        //获取当前课程信息
        $cur=$curriculum->where($map)->find();
        // dump($cur);
        $this->assign('_cur', $cur);
        $this->assign('_curriculum', $clist);
        $this->assign('XbStudent', $stlist);
        //输出sseion
        //dump($_SESSION);
        $this->display();  

    }
        /**
     * 修改学生课程信息初始化
     * @author singliang <singliang@163.com>
     */
    
    public function selectCurriculum($sid=0){
        $student = M('XbStudent')->find(I('sid'));
        $this->assign('Student', $student);
        $map['grade'] =array('like',"%".$student['grade']."%");
        $map['year'] = C('CUR_YEAR');
        $map['status'] = 1;
        $curriculum=M('XbCurriculum')->where($map)->limit(50)->select();
        //dump(I('sid'));
        //$curriculum=$sql->getLastSql();
        //dump($curriculum);
        //dump($student);
        $this->assign('curriculum', $curriculum);
        $this->assign('student', $student);
        $this->meta_title = '学生选课';
        $this->display();
    }
    //清除选课
    public function truncate($sid=0){
            $data = array(
            'sid'             => I('sid'),
            'cid'             => 0,
            'update_time' => NOW_TIME,
            'update_ip'   => get_client_ip(1),
        );

         $cur['class']=I('class');
         $res = D('XbStudent')->save($data);

        if(!$res){
            $this->error(D('XbStudent')->getError());
        }else{
            $this->success('清除成功！', U('student/index',$cur));
        }

    }
	//保存选课程信息
	 public function saveStudentCurriculum(){

        $data = array(
            'sid'             => I('sid'),
            'cid'             => I('cid'),
            'update_time' => NOW_TIME,
            'update_ip'   => get_client_ip(1),
        );

           $cur['class']=I('class');
           $res = D('XbStudent')->save($data);
           
        if(!$res){
            $this->error(D('XbStudent')->getError());
        }else{
			$curr=M('curriculum');
			$curdata['cid']=I('cid');
			$curdata['count']=M('XbStudent')->where($curdata)->count();
		  	$curr->save($curdata);
			//dump($curdata);		
            $this->success('选课成功！', U('Student/index',$cur));
        }
    }
public function classScore($class="请选择班级"){
        $XbClass=D("XbClass");
        $clist=$XbClass->field("id, class")->order(array('sort'=>"asc"))->select();
        //获取学生信息
         $map['class'] = I('class')?I('class'):$class;//获取班级id
         //dump($clist);
        $XbStudent=D('XbStudent');
        $order=array('grade'=>'asc','code'=>'asc');
       
        $stlist=$XbStudent->relation(true)
        //->field("sid,code,class,name,sex,cname,score,outstand,scoreuptime")
        ->order($order)->where($map)->limit('42')->select();
        //echo $curriculum->getLastSql();
        //dump($stlist);
        //获取当前班级信息
        if("请选择班级"==$map['class']){
            $cur['class']="请选择班级";
             }else{
                $cur=$XbClass->where($map)->find();
            }

        $this->assign('_cur', $cur);
        $this->assign('classlist', $clist);
        $this->assign('student', $stlist);
	 $this->display();
     }

     /*优秀学员*/
     public function outstand(){

         $map['outstand'] = 1;
         //dump($clist);
        $XbStudent=D('XbStudent');
        $order=array('cid'=>'asc','grade'=>'asc','code'=>'asc');
       
        $stlist=$XbStudent->relation(true)
        //->field("sid,code,class,name,sex,cname,score,outstand,scoreuptime")
        ->order($order)->where($map)->limit('42')->select();
        //dump($stlist);

        $this->assign('student', $stlist);
	    $this->display();
     }

public function updateScoredata(){
	header("Content-Type:text/html; charset=utf-8");
	$uid=is_login();
	$term=I('term');
	//echo C('CUR_TERM');
	if($uid!=1){
		 $this->redirect('/Home/User/login'); 		
	}else{
		$error='只有管理员才有权限更新学期成绩 , <br>输入的学期代号必须正确';
	}
	if(IS_POST){
		if($term!==C('CUR_TERM')) {$error='输入的学期代号必须正确';
			
		}else{
        
        //获取学生信息
        $XbStudent=D('XbStudent');
        $order=array('grade'=>'asc','class'=> 'asc','code'=>'asc');
        $map["status"] = 1;       
        $stlist=$XbStudent->relation(true)
        //->field("sid,code,class,name,sex,cname,score,outstand,scoreuptime")
        ->order($order)->where($map)->select();
        //整理插入数据$data
        foreach($stlist as $v){
        	$data[]=array(
        		'grade'=> $v['grade'] ,
        		'team'=>C('CUR_TERM'),//配置项中的当前学期
        		'class'=>$v['class'],
        		'code'=>$v['code'],
        		'student'=>$v['name'],
        		'sex'=>$v['sex'],
        		'kecheng'=>$v['cname'],
        		'teacher'=>$v['teacher'],
        		'score'=>$v['score'],
        		'outstand'=>$v['outstand'],
        		'date'=>date(Ymd,$v['scoreuptime'])
        		);
        }
       //dump($data);
       $sql=M('xbStudentScore');
       $sql_num=$sql->addall($data);
       $error='期末成绩更新成功，'.$sql_num.'条学生成绩记录更新到数据库';
       }
        //echo $curriculum->getLastSql();
        //更新到相应数据库中
    }
       
      //$this->assign('student', $stlist);
      $this->assign('error_msg', $error);
      $this->display(); 
	 
     }
      
 












}