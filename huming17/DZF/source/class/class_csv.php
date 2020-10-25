<?php
/**
 * CSV类 数据导出CSV文件类
 * @author HumingXu E-mail:huming17@126.com
*/


/*
 * 	//DEBUG 类函数调用示例
 *	//导出数据量在300000时候内存占用超过128MB 不过速度还算快 基本在3-5秒左右能够完成(测试环境与excel类相同)
 * 	for($i=1; $i < 300000; $i++){
 * 		$csv_data[$i] = array(1=>"数据你好数据你好",1=>"数据你好数据你好",3=>"数据你好数据你好");
 * 	}
 * 	$csv_name = 'test.csv';
 * 	$csv_path = dirname(__FILE__).'/';
 * 	//DEBUG 导出csv文件
 * 	$csv = new csv($csv_name,$csv_data,$output_way=2,$csv_path);
 * 	$csv->csv_to_file();
 * 	
 * 	//DEBUG 下载csv文件
 * 	$csv = new csv($csv_name,$csv_data);
 * 	$csv->csv_to_httpd();
 *
 *	//DEBUG csv文件转数据 
 *  //TODO 遇到大数据时候如何优化?
 * 	$csv_name = 'test.csv';
 * 	$csv_path = dirname(__FILE__).'/';
 * 	$csv = new csv($csv_name, '', '', $csv_path);
 * 	$ttt = $csv->csv_to_array();
 *
 *
*/

class csv {
	/*
	* @pram $csv_title csv 文件名
	* @pram $csv_data csv 内数据 二维数组
	* @pram $output_way 1 HTTP页面输出下载 2 保存到服务器磁盘
	* @pram $csv_path csv 文件名 注意以 / 结尾
	* @pram $max_line 读写最大行 提高效率 读取CSV用到
	*/
	
	private $csv_name='csv.csv';
	private $csv_data=array();
	private $output_way=1;
	private $csv_path='';
	private $max_line=999999;
 
	//定义一个构造方法 
	function __construct($csv_name,$csv_data,$output_way=1,$csv_path='',$max_line=999999)   
	{     
		$this->csv_name=$csv_name;
		$this->csv_data=$csv_data;  
		$this->output_way=$csv_data;
		$this->csv_path=$csv_path;
		$this->max_line=$max_line;
	}
	public function array_to_csv()
	{
	    $outstream = fopen("php://temp", 'r+');
	    fputcsv($outstream, $this->csv_data, ',', '"');
	    rewind($outstream);
	    $csv = fgets($outstream);
	    fclose($outstream);
	    return $csv;
	}

	public function csv_to_array()
	{
		$csv_array = array();
		if(file_exists($this->csv_path.$this->csv_name) && !empty($this->csv_name)){
			$file = fopen($this->csv_path.$this->csv_name, "r");
			while (!feof($file)) {
				$csv_array[] = fgetcsv($file, $this->max_line, ',', '"');
			}
			fclose($file);
		}
	    return($csv_array);
	}

	//DEBUG 磁盘写入CSV文件函数
	public function csv_to_file(){
		$return = 0;
		if($this->csv_path && $this->csv_name){
			$csv_file = fopen($this->csv_path.$this->csv_name, "w");
		    fwrite($csv_file, chr(0xEF).chr(0xBB).chr(0xBF));//DEBUG 中文乱码问题
		    foreach ($this->csv_data as $key => $value) {
		    	fputcsv($csv_file, $value);
			}
		    fclose($csv_file);
		    
			if(file_exists($this->csv_path.$this->csv_name)){
				$return = 1;
			}
		}
		return $return;
	}

	//DEBUG HTTP下载CSV文件函数
	public function csv_to_httpd(){
		$return = false;
		if($this->csv_name){
			# Set the headers we need for this to work
	        header('Content-Type: text/csv; charset=utf-8');
	        header('Content-Disposition: attachment; filename=' . $this->csv_name);
			header('Cache-Control: max-age=0');
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			# Start the ouput
	        $output = fopen('php://output', 'w');
	        # Then loop through the rows
	        foreach($this->csv_data as $key => $value)
	        {
	            # Add the rows to the body
	            fputcsv($output, $value);
	        }
	        # Exit to close the stream off
	        $return = true;
	        exit();
		}
		return $return;
	}
}