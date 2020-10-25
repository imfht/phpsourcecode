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
class IndexController extends ComController {
   
   /**
	 *
	 * Enter 导出excel共同方法 ...
	 * @param unknown_type $expTitle
	 * @param unknown_type $expCellName
	 * @param unknown_type $expTableData
	 */
	function  index(){
		//phpinfo();
		$where['tname']=session('username');
		$where['schoolid']=session('schoolid');
		$info=M('te')->where($where)->find();
		//dump($info);
		$this->assign('info',$info);
		$this->display();
	}
	function  main(){
		$num['sel']=M('sel')->count();
		$num['te']=M('te')->count();
		$num['stu']=M('stu')->count();
		$this->assign('num',$num);
		$this->display();
	}
	function  tpass(){

		$this->display();
	}
	function check_tpass(){
    	$pass=I('post.pass','','md5');
    	$where['tname']=session('username');
    	$where['schoolid']=session('schoolid');
    	$tpass=M('te')->where($where)->getField('pass');
    	if ($pass!=$tpass) {
    		$re=0;
    	} else {
    		$re=1;
    	}
    	
    	$this->ajaxReturn($re);
    }
    function do_tpass(){
    	$pass=I('post.pass','','md5');
    	$where['tname']=session('username');
    	$where['schoolid']=session('schoolid');
    	$re=M('te')->where($where)->setField('pass',$pass);
    	$this->ajaxReturn($re);
    }
	/**实现导入excel
	 **/
   function impUser(){
		if (!empty($_FILES)) {
			//import("@.ORG.UploadFile");
			$config=array(
                'exts'=>array('xlsx','xls'),
                'rootPath'=>"./Public/",
                'savePath'=>'Uploads/',
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
				$data['account']= $data['truename'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
				$sex = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
				// $data['res_id']    = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
				$data['class'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
				$data['year'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
				$data['city']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
				$data['company']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
				$data['zhicheng']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
				$data['zhiwu']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
				$data['jibie']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
				$data['honor']= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
				$data['tel']= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
				$data['qq']= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
				$data['email']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
				$data['remark']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
				$data['sex']=$sex=='男'?1:0;
				$data['res_id'] =1;

				$data['last_login_time']=0;
				$data['create_time']=$data['last_login_ip']=$_SERVER['REMOTE_ADDR'];
				$data['login_count']=0;
				$data['join']=0;
				$data['avatar']='';
				$data['password']=md5('123456');
				M('Member')->add($data);
					
			}
			$this->success('导入成功！');
		}else
		{
			$this->error("请选择上传的文件");
		}
			

	}

  


}
