<?php
/*
* 对 PHPEXCEL 使用的一个简单封装
* @class name excel
* @author xuhm
* @email huming17@126.com
*/

/*
* 简单PHPExcel EXCEL插件使用,对于大数据超过6万数据的导出服务器内存、CPU和导出时间消耗大

服务器标识 Windows NT XP-2011052154KJ 5.1 build 2600	
机器配置 "CPU型号 [2核]	Intel(R) Celeron(R) CPU E3400 @ 2.60GHz"	
服务器地址	local	
服务器解译引擎 Apache/2.2.9	
PHP运行方式	FPM-FCGI	
脚本占用最大内存 256M	
POST最大限制 80M	
脚本超时 30秒	
socket超时时间 30秒	
PHP版本	5.2.6	
MySQL 5.1.60	
Nginx 1.0.15	
测试请求地址 local	
		
第三方组件	     版本	    是否使用
Zend版本	     2.3.0	     YES
eAccelerator	 0.9.5.3	 YES
XCache		     NO
ZendGuardLoader	 NO
ioncube		     NO
APC		         NO

编号	生成数据量	内存消耗[MB]	文件大小[KB] 输出处理时间[秒]  输出文件类型
						
1		10000		25				406				3				xls
2		50000		120				2099			16.8			xls
3		65535		129				2757			16.8			xls
4		100000		230				977				42				xlsx
5		150000		420				1474			71				xlsx
6		200000		500				1971			103				xlsx
7		300000		700				2964			173				xlsx
8		400000		1024			3957			296				xlsx
9		500000		1200			

该次测试目的以及结果性能的评价：						
	1、当导出数据量达到50000以上时候，单进程内存占用可能超过128M					
	2、大数据量导出，CPU/内存消耗相对都较高，而且内存消耗和导出数量成正比					
建议：						
	1、如果处理小数据量的数据或这单页类型的数据统计分析处理后的格式导出，PHPExcel处理功能比较完善丰富					
	2、对于大量的数据导出建议分块导出或者采用其他格式导出					
*/

//DEBUG 数据赋值示例
/*
$excel_file_name = '测试文档';//文件名
$excel_sheet_title = '测试文档';//数据标签页名

//EXCEL数据 [注:如果 $excel_data_type==2 那么数据随机排列, 不根据KEY值排列]
$excel_data = array(
	0 => array('A1'=>'标题1', 'B1'=>'标题2', 'C1'=>'标题3'),
	1 => array('A2'=>'数据11', 'B2'=>'数据12', 'C2'=>'数据13'),
	2 => array('A3'=>'数据21', 'B3'=>'数据22', 'C3'=>'数据23'),
	3 => array('AA1'=>'标题1', 'AB1'=>'标题2', 'AC1'=>'标题3'),
	4 => array('AA2'=>'数据11', 'AB2'=>'数据12', 'AC2'=>'数据13'),
	5 => array('AA3'=>'数据21', 'AB3'=>'数据22', 'AC3'=>'数据23'),
);
*/

//大数据导出测试
//for($i=1; $i < 100000; $i++){
//	$excel_data[$i] = array('A'.$i=>'数据'.$i);
//}

//DEBUG 类函数调用示例
//DEBUG 类函数调用示例
//require SITE_ROOT.'./source/lib/excel/class_excel.php';
//$excel = new excel($excel_file_name, $excel_sheet_title, $excel_data);
//$excel->output_excel();

class excel {
	
	/*
	* @pram $excel_file_name excel文件名
	* @pram $excel_sheet_title excel数据sheet名
	* @pram $excel_data excel内数据
	* @pram $output_way 1 页面输出下载 2 保存到服务器磁盘
	* @pram $output_type Excel2007 Excel5(Excel2003格式最大行65536限制 列256)
	* @pram $excel_data_type 1 自定义格式 即示例格式 定义好行列位置 如A1 B1; 2 自动排列格式 纯二位数组 程序自定排列
	*/
	
	private $excel_sheet_title='';
	private $excel_data=array();
	private $output_type='Excel5';
	private $output_way=1;
	private $output_path='';
	private $excel_data_type=1;
	private $excel_data_auto=array();
	private $excel5_max_rows = 65535; //最大行
	private $excel5_max_columns = 256; //最大列
 
