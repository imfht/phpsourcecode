<?php
namespace Xbkc\Controller;
use Think\Page;
use Think\Upload;
use Think\Controller;

/**
 * 校本课程控制器
 * @author水月居 <singliang@163.com>
 */
class IndexController extends Controller {

    /**
     * 用户管理首页
    */
    public function index(){

		
		//echo "查看已申报课程";

		$where=array("status"=>"1","year"=>C('CUR_YEAR'));		
        $curriculum=D("XbCurriculum");
		$list=$curriculum
        //->field("sid, grade, class, number, name, sex, cid, status")
        ->order("category asc")->where($where)->select();

		$this->assign('curriculum', $list);
        $this->assign('categoryid', $cateid);

        $this->display();
    }
        //课程程查询
        public function indexCurriculum(){

    
    //echo "查看已申报课程indexCurriculum";

    $sclass = I('class')?I('class'):"请选择班级" ; //设置学生班级
    $where=array("status"=>"1","year"=>C('CUR_YEAR'));    
        $curriculum=D("XbCurriculum");
    $list=$curriculum
        //->field("sid, grade, class, number, name, sex, cid, status")
        ->order("category asc")->where($where)->select();
    
      //dump($category);
        //$cateid=array($category['id'],true);
    $this->assign('_curriculum', $list);
        $this->assign('_categoryid', $cateid);
       
        $this->meta_title = '课程信息';
        $this->display();
    }

    /**
     * 查看学生信息
     * @param  integer $cid 课程ID
     * @return string       
     */
    public function student($cid = 0){
       //获取课程信息
        $curriculum=D("XbCurriculum");
        $where['year']=I('year')?I('year'):C('YEAR');
        $clist=$curriculum->field("cid, cname")->where($where)->select();
        //获取学生信息
         $map['cid'] =I('cid')?I('cid'):$cid;//获取cid
        $student=D('XbStudent');
        $order=array('class'=>'asc','sid'=>'asc');       
        $stlist=$student->field("sid,grade,code,class,name,sex,update_time")->order($order)->where($map)->limit(60)->select();
	    //检查重新统计人数
        $curr=M('XbCurriculum');          
            $curdata['count']=$student->where($map)->count();
            if($cur['count']!=$curdata['count']){
            $curr->where($map)->save($curdata);
            }

        //获取当前课程信息
        $cur=$curriculum->where($map)->find();        
        
        $this->assign('_cur', $cur);
        $this->assign('_curriculum', $clist);
        $this->assign('student', $stlist);
        $this->display();

    }
    public function showCurriculum($cid=0){
        $cid=I('cid')?I('cid'):$cid;//获取cid
        //dump($cid);
        $curriculum=D("XbCurriculum");
        $cur=$curriculum->find($cid);
        //dump($cur);
        $this->assign('_cur', $cur);
        $this->display();
    }

    public function edit($cid=0){
        $cid=I('cid')?I('cid'):$cid;//获取cid
         $curriculum=D("XbCurriculum");
         if(IS_POST){
             $curriculum->create();
             //$se=I('post.cid');
              //dump($curriculum->create());
             $curriculum->save();
             }       
        $cur=$curriculum->find($cid);
        $this->assign('_cur', $cur);
        $this->display();             
    }




    /*显示课程资料汇总列表*/

    public function xbDocFiles($cid=0){ 

      $year=I('post.year');     
      if(""==$year){
        $map['year']=C('CUR_YEAR');       
      }else{
        $map['year']= $year;
      }

      $teacher=I('post.teacher');     
      if(""==$teacher){
        unset($map['teacher']);         
      }else{
        $map['teacher']=array('like', '%' . $teacher . '%');
      }
      $cname=I('post.keywords');     
      if(""==$cname){
        unset($map['cname']);         
      }else{
        $map['cname']=array('like', '%' . $cname . '%');
      }

      $cate=I('post.cate');     
      if(""==$cate ||"所有类别"==$cate){
        unset($map['cate']);         
      }else{
        $map['cate']=array('like', '%' . $cate . '%');
      }
      // dump($map);

        $order=array('id'=>'desc');
        $map["cid"]=I('cid')?I('cid'):0;//获取cid
         if(!$map["cid"]) unset($map["cid"]);


          $xbfiles=D('Xb_files')->field('cate,cid,cname,teacher,update_time,filename,url,uid')->where($map)->order($order)->select();
          //dump($kecheng);
        $this->assign('kecheng',$xbfiles);
        $this->display();  
    }
    public function showUpload(){
		 $order=array('id'=>'desc','cid'=>'asc');
		 $map["cid"]=I('cid')?I('cid'):0;//获取cid
		 if(!$map["cid"]) unset($map["cid"]);
     $year=I('get.year');
		 $map['year']=$year?$year:C('CUR_YEAR');
     //dump($map);
    	$list=D('XbFiles')->field('cate,cid,cname,teacher,update_time,filename,url,uid')->where($map)->order($order)->select();
    	//dump($list);
    	$this->assign('list',$list);
    	$this->display();
    }
//上传资料 

