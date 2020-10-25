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
class DataController extends ComController {
   
   
	function  index(){
		//phpinfo();
		//echo "数据分析";
		$this->display();
	}
	function search(){
		$where['schoolid']=I('post.schoolid');
		$where['seid']=I('post.seid');
		$class=I('post.class');
		if (!empty($class)) {
			$where['class']=I('post.class');
		}
		$stusel=D('selstu')->relation(true)->order('id desc')->where($where)->select();
		//dump($stusel);
		$selstu=M('selstu');
		$data=$selstu->where($where)->field('course,count(id) as course_num')->group('course')->select();
		$course=i_array_column($data, 'course');
		$course_num=i_array_column($data, 'course_num');
		$kemu=array('物','化','生','政','史','地');
		$selcount=array();
		for ($i=0; $i < count($kemu); $i++) { 
			//echo $kemu[$i];
			$map['course']=array('like','%'.$kemu[$i].'%');
			$selcount[]=$selstu->where($where)->where($map)->count();
		}
		session('where',$where);
		//dump($selcount);
		$this->assign('jscount',json_encode($selcount));
		$this->assign('jscourse',json_encode($course));
        $this->assign('jscourse_num',json_encode($course_num));
		$this->assign('num',count($stusel));
		$this->assign('stusel',$stusel);
		$this->display('index');
		//$where['sname'] = array('like',$sname.'%');//模糊查询
	}
	function get_sel(){
		$schoolid=I('post.schoolid');
		$sel=M('sel')->where('schoolid='.$schoolid)->select();
		$this->ajaxReturn($sel,'JSON');
	}
	public function outexcel($expTitle,$expCellName,$expTableData){
		$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
		//$fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		vendor("PHPExcel.PHPExcel");
			
		$objPHPExcel = new \PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

		$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
		}
		// Miscellaneous glyphs, UTF-8
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
			}
		}

		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 *
	 * 导出Excel
	 */
	public function download(){//导出Excel
		$schoolid=session('where.schoolid');
		$xlsName  = "CourseSEL_".getschool($schoolid)."选课数据";
		$xlsCell  = array(
		array('id','选课id'),
		array('sid','学生id'),
		array('seid','项目id'),
		array('stuid','学籍号'),
		array('sname','姓名'),
		array('sex','性别'),
		array('class','班级'),
		array('schoolid','学校'),
		array('year','入学年'),
		array('course','所选科目'),
		array('stime','最后选课时间'),
		);
		//$xlsModel = D('Member');
		// echo date('Y-m-d H:i:s',time());
		// echo date('Y-m-d H:i:s','1509630684');
		// $xlsData  = $xlsModel->Field('id,truename,sex,res_id,sp_id,class,year,city,company,zhicheng,zhiwu,jibie,tel,qq,email,honor,remark')->select();
		$where=session('where');
		if(empty($where)){
		$stusel=null;
		}else{
		$stusel=D('selstu')->relation(true)->order('id desc')->where($where)->select();
			foreach ($stusel as $k => $v)
			{
				$stusel[$k]['sex']=$v['sex']==1?'男':'女';
				$stusel[$k]['schoolid']=getschool($v['schoolid']);
				$stusel[$k]['stime']=date('Y-m-d H:i:s',$v['stime']);
				$stusel[$k]['stuid']=$v['stuid'].' ';
			}
		}
		//var_dump($xlsData);
		//$this->exportExcel($xlsName,$xlsCell,$xlsData);
		$this->outexcel($xlsName,$xlsCell,$stusel);
		session('where',null);
			
	}
}