	//定义一个构造方法参数为$excel_data_type   
	function __construct($excel_file_name, $excel_sheet_title, $excel_data, $output_type='Excel5', $output_way=1, $output_path='',$excel_data_type=1,$excel5_max_rows=65535,$excel5_max_columns=256)   
	{   
		//通过构造方法传进来的参数给属性$this->excel_file_name 等赋初使值   
		$this->excel_file_name=$excel_file_name;
		$this->excel_sheet_title=$excel_sheet_title;  
		$this->excel_data=$excel_data;
		$this->output_type=$output_type;
		$this->output_way=$output_way;
		$this->output_path=$output_path;
		$this->excel_data_type=$excel_data_type;
		$this->excel_data_auto=$excel_data_auto;
		$this->excel5_max_rows=$excel5_max_rows;
		$this->excel5_max_columns=$excel5_max_columns;
	}


	public function output_excel(){
		/** Error reporting */
		@set_time_limit(0);             
		@set_magic_quotes_runtime(0);   
		//TODO 后期通过传输过来的数据量来初始化需要用到的内存大小
		ini_set('memory_limit', '256M');

		/** Include PHPExcel */
		if(!$this->output_path){
			$this->output_path = __FILE__;
		}
		require_once dirname(__FILE__) . '/phpexcel/PHPExcel.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Excel Document")
									 ->setLastModifiedBy("Excel Document")
									 ->setTitle("Excel Document")
									 ->setSubject("Excel Document")
									 ->setDescription("Excel Document")
									 ->setKeywords("Excel Document")
									 ->setCategory("Excel Document");
		//DEBUG 自动分配数据位置
		if(2==$this->excel_data_type){
			$this->excel_data_auto_format($this->excel_data);	
		}

		// Add data
		$i=1;
		foreach($this->excel_data AS $key => $value){
			foreach($value AS $k => $v){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($k, $v);
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle($this->excel_sheet_title);
		$objPHPExcel->setActiveSheetIndex(0);

		//页面输出
		if(1==$this->output_way){
			if('Excel2007'==$this->output_type){
				header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//输出 Excel2007
				header('Content-Disposition: attachment;filename="'.$this->excel_file_name.'.xlsx"');
			}elseif('Excel5'==$this->output_type){
				header('Content-Type: application/vnd.ms-excel');//输出 Excel2003
				header('Content-Disposition: attachment;filename="'.$this->excel_file_name.'.xls"');
			}else{
				header('Content-Type: application/download');//输出文件
				header('Content-Disposition: attachment;filename="'.$this->excel_file_name.'.xls"');
			}
			header('Cache-Control: max-age=0');
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->output_type);
			$objWriter->save('php://output');
			exit;
		}
		//保存到服务器磁盘
		if(2==$this->output_way){
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->output_type);
			$objWriter->save($this->output_path);
			//TODO 校验生成文件完整性
			return 1;//DEBUG 生成成功
		}
	}
	
	public function excel_data_auto_format(){
		//DEBUG 最大列 256 列允许 结尾列为 IV ,如果设置 Excel5 行数仅输出 65535 行
		//TODO 26字母排列组合定位 固定下来 对于 Excel5 目前支持列数为 702 列如有需要可以再扩充
		$pos = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$pos_sub = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$excel_position = array();
		$j = 1;
		$sig_break = 0;
		
		foreach($pos AS $key => $value){
			$i=1;
			if('Excel5'==$this->output_type){
				foreach($pos_sub AS $k => $v){
					if('I'==$value && 'V'==$v){
						$sig_break = 1;
					}
					$excel_position[$j] = $value.$v;	
					$i++;
					$j++;
					if($sig_break){
						break;
					}
				}
				if($sig_break){
					break;
				}
			}
			if('Excel2007'==$this->output_type){
				foreach($pos_sub AS $k => $v){
					$excel_position[$j] = $value.$v;	
					$i++;
					$j++;
				}
			}
		}
		
		$row=1;
		
		//DEBUG 循环行
		foreach($this->excel_data AS $key => $value){
			//DEBUG 循环列
			$column=1;
			$sig_break = 0;
			foreach($value AS $k => $v){
				$this->excel_data_auto[$key][$excel_position[$column].$row]=$v;
				$column++;
				if('Excel5'==$this->output_type && $column > $this->excel5_max_columns){
					$sig_break = 1;
				}
				if($sig_break){
					break;
				}
			}
			$sig_break = 0;
			$row++;
			if('Excel5'==$this->output_type && $row > $this->excel5_max_rows){
				$sig_break = 1;
			}
			if($sig_break){
				break;
			}
		}
		$this->excel_data = $this->excel_data_auto;
	}
}