   public function upload(){
      $cid=I('cid')?I('cid'):$cid;//获取cid
          if(IS_POST){
            if(!$_POST['cate']){
                $this->error("请选择上传类别后再上传");
            }
            //header("Content-type: text/html; charset=utf-8");           

           /**如果目录不存在则自动创建**/
            $root_path= './Uploads/Xiaoben/';
            $uppath   = $root_path.C('CUR_TERMA');
            $uppath_gk=iconv('utf-8', 'gbk', $uppath);  //将目录转化gbk  
            if(!file_exists($uppath_gk)) {
                $up='./Uploads/Xiaoben/';
                if(!file_exists($up)) mkdir($up);
                $up.= '/'.C('CUR_TERMA');
                if(!file_exists($up)) mkdir($up);
                mkdir($uppath_gk);//如果不存在则设定目录
                echo '目录创建成功';
             }

       $save=I('cate').I('cid').I('cname').I('teacher').date(ymd);//保存文件的命名规则

       $upload = new \Think\Upload();//实例化上传类   
       $upload->maxSize  = 30*1024*1024 ; // 设置附件上传大小 
       $upload->exts      =     array('zip','rar','doc','docx','ppt','pptx');// 设置附件上传类型
       $upload->rootPath  =      $root_path.C('CUR_TERMA').'/'; // 设置附件上传根目录
       $upload->savePath  = ''; // 设置附件上传（子）目录   
       $upload->autoSub  = false; 
        $upload->saveName=iconv('utf-8', 'gbk', $save); //保存文件的命名规则
        $upload->Replace=true;
        $info   =  $upload->upload();

				 //$file=$_FILES['upfile'];
				 $file['ext']    =   pathinfo($_FILES['upfile']['name'], PATHINFO_EXTENSION);

				    if(!$info) {// 上传错误提示错误信息
				        $this->error($upload->getError());
				    }else{// 上传成功
				  //保存表单数据
				        $url= C('YEAR').'/'.$save.'.'.$file['ext'];      
				    	$data=array(    
				    		         "url" => $url,
				    		         'cate'=> I('cate'),
				                     "cid" =>   I('cid'),
				                     "teacher"=>I('teacher'),
				                     "filename"=>$_FILES['upfile']['name'],
				                     "cname"     => $_POST["cname"],  
				                     "uid" =>   I('uid'),
				                     "year"=>C('CUR_YEAR'),
				                     "update_time" => date(Y-m-d)
				                     ); 

				             //  dump($data) ;            
				           $kecheng=D('XbFiles');
				             $kecheng->create($data);
				             $kecheng->add(); 
				             //dump($kecheng->getLastSql());
				             
				        $this->success('上传成功！');
				    }
             } 
        $cur=D("XbCurriculum")->find($cid);
        $this->assign('_cur', $cur);
        //dump($cur);
	   $this->display(); 
   
   }
	/*编辑优秀学员*/
	public function outstand(){
         $cid=78;
		  $cid=I('cid')?I('cid'):$cid;//获取cid
		  $map['year']=C('YEAR');
		  $map['cid']=$cid;//$cid;
		  //获取当前课程学生
	     $studata=M('student')->where($map)->field('sid,class,code,name,sex,outstand')->select();
	     //dump($studata);
	     $this->assign('student',$studata);
	     //获取当前课程信息
		  $cur=D('Curriculum')->find($cid);
         $this->assign('_cur', $cur);
          if(IS_POST){
          	dump($_POST);
          }
          $this->display();

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
        //$stlist=$XbStudent->where($map)->select();
        //echo $curriculum->getLastSql();
        //dump($stlist);
        
    // ["sid"] => string(3) "387"
    // ["grade"] => string(1) "3"
    // ["class"] => string(13) "三年级1班"
    // ["code"] => string(1) "1"
    // ["name"] => string(9) "曹梦瑶"
    // ["sex"] => string(3) "女"
    // ["cid"] => string(2) "98"
    // ["score"] => string(1) "A"
    // ["outstand"] => string(1) "0"
    // ["scoreuptime"] => string(10) "1427819539"
    // ["update_time"] => string(10) "1409908690"
    // ["update_ip"] => string(9) "174981907"
    // ["status"] => string(1) "1"
    // ["uid"] => string(1) "0"
    // ["cname"] => string(12) "我是歌手"
        //获取当前班级信息
        if("请选择班级"==$map['class']){
            $cur['class']="请选择班级";
             }else{
                $cur=$XbClass->where($map)->find();
            }
        // dump($map);
        //  dump($stlist);
        $this->assign('_cur', $cur);
        $this->assign('classlist', $clist);
        $this->assign('student', $stlist);
	$this->display();


     }



















}