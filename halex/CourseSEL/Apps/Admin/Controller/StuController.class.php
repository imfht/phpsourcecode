<?php
/**
	 * CourseSEL   后台
	 * @Author hxb0810(halexcode)
	 * Email: hxb0810@163.com
	 * Tel:   15534378771
	 * Date:  2017-10-07 21:00
	 * @Tool Sublime
	 * 
	 */
// 本类由系统自动生成，仅供测试用途
namespace Admin\Controller;
use Think\Controller;
class StuController extends ComController {
	public	function  index(){
		//phpinfo();
		
		if (session('type')!=2) {
			//echo session('type');
			$where['schoolid']=session('schoolid');
		}
		$te=D('stu');
			$count      = $te->where($where)->count();
			$Page       = new \Think\Page($count,10);
			$Page->setConfig('prev',  '<span >上一页</span>');//上一页
			$Page->setConfig('next',  '<span >下一页</span>');//下一页
			$Page->setConfig('first', '<span >首页</span>');//第一页
			$Page->setConfig('last',  '<span >尾页</span>');//最后一页
		$show       = $Page->show();// 分页显示输出
		$list = $te->relation(true)->order('sid desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('num',$count);
		$this->assign('stu',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	public function search (){
		$sname=I('post.sname');
		$schoolid=I('post.schoolid');
		$class=I('post.class');
		if (!empty($sname)) {
			$where['sname'] = array('like',$sname.'%');//模糊查询
		}
		if (!empty($class)) {
			$where['class'] = $class;//模糊查询
		}		
		if (session('type')!=2) {
			$where['schoolid']=session('schoolid');
		}else{
			if (!empty($schoolid)) {
			$where['schoolid'] = $schoolid;//模糊查询
			}
		}
		$re=D('Stu')->relation(true)->order('sid desc')->where($where)->select();
		$this->assign('num',count($re));
		$this->assign('stu',$re);
		$this->display('index');
	}
	function user_stop(){
		//$tid=I('get.tid');
		$User = M("Stu"); 
		$where['sid']=I('get.sid');
		// 更改用户的status值
		$re=$User-> where($where)->setField('status',0);
		$this->ajaxReturn($re);
	}
	function user_open(){
		//$tid=I('get.tid');
		$User = M("Stu"); 
		$where['sid']=I('get.sid');
		// 更改用户的status值
		$re=$User-> where($where)->setField('status',1);
		$this->ajaxReturn($re);
	}
	function  stuedit(){
		$where['sid']=(I('get.id'));
		$t=M('Stu')->where($where)->find();
		$this->assign('stu',$t);
		$this->display();
	}
	function do_stuedit(){
		$where['sid']=I('post.sid');
		$data['stuid']=I('post.stuid');
		$data['sname']=I('post.sname');
		$data['sex']=I('post.sex');
		$data['class']=I('post.sclass');
		$data['year']=I('post.year');
		$data['schoolid']=I('post.schoolid');
		if (is_md5(I('post.spass'))) {
			$data['pass']=I('post.spass');
		}else{
			$data['pass']=I('post.spass','','md5');
		}
		$User = M("Stu"); 
		$re=$User-> where($where)->save($data);	 
		$this->ajaxReturn($re);
		//$this->display();
	}
	function  tdel(){
		$where['sid']=I('get.sid');
		$res= M("Stu")->where($where)->delete(); 
		if ($res=false) {
			$re=0;
		} else {
			$re=1;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function stuadd(){
		//$re=I('post.tname');
		//$this->ajaxReturn($re);
		$this->display();
	}
	public function do_stuadd(){
		//$re=I('post.tname');
		$data['stuid']=I('post.stuid');
		$data['sname']=I('post.sname');
		$data['sex']=I('post.sex');
		$data['class']=I('post.sclass');
		//$data['pass']=I('post.pass','','md5');
		$data['schoolid']=I('post.schoolid');
		$data['year']=I('post.year');
		$data['ctime']=time();
		//$re=$data['sname'];
		$User = M("Stu"); 
		$res=$User->add($data);
		if ($res=false) {
			$re=0;
		} else {
			$re=1;
		}	 
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function delall(){
		$tid=I('get.sid');
		 $where = 'sid in('.implode(',',$tid).')';
		 $User = M("stu"); 
		 $num=$User->where($where)->delete();
		 if($num!==false) {
		 	$re=$num;
		 }else{
		  $re=0;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	public	function  import_index(){
		//phpinfo();
		$stuin=M('stuin');
			$count      = $stuin->count();
			$Page       = new \Think\Page($count,10);
			$Page->setConfig('prev',  '<span >上一页</span>');//上一页
			$Page->setConfig('next',  '<span >下一页</span>');//下一页
			$Page->setConfig('first', '<span >首页</span>');//第一页
			$Page->setConfig('last',  '<span >尾页</span>');//最后一页
		$show       = $Page->show();// 分页显示输出
		if (session('type')!=2) {
			//echo session('type');
			$where['schoolid']=session('schoolid');
		}
		$list = $stuin->order('id desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('num',$count);
		$this->assign('stuin',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	public function inadd(){
		$this->display();
	}
	public function do_inadd(){
		//$re=I('post.tname');
		$data['title']=I('post.title');
		$data['schoolid']=I('post.schoolid');
		$data['iner']=session('username');
		$data['intime']=time();
		//$re=$data['sname'];
		$User = M("stuin"); 
		$res=$User->add($data);
		if ($res=false) {
			$re=0;
		} else {
			$re=1;
		}	 
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function delallin(){
		$tid=I('get.id');
		 $where = 'id in('.implode(',',$tid).')';
		 $map= 'inid in('.implode(',',$tid).')';
		 $User = M("stuin"); 
		 $num=$User->where($where)->delete();
		 $numstu=M("Stu")->where($map)->delete();
		 if($num!==false) {
		 	$re=$num;
		 }else{
		  $re=0;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	function  inedit(){
		$where['id']=(I('get.id'));
		$t=M('Stuin')->where($where)->find();
		$this->assign('stuin',$t);
		$this->display();
	}
	function do_inedit(){
		$where['id']=I('post.id');
		$data['title']=I('post.title');
		$data['schoolid']=I('post.schoolid');
		$data['iner']=session('username');
		$data['intime']=time();
		$User = M("Stuin"); 
		$re=$User-> where($where)->save($data);	 
		$this->ajaxReturn($re);
		//$this->display();
	}
	function  indel(){
		$where['id']=I('get.id');
		$map['inid'] =I('get.id');
		$res= M("Stuin")->where($where)->delete();
		$restu=M("Stu")->where($map)->delete();
		if ($res==false) {
			$re=0;
		} else {
			$re=1;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function insearch (){
		$title=I('post.title');
		$schoolid=I('post.schoolid');
		if (!empty($title)) {
			$where['title'] = array('like','%'.$title.'%');//模糊查询
		}		
		if (session('type')!=2) {
			$where['schoolid']=session('schoolid');
		}else{
			if (!empty($schoolid)) {
			$where['schoolid'] = $schoolid;//模糊查询
			}
		}
		$re=M('Stuin')->where($where)->select();
		$this->assign('num',count($re));
		$this->assign('stuin',$re);
		$this->display('import_index');
	}
	public function  up_check(){
		$id=I('get.id');
		$where['inid']=$id;
		$s=M('stu')->where($where)->count();
		$data= $s;
		$this->ajaxReturn($data);
	}
	public function up_stu(){
		 //I('get.id');
		$where['id']=I('get.id');
		$stuin=M('stuin')->where($where)->find();
		//dump($exam_name);
		$this->assign('stuin',$stuin);
		$this->display();
	}
	public function close(){
		$this->display();
	}
	function stuin_stop(){
		//$tid=I('get.tid');
		$User = M("Stuin"); 
		$where['id']=I('get.id');
		// 更改用户的status值
		$map['inid']=I('get.id');
		$re=$User-> where($where)->setField('status',0);
		$restu=M('stu')->where($map)->setField('status',0);
		$this->ajaxReturn($re);
	}
	function stuin_open(){
		//$tid=I('get.tid');
		$User = M("Stuin"); 
		$where['id']=I('get.id');
		// 更改用户的status值
		$map['inid']=I('get.id');
		$re=$User-> where($where)->setField('status',1);
		$restu=M('stu')->where($map)->setField('status',1);
		$this->ajaxReturn($re);
	}
	public function in_stu_excel($inid,$schoolid){
        if (!empty($_FILES)) {
            //import("@.ORG.UploadFile");
            $config=array(
                'exts'=>array('xlsx','xls'),
                'rootPath'=>"./Public/",
                'savePath'=>'Uploads/stu_excel/',
                //'autoSub'    =>    true,
                'subName'    =>    array('date','Ymd'),
            );
            $upload = new \Think\Upload($config);
            //var_dump($upload);exit;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            } /*else {
                //$info = $upload->getUploadFileInfo();
                  
            }
            */
            //var_dump($_FILES);exit;
            vendor("PHPExcel.PHPExcel");
            $file_name=$upload->rootPath.$info['import']['savepath'].$info['import']['savename'];
            //var_dump($file_name);exit;
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=3;$i<=$highestRow;$i++)
            {
                $data['stuid'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                //$sex = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                // $data['res_id']    = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['sname'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data['sex'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $data['class']= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['year']= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                $data['sex']=$data['sex']=='男'?1:0;
                $data['inid']= $inid;
                $data['schoolid']=$schoolid;
                $data['ctime']=time();
                M('stu')->add($data);
            }
            //var_dump($data);
            $this->success('导入成功！','close',1);
            unlink($file_name);
        }else
        {
            $this->error("请选择上传的文件");
        }

    }
	public function do_up(){
		//dump (I('post.'));
		if (IS_POST) {
			$inid=I('post.inid');
			$schoolid=I('post.schoolid');
			$where['inid']=$inid;
			$where['schoolid']=$schoolid;
			$s=M('stu')->where($where)->count();
			//echo $s;
			if ($s>0) {//说明传错了，先删除旧的
				//echo "da";
				$score=M('stu')->where($where)->delete();
				if ($score==false or $score==0) {
					$this->error('旧数据删除失败，请联系开发者！','close',1);
				}
				$this->in_stu_excel($inid,$schoolid);

			} else {
				//echo "xiao";
				$this->in_stu_excel($inid,$schoolid);				
			}
			
		}else{
			$this->error('页面错误！','close',1);
		}
	}
	
	// function  import(){
	// 	echo "批量导入学生";
	// 	$this->display();
	// }
}
