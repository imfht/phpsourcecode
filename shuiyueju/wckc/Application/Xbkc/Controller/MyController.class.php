<?php
namespace Xbkc\Controller;
use Think\Page;
use Think\Upload;
use Think\Controller;

/**
 * 校本课程申报类控制器
 * @author水月居 <singliang@163.com>
 */
class MyController extends Controller {

    /**
     * 用户课程管理
    */
    public function index(){
		
		//echo "查看已申报课程";

		$sclass = I('class')?I('class'):"请选择班级" ; //设置学生班级
		$where=array("status"=>"1","year"=>C('YEAR'));		
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
    /* "添加课程"*/
   public function addCurriculum(){
       $uid=is_login();
       if($uid){
        $my['teacher']=query_user('nickname');
        $my['uid']=$uid;
        $my['year']=date('Y');

        if(IS_POST){
           $curriculum=D('XbCurriculum');
           $data=array(
            'cname'=>I('post.cname'),
            'teacher'=>I('post.teacher'),
            'category'=>I('post.category'),
            'grade'=>I('post.grade'),
            'year'=>I('post.year'),
            'create_time'=> time(),
            'number'=>I('post.number'),
            'room'=>I('post.room'),
            //'uid'=>I('post.uid'),
            );
             //dump($data); 
             $curriculum=D('XbCurriculum');
           if($curriculum->create($data)){
                 if($curriculum->add()){
                     $this->success('申报成功。' , 'desCurriculum');
                   }
           }else{
             $this->error($curriculum->getError());
           }
  
                             
            }

       }else{
          $this->error('请先登录再申报',U('/Home/User/login'),3);
          }
          $this->assign(my,$my);
       $this->display();
      }
      /*课程描述description*/
    public function desCurriculum($uid=0){
               $uid=is_login();
               $map['cid']=query_user('cid');
               //dump($map);
                $curdes=D('XbCurriculumDescription');
                if ($uid != 0) {
                  $result = $curdes->where($map)->select();
                } else {
                   $this->redirect('/Home/User/login');
                    //$result = $this->weiboApi->listAllWeibo($page, $count, '', $loadCount, $lastId, $keywords);
                }
              
              //dump($uid);
               if(IS_POST){
                 $curdes=D('XbCurriculumDescription');
                              $curdes->create();
                         //$se=I('post.cid');
                          //dump($curriculum->create());
                         $curriculum->save();   
                  }
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
        $map['year']=I('year')?I('year'):C('CUR_YEAR');
        $map['status']=1;
        $clist=$curriculum->field("cid, cname, teacher")->where($map)->select();
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
        $cur=$curriculum->find($cid); 
        $this->assign('_cur', $cur);
        $this->assign('clist', $clist);
        $this->assign('student', $stlist);
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

    //显示所有课程资料上传列表
    public function uploadindex(){   

        $where=array("status"=>"1","year"=>C('CUR_YEAR'));      
        $curriculum=D("XbCurriculum");
        $list=$curriculum
        //->field("cid, teacher, name, uid")
        ->order("category asc")->where($where)->select();
        
        //dump($list);
        //$cateid=array($category['id'],true);
        $this->assign('clist', $list);       
        $this->meta_title = '课程信息';
        $this->display();
    }
    public function showUpload(){
    	$uid=is_login();
		 $order=array('id'=>'desc','cid'=>'asc');
		 $map["cid"]=I('cid')?I('cid'):0;//获取cid
		 if(!$map["cid"]) unset($map["cid"]);

		 $map['year']=C('CUR_YEAR');
    	$xbfiles=D('Xb_files')->field('id,cate,cid,cname,teacher,update_time,filename,url,uid')->where($map)->order($order)->select();
    	dump($xbfiles);
    	$this->assign('kecheng',$xbfiles);
    	$this->display();
    }
    public function myDoc(){
        $uid=is_login();
           if ($uid != 0) {
                 $map['cid']=I('cid')?I('cid'):query_user('cid');
                 $map['year']=C('CUR_YEAR');
                 $order=array('id'=>'desc');
                 $xbfiles=D('Xb_files')->field('id,cate,cid,cname,teacher,update_time,filename,url,uid')->where($map)
                          ->order($order)->select();
                 $this->assign('kecheng',$xbfiles);
                 
                 } else {
                $this->redirect('/Home/User/login');                   
              }
     //$map["cid"]=I('cid')?I('cid'):0;//获取cid
     //if(!$map["cid"]) unset($map["cid"]);
     //判断是否登录获取用户信息
           $this->display();
    }



/*上传资料 */

   public function upload(){
      $cid=I('cid')?I('cid'):$cid;//获取cid
          if(IS_POST){
               if(!$_POST['cate']){
                $this->error("请选择上传类别后再上传");
               }
            header("Content-type: text/html; charset=utf-8");           

           /**如果目录不存在则自动创建**/
            $root_path= './Uploads/Xiaoben/';
            $uppath   = $root_path.C('CUR_TERM').'/';
            $uppath_gk=iconv('utf-8', 'gbk', $uppath);  //将目录转化gbk  SERVER_CODE
            if(!file_exists($uppath_gk)) {
                $up='./Uploads/Xiaoben/';
                if(!file_exists($up)) mkdir($up);
                $up.= '/'.C('CUR_TERM');
                if(!file_exists($up)) mkdir($up);
                mkdir($uppath_gk);//如果不存在则设定目录
                echo '目录创建成功';
             }

       $save=I('cate').I('cid').I('cname').I('teacher').date(ymd);//保存文件的命名规则
       $fileconfig=array(
               'exts'      => array('zip','rar','doc','docx','ppt','pptx','pdf'),// 设置附件上传类型
               'maxSize'   => 30*1024*1024 ,// 设置附件上传大小 
               'rootPath'  => $root_path.'/', // 设置附件上传根目录
               'savePath'  => C('CUR_TERM').'/', // 设置附件上传（子）目录
               'autoSub'   => false,
               'saveName'  =>iconv('utf-8', 'gbk', $save), //保存文件的命名规则
               'autoSub'  =>false,
               'Replace'  =>true,
               );
       $upload = new \Think\Upload($fileconfig);//实例化上传类 
       $info   =  $upload->upload();
				 //$file=$_FILES['upfile'];
				 $file['ext']    =   pathinfo($_FILES['upfile']['name'], PATHINFO_EXTENSION);

				    if(!$info) {// 上传错误提示错误信息
				        $this->error($upload->getError());
				    }else{// 上传成功
				  //保存表单数据
				        $url= C('CUR_TERM').'/'.$save.'.'.$file['ext'];      
				    	$data=array(    
				    		             "url" => $url,
				    		             'cate'=> I('cate'),
				                     "cid" =>   I('cid'),
				                     "teacher"=>I('teacher'),
				                     "filename"=>$_FILES['upfile']['name'],
				                     "cname"     => $_POST["cname"],  
				                     "uid" =>   I('uid'),
				                     "year"=>C('CUR_YEAR'),
				                     "update_time" => date(Ymd)
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
   
	   $this->display(); 
   
   }

		/*提交学员成绩*/
	public function upScore($cid=0){
	
       $uid=is_login();
       if(0<$uid){

        $mymap=array(
        	"uid"=>$uid,
        	'year'=>C('CUR_YEAR'),
        	);
        $my=M('xb_curriculum')->field('cid,teacher')->where($mymap)->find();
        $my['teacher2']=query_user('nickname');
        $my['uid']=$uid;
       // dump($my);

        $this->assign(my,$my);               
          }else{
          $this->error('请先登录再上报成绩',U('/Home/User/login'),3);
       }



		  $cid=I('cid')?I('cid'):$my['cid'];//获取cid
		  $map['year']=C('CUR_YEAR')?C('CUR_YEAR'):date('Y');
          $map['status']=1;
		  $list=M('XbCurriculum')
        ->field("cid, cname, teacher,uid")
        ->order("category asc")->where($map)->select();

		  //获取当前课程学生
	     $studata=M('XbStudent')->where(array(cid=>$cid))->field('sid,class,code,name,sex,score,outstand,scoreuptime')->select();
	     //dump($studata);
	     $this->assign('student',$studata);
	     //获取当前课程信息
		    $this->_cur=D('XbCurriculum')->find($cid);
        
         $this->assign('list',$list);
          if(IS_POST){
          	// dump($_POST);
          	// die();
          	$p=$_POST;
            //转换数组为更新格式
          	for($i=1;$i<=count($p['sid']);++$i){          		
          	$data[$i]['sid']=$p['sid'][$i];
          	$data[$i]['score']=$p['score'][$i];
          	$data[$i]['outstand']=$p['out'][$i];
          	$data[$i]['scoreuptime'] = NOW_TIME;        
             }         
        	//dump($data);
        	//exit;
                // $student->create($data);
          //$student->save($data);
          //echo $student->getLastSql() ;

          $student=M('XbStudent');
        
          foreach ($data as $v) {          

         	if($student->save($v)=== false) $this->error("数据保存失败");
            }
              $this->success('更新成功', U('My/upScore'));

          }
          $this->display();
	}
    //删除文档
	public function delete(){
	$uid=is_login();
	$id=I('id');
	if($id==""){
		$this->error("参数错误");
	}else{
		$filesModel=M('XbFiles');
		$file=$filesModel->find($id);
		// dump($file);die();
		//权限验证是否为本人或管理员为1
			if($file['uid']==$uid||$uid==1){
				$url="./Uploads/Xiaoben/".$file['url'];
				
				$fileurl=iconv('utf-8', 'gbk', $url);//'2015A/自编教材132变废为宝江莎莎151112.docx'
		    if(unlink($fileurl)){
		    	$rid=$filesModel->delete($id);

		    	$this->success($rid.'删除成功', U('My/mydoc')); 
				dump($url);die();
				  }else{
	                $this->error('文件删除错误，可能是文件已经被删除！');

                  }
			}else{
				$this->error("对不起，你不能删除别人上传的文档！");
			}
    } 





	}



}