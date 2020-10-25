<?php
class ExeclAction extends CommentAction {
	/**
	 * +----------------------------------------------------------
	 * Export Excel | 2013.08.23
	 * Author:HongPing <hongping626@qq.com> Update for Author:HuangGaoMing <530035210@qq.com>
	 * +----------------------------------------------------------
	 * 
	 * @param $expTitle string
	 *        	File name
	 *        	+----------------------------------------------------------
	 * @param $expCellName array
	 *        	Column name
	 *        	+----------------------------------------------------------
	 * @param $expTableData array
	 *        	Table data
	 *        	+----------------------------------------------------------
	 */
	public function exportExcel($expTitle, $expCellName, $expTableData) {
		$xlsTitle = iconv ( 'utf-8', 'gb2312', $expTitle ); // 文件名称
		$fileName = $xlsTitle . date ( '_YmdHis' ); // or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count ( $expCellName );
		$dataNum = count ( $expTableData );
		vendor ( "PHPExcel.PHPExcel" );
		$objPHPExcel = new PHPExcel ();
		$cellName = array (
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z',
				'AA',
				'AB',
				'AC',
				'AD',
				'AE',
				'AF',
				'AG',
				'AH',
				'AI',
				'AJ',
				'AK',
				'AL',
				'AM',
				'AN',
				'AO',
				'AP',
				'AQ',
				'AR',
				'AS',
				'AT',
				'AU',
				'AV',
				'AW',
				'AX',
				'AY',
				'AZ' 
		);
		
		$objPHPExcel->getActiveSheet ( 0 )->mergeCells ( 'A1:' . $cellName [$cellNum - 1] . '1' ); // 合并单元格
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', $expTitle . '  Export time:' . date ( 'Y-m-d H:i:s' ) );
		for($i = 0; $i < $cellNum; $i ++) {
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $cellName [$i] . '2', $expCellName [$i] [1] );
		}
		// Miscellaneous glyphs, UTF-8
		for($i = 0; $i < $dataNum; $i ++) {
			for($j = 0; $j < $cellNum; $j ++) {
				$objPHPExcel->getActiveSheet ( 0 )->setCellValue ( $cellName [$j] . ($i + 3), $expTableData [$i] [$expCellName [$j] [0]] );
			}
		}
		
		header ( 'pragma:public' );
		header ( 'Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"' );
		header ( "Content-Disposition:attachment;filename=$fileName.xls" ); // attachment新窗口打印inline本窗口打印
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}
	
	/**
	 * +----------------------------------------------------------
	 * Import Excel | 2013.08.23
	 * Author:HongPing <hongping626@qq.com>
	 * +----------------------------------------------------------
	 * 
	 * @param $file upload
	 *        	file $_FILES
	 *        	+----------------------------------------------------------
	 * @return array array("error","message")
	 *         +----------------------------------------------------------
	 */
	public function importExecl($file) {
		if (! file_exists ( $file )) {
			return array (
					"error" => 0,
					'message' => 'file not found!' 
			);
		}
		Vendor ( "PHPExcel.PHPExcel.IOFactory" );
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		try {
			$PHPReader = $objReader->load ( $file );
		} catch ( Exception $e ) {
		}
		if (! isset ( $PHPReader ))
			return array (
					"error" => 0,
					'message' => 'read error!' 
			);
		$allWorksheets = $PHPReader->getAllSheets ();
		$i = 0;
		foreach ( $allWorksheets as $objWorksheet ) {
			$sheetname = $objWorksheet->getTitle ();
			$allRow = $objWorksheet->getHighestRow (); // how many rows
			$highestColumn = $objWorksheet->getHighestColumn (); // how many columns
			$allColumn = PHPExcel_Cell::columnIndexFromString ( $highestColumn );
			$array [$i] ["Title"] = $sheetname;
			$array [$i] ["Cols"] = $allColumn;
			$array [$i] ["Rows"] = $allRow;
			$arr = array ();
			$isMergeCell = array ();
			foreach ( $objWorksheet->getMergeCells () as $cells ) { // merge cells
				foreach ( PHPExcel_Cell::extractAllCellReferencesInRange ( $cells ) as $cellReference ) {
					$isMergeCell [$cellReference] = true;
				}
			}
			for($currentRow = 1; $currentRow <= $allRow; $currentRow ++) {
				$row = array ();
				for($currentColumn = 0; $currentColumn < $allColumn; $currentColumn ++) {
					;
					$cell = $objWorksheet->getCellByColumnAndRow ( $currentColumn, $currentRow );
					$afCol = PHPExcel_Cell::stringFromColumnIndex ( $currentColumn + 1 );
					$bfCol = PHPExcel_Cell::stringFromColumnIndex ( $currentColumn - 1 );
					$col = PHPExcel_Cell::stringFromColumnIndex ( $currentColumn );
					$address = $col . $currentRow;
					$value = $objWorksheet->getCell ( $address )->getValue ();
					if (substr ( $value, 0, 1 ) == '=') {
						return array (
								"error" => 0,
								'message' => 'can not use the formula!' 
						);
						exit ();
					}
					if ($cell->getDataType () == PHPExcel_Cell_DataType::TYPE_NUMERIC) {
						$cellstyleformat = $cell->getParent ()->getStyle ( $cell->getCoordinate () )->getNumberFormat ();
						$formatcode = $cellstyleformat->getFormatCode ();
						if (preg_match ( '/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode )) {
							$value = gmdate ( "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP ( $value ) );
						} else {
							$value = PHPExcel_Style_NumberFormat::toFormattedString ( $value, $formatcode );
						}
					}
					if ($isMergeCell [$col . $currentRow] && $isMergeCell [$afCol . $currentRow] && ! empty ( $value )) {
						$temp = $value;
					} elseif ($isMergeCell [$col . $currentRow] && $isMergeCell [$col . ($currentRow - 1)] && empty ( $value )) {
						$value = $arr [$currentRow - 1] [$currentColumn];
					} elseif ($isMergeCell [$col . $currentRow] && $isMergeCell [$bfCol . $currentRow] && empty ( $value )) {
						$value = $temp;
					}
					$row [$currentColumn] = $value;
				}
				$arr [$currentRow] = $row;
			}
			$array [$i] ["Content"] = $arr;
			$i ++;
		}
		spl_autoload_register ( array (
				'Think',
				'autoload' 
		) ); // must, resolve ThinkPHP and PHPExcel conflicts
		unset ( $objWorksheet );
		unset ( $PHPReader );
		unset ( $PHPExcel );
		unlink ( $file );
		return array (
				"error" => 1,
				"data" => $array 
		);
	}
	
	// SVN导入
	// $import_tb: 要导入的表
	function impSvnManage() {
		$import_tb = $this->_param ( 'import_tb', 'htmlspecialchars', 'svn_manage' ); // 要导入表的名称
		$distiing_string = $this->_param ( 'distiing_string', 'htmlspecialchars', 'account' ); // 数据库中区别的字段
		$excel_string_rownum = $this->_param ( 'excel_string_rownum', 'htmlspecialchars', '1' ); // excel中区别的列(从0开始,4是指第五列)和上面的$distiing_string相对应,相同的数据则不导入
		import ( 'ORG.Util.ExcelToArrary' ); // 导入excelToArray类
		$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
		$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
		$file_type = $file_types [count ( $file_types ) - 1];
		
		/* 判别是不是.xls文件，判别是不是excel文件 */
		if (strtolower ( $file_type ) != "xlsx" && strtolower ( $file_type ) != "xls") {
			$this->error ( '不是Excel文件，请重新上传!' );
		}
		
		/* 设置上传路径 */
		$savePath = C ( 'UPLOAD_DIR' ); // 定义上传路径
		
		/* 以时间来命名上传的文件 */
		$str = date ( 'Ymdhis' );
		$file_name = $str . "." . $file_type;
		
		/* 是否上传成功 */
		if (! copy ( $tmp_file, $savePath . $file_name )) {
			$this->error ( '上传失败' );
		}
		$ExcelToArrary = new ExcelToArrary (); // 实例化
		$res = $ExcelToArrary->read ( C ( 'UPLOAD_DIR' ) . $file_name, "UTF-8", $file_type ); // 传参,判断office2007还是office2003
		                                                                          
		// 查询原有的数据库表种是否存在相同的记录,通过'network_ip'字段来区别
		$disting = M ( "$import_tb" );
		$disting_array = $disting->field ( "$distiing_string" )->select (); // 关联数组
		$disting_arr = array (); // 返回索引数组
		foreach ( $disting_array as $val ) {
			$disting_arr [] = $val ["$distiing_string"];
		}
		
		// 业务逻辑处理
		foreach ( $res as $k => $v ) 		// 循环excel表
		{
			if (isOk_ip ( $v [3] )) {
				if (! in_array ( $v ["$excel_string_rownum"], $disting_arr )) { // 如果不相同,则构建data数组
					$k = $k - 1; // addAll方法要求数组必须有0索引
					$data_arr [$k] ['account'] = $v [1];
					$data_arr [$k] ['password'] = $v [2];
					$data_arr [$k] ['network_ip'] = $v [3];
					$data_arr [$k] ['owner'] = $v [4];
					$data_arr [$k] ['owner_group'] = $v [5];
					$data_arr [$k] ['created_time'] = $v [6];
					$data_arr [$k] ['updated_time'] = $v [7];
					$data_arr [$k] ['directory_pri'] = $v [8];
					$data_arr [$k] ['status'] = $v [9];
					$data_arr [$k] ['note'] = $v [10];
				}
			}
		}
		
		$data = array ();
		foreach ( $data_arr as $val ) {
			$data [] = $val;
		}
		$export_num = count ( $data );
		if ($export_num == "0") {
			$this->error ( '没有最新的导入记录!' );
			exit ();
		}
		$result = $disting->addAll ( $data );
		if (! $result) {
			$this->error ( '导入数据库失败' );
			exit ();
		} else {
			$this->success ( "成功导入" . $export_num . "条记录!" );
		}
	}
	
	// 导入
	// $import_tb: 要导入的表
	function impServerDetail() {
		$import_tb = $this->_param ( 'import_tb', 'htmlspecialchars', 'server_detail' ); // 要导入表的名称
		$distiing_string = $this->_param ( 'distiing_string', 'htmlspecialchars', 'network_ip' ); // 数据库中区别的字段
		$excel_string_rownum = $this->_param ( 'excel_string_rownum', 'htmlspecialchars', '4' ); // excel中区别的列(从0开始,4是指第五列)和上面的$distiing_string相对应,相同的数据则不导入
		import ( 'ORG.Util.ExcelToArrary' ); // 导入excelToArray类
		$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
		$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
		$file_type = $file_types [count ( $file_types ) - 1];
		
		/* 判别是不是.xls文件，判别是不是excel文件 */
		if (strtolower ( $file_type ) != "xlsx" && strtolower ( $file_type ) != "xls") {
			$this->error ( '不是Excel文件，请重新上传!' );
		}
		
		/* 设置上传路径 */
		$savePath = C ( 'UPLOAD_DIR' ); // 定义上传路径
		
		/* 以时间来命名上传的文件 */
		$str = date ( 'Ymdhis' );
		$file_name = $str . "." . $file_type;
		
		/* 是否上传成功 */
		if (! copy ( $tmp_file, $savePath . $file_name )) {
			$this->error ( '上传失败' );
		}
		$ExcelToArrary = new ExcelToArrary (); // 实例化
		$res = $ExcelToArrary->read ( C ( 'UPLOAD_DIR' ) . $file_name, "UTF-8", $file_type ); // 传参,判断office2007还是office2003
		                                                                          
		// 查询原有的数据库表种是否存在相同的记录,通过'network_ip'字段来区别
		$disting = M ( "$import_tb" );
		$disting_array = $disting->field ( "$distiing_string" )->select (); // 关联数组
		$disting_arr = array (); // 返回索引数组
		foreach ( $disting_array as $val ) {
			$disting_arr [] = $val ["$distiing_string"];
		}
		
		// 业务逻辑处理
		foreach ( $res as $k => $v ) 		// 循环excel表
		{
			if (isOk_ip ( $v ["$excel_string_rownum"] )) {
				if (! in_array ( $v ["$excel_string_rownum"], $disting_arr )) { // 如果不相同,则构建data数组
					$k = $k - 1; // addAll方法要求数组必须有0索引
					$data_arr [$k] ['server_name'] = $v [1];
					$data_arr [$k] ['external_ip'] = $v [3];
					$data_arr [$k] ['network_ip'] = $v [4];
					$data_arr [$k] ['system_type'] = $v [6];
					$data_arr [$k] ['cpu'] = $v [7];
					$data_arr [$k] ['mem'] = $v [8];
					$data_arr [$k] ['disk'] = $v [9];
					$data_arr [$k] ['created_time'] = $v [10];
					$data_arr [$k] ['server_id'] = $v [11];
					$data_arr [$k] ['mirr_id'] = $v [12];
				}
			}
		}
		
		$data = array ();
		foreach ( $data_arr as $val ) {
			$data [] = $val;
		}
		$export_num = count ( $data );
		if ($export_num == "0") {
			$this->error ( '没有最新的导入记录!' );
			exit ();
		}
		$result = $disting->addAll ( $data );
		if (! $result) {
			$this->error ( '导入数据库失败' );
			exit ();
		} else {
			// 添加成功在额外增加新的必要字段的值
			$map1 ['depart_name'] = "";
			$map1 ['server_name'] = array (
					'like',
					'[基础]%' 
			);
			$exdata ['depart_name'] = "基础分区";
			$exdata ['status'] = "1";
			$base_update = $disting->where ( $map1 )->save ( $exdata );
			$string_arr = array (
					"B2C",
					"分区一",
					"分区二",
					"分区三",
					"分区四",
					"分区五",
					"分区六" 
			);
			foreach ( $string_arr as $string_val ) {
				$map ['depart_name'] = "";
				$map ['server_name'] = array (
						"like",
						"%$string_val%" 
				);
				$exdata ['depart_name'] = "$string_val";
				$exdata ['status'] = "1";
				$depart_update = $disting->where ( $map )->save ( $exdata );
			}
			
			if ($depart_update == false and $base_update == false) {
				$this->error ( "成功导入" . $export_num . "条记录,但基础分区分区名称失败!" );
				exit ();
			}
			
			$this->success ( "成功导入" . $export_num . "条记录!",U('Admin/ServerDetail/index'));
		}
	}
	
	// 导入准生产表格
	function impServerZw() {
		$import_tb = $this->_param ( 'import_tb', 'htmlspecialchars', 'server_zw' ); // 要导入表的名称
		$distiing_string = $this->_param ( 'distiing_string', 'htmlspecialchars', 'mark' ); // 数据库中区别的字段
		$excel_string_rownum = $this->_param ( 'excel_string_rownum', 'htmlspecialchars', '0' ); // excel中区别的列(从0开始,4是指第五列)和上面的$distiing_string相对应,相同的数据则不导入
		import ( 'ORG.Util.ExcelToArrary' ); // 导入excelToArray类
		$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
		$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
		$file_type = $file_types [count ( $file_types ) - 1];
		
		/* 判别是不是.xls文件，判别是不是excel文件 */
		if (strtolower ( $file_type ) != "xlsx" && strtolower ( $file_type ) != "xls") {
			$this->error ( '不是Excel文件，请重新上传!' );
		}
		
		/* 设置上传路径 */
		$savePath = C ( 'UPLOAD_DIR' ); // 定义上传路径
		
		/* 以时间来命名上传的文件 */
		$str = date ( 'Ymdhis' );
		$file_name = $str . "." . $file_type;
		
		/* 是否上传成功 */
		if (! copy ( $tmp_file, $savePath . $file_name )) {
			$this->error ( '上传失败' );
		}
		$ExcelToArrary = new ExcelToArrary (); // 实例化
		$res = $ExcelToArrary->read ( C ( 'UPLOAD_DIR' ) . $file_name, "UTF-8", $file_type ); // 传参,判断office2007还是office2003
		                                                                          
		// 查询原有的数据库表种是否存在相同的记录,通过'network_ip'字段来区别
		$disting = M ( "$import_tb" );
		$disting_array = $disting->field ( "$distiing_string" )->select (); // 关联数组
		$disting_arr = array (); // 返回索引数组
		foreach ( $disting_array as $val ) {
			$disting_arr [] = $val ["$distiing_string"];
		}
		
		// 业务逻辑处理
		foreach ( $res as $k => $v ) 		// 循环excel表
		{
			if (isOk_ip ( $v [3] )) {
				if (! in_array ( $v ["$excel_string_rownum"], $disting_arr )) { // 如果不相同,则构建data数组
					$k = $k - 1; // addAll方法要求数组必须有0索引
					$data_arr [$k] ['mark'] = $v [0];
					$data_arr [$k] ['depart_name'] = $v [1];
					$data_arr [$k] ['server_name'] = $v [2];
					$data_arr [$k] ['network_ip'] = $v [3];
					$data_arr [$k] ['cpu'] = $v [4];
					$data_arr [$k] ['mem'] = $v [5];
					$data_arr [$k] ['disk'] = $v [6];
					$data_arr [$k] ['pro_type'] = $v [7];
					$data_arr [$k] ['pro_dir'] = $v [8];
					$data_arr [$k] ['config_dir'] = $v [9];
					$data_arr [$k] ['pro_init'] = $v [10];
					$data_arr [$k] ['created_time'] = $v [11];
					$data_arr [$k] ['updated_time'] = $v [12];
					$data_arr [$k] ['note'] = $v [13];
					$data_arr [$k] ['status'] = $v [14];
				}
			}
		}
		
		$data = array ();
		foreach ( $data_arr as $val ) {
			$data [] = $val;
		}
		$export_num = count ( $data );
		if ($export_num == "0") {
			$this->error ( '没有最新的导入记录!' );
			exit ();
		}
		$result = $disting->addAll ( $data );
		if (! $result) {
			$this->error ( '导入数据库失败' );
			exit ();
		} else {
			$this->success ( "成功导入" . $export_num . "条记录!" );
		}
	}
	
	// 导入mysql表
	function impMysql() {
		$import_tb = $this->_param ( 'import_tb', 'htmlspecialchars', 'mysql' ); // 要导入表的名称
		$distiing_string = $this->_param ( 'distiing_string', 'htmlspecialchars', 'mark' ); // 数据库中区别的字段
		$excel_string_rownum = $this->_param ( 'excel_string_rownum', 'htmlspecialchars', '0' ); // excel中区别的列(从0开始,4是指第五列)和上面的$distiing_string相对应,相同的数据则不导入
		import ( 'ORG.Util.ExcelToArrary' ); // 导入excelToArray类
		$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
		$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
		$file_type = $file_types [count ( $file_types ) - 1];
		
		/* 判别是不是.xls文件，判别是不是excel文件 */
		if (strtolower ( $file_type ) != "xlsx" && strtolower ( $file_type ) != "xls") {
			$this->error ( '不是Excel文件，请重新上传!' );
		}
		
		/* 设置上传路径 */
		$savePath = C ( 'UPLOAD_DIR' ); // 定义上传路径
		
		/* 以时间来命名上传的文件 */
		$str = date ( 'Ymdhis' );
		$file_name = $str . "." . $file_type;
		
		/* 是否上传成功 */
		if (! copy ( $tmp_file, $savePath . $file_name )) {
			$this->error ( '上传失败' );
		}
		$ExcelToArrary = new ExcelToArrary (); // 实例化
		$res = $ExcelToArrary->read ( C ( 'UPLOAD_DIR' ) . $file_name, "UTF-8", $file_type ); // 传参,判断office2007还是office2003
		                                                                          
		// 查询原有的数据库表种是否存在相同的记录,通过'network_ip'字段来区别
		$disting = M ( "$import_tb" );
		$disting_array = $disting->field ( "$distiing_string" )->select (); // 关联数组
		$disting_arr = array (); // 返回索引数组
		foreach ( $disting_array as $val ) {
			$disting_arr [] = $val ["$distiing_string"];
		}
		
		// 业务逻辑处理
		foreach ( $res as $k => $v ) 		// 循环excel表
		{
			if (isOk_ip ( $v [11] )) {
				if (! in_array ( $v ["$excel_string_rownum"], $disting_arr )) { // 如果不相同,则构建data数组
					$k = $k - 1; // addAll方法要求数组必须有0索引
					$data_arr [$k] ['mark'] = $v [0];
					$data_arr [$k] ['name'] = $v [1];
					$data_arr [$k] ['network_ip'] = $v [2];
					$data_arr [$k] ['type'] = $v [3];
					$data_arr [$k] ['charset'] = $v [4];
					$data_arr [$k] ['disk'] = $v [5];
					$data_arr [$k] ['created_time'] = $v [6];
					$data_arr [$k] ['updated_time'] = $v [7];
					$data_arr [$k] ['root_passwd'] = $v [8];
					$data_arr [$k] ['insconn_passwd'] = $v [9];
					$data_arr [$k] ['map_server'] = $v [10];
					$data_arr [$k] ['map_network'] = $v [11];
					$data_arr [$k] ['map_port'] = $v [12];
					$data_arr [$k] ['note'] = $v [13];
					$data_arr [$k] ['status'] = $v [14];
				}
			}
		}
		$data = array ();
		foreach ( $data_arr as $val ) {
			$data [] = $val;
		}
		
		$export_num = count ( $data );
		if ($export_num == "0") {
			$this->error ( '没有最新的导入记录!' );
			exit ();
		}
		$result = $disting->addAll ( $data );
		if (! $result) {
			$this->error ( '导入数据库失败' );
			exit ();
		} else {
			$this->success ( "成功导入" . $export_num . "条记录!" );
		}
	}
	/**
	 * 快速导出任何表
	 * 
	 * @param string $xlsName
	 *        	要导出的表的前缀名字
	 * @param string $table
	 *        	要导出的表
	 * @param array $xlsCell
	 *        	二元索引数组对应的字段和中文名称
	 * @param string $expstr
	 *        	要导出的字段
	 */
	function expTable($xlsName, $table, $xlsCell, $expstr) { // 导出Excel
		$xlsModel = M ( "$table" );
		$xlsData = $xlsModel->Field ( "$expstr" )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	function expUser() { // 导出Excel
		$xlsName = "server_detail";
		$xlsCell = array (
				array (
						'id',
						'序号' 
				),
				array (
						'depart_name',
						'分区名称' 
				),
				array (
						'server_name',
						'服务器名称' 
				),
				array (
						'external_ip',
						'公网IP' 
				),
				array (
						'network_ip',
						'内网IP' 
				) 
		);
		$xlsModel = M ( 'server_detail' );
		$xlsData = $xlsModel->Field ( 'id,depart_name,server_name,external_ip,network_ip' )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	// 下载腾讯云服务器统计表
	function exportserverdetail() {
		$xlsName = "腾讯云生产服务器统计表";
		$xlsCell = array (
				array (
						'id',
						'序号' 
				),
				array (
						'depart_name',
						'分区名称' 
				),
				array (
						'server_name',
						'服务器名称' 
				),
				array (
						'external_ip',
						'公网IP' 
				),
				array (
						'network_ip',
						'内网IP' 
				),
				array (
						'cpu',
						'CPU' 
				),
				array (
						'mem',
						'内存' 
				),
				array (
						'disk',
						'硬盘' 
				),
				array (
						'created_time',
						'创建时间' 
				),
				array (
						'note',
						'备注' 
				),
				array (
						'status',
						'使用状态' 
				),
				array (
						'server_id',
						'服务器ID' 
				),
				array (
						'mirr_id',
						'镜像ID' 
				),
				array (
						'system_type',
						'操作系统类型' 
				) 
		);
		$xlsModel = M ( "server_detail" );
		$xlsData = $xlsModel->Field ( 'id,depart_name,server_name,external_ip,network_ip,cpu,mem,disk,created_time,note,status,server_id,mirr_id,system_type' )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	
	// 下载svn统计表
	function exportSvnManage() {
		$xlsName = "svn账号统计表";
		$xlsCell = array (
				array (
						'id',
						'序号' 
				),
				array (
						'account',
						'账号' 
				),
				array (
						'password',
						'密码' 
				),
				array (
						'network_ip',
						'所属于服务器' 
				),
				array (
						'owner',
						'账号所有者' 
				),
				array (
						'owner_group',
						'账号所属组' 
				),
				array (
						'created_time',
						'创建时间' 
				),
				array (
						'updated_time',
						'更新记录时间' 
				),
				array (
						'directory_pri',
						'目录权限' 
				),
				array (
						'status',
						'状态' 
				),
				array (
						'note',
						'备注内容' 
				) 
		);
		$xlsModel = M ( "svn_manage" );
		$xlsData = $xlsModel->Field ( 'id,account,password,network_ip,owner,created_time,updated_time,directory_pri,status,note' )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	
	// 下载准生产表格
	function exportServerZw() {
		$xlsName = "准生产服务器统计表";
		$xlsCell = array (
				array (
						'id',
						'序号' 
				),
				array (
						'depart_name',
						'分区名称' 
				),
				array (
						'server_name',
						'服务器名称' 
				),
				array (
						'network_ip',
						'内网IP' 
				),
				array (
						'cpu',
						'CPU' 
				),
				array (
						'mem',
						'内存' 
				),
				array (
						'disk',
						'硬盘' 
				),
				array (
						'pro_type',
						'应用类型' 
				),
				array (
						'pro_dir',
						'工程目录' 
				),
				array (
						'config_dir',
						'配置文件目录' 
				),
				array (
						'pro_init',
						'启动脚本' 
				),
				array (
						'created_time',
						'创建时间' 
				),
				array (
						'updated_time',
						'更新记录时间' 
				),
				array (
						'note',
						'备注' 
				),
				array (
						'status',
						'使用状态' 
				) 
		);
		$xlsModel = M ( "server_zw" );
		$xlsData = $xlsModel->Field ( 'id,depart_name,server_name,network_ip,cpu,mem,disk,pro_type,pro_dir,config_dir,pro_init,created_time,updated_time,note,status' )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	
	// 下载准生产表格
	function exportMysql() {
		$xlsName = "腾讯云数据库统计表";
		$xlsCell = array (
				array (
						'id',
						'序号' 
				),
				array (
						'name',
						'名称' 
				),
				array (
						'network_ip',
						'数据库地址' 
				),
				array (
						'type',
						'实例类型' 
				),
				array (
						'charset',
						'默认字符集' 
				),
				array (
						'disk',
						'容量' 
				),
				array (
						'created_time',
						'创建时间' 
				),
				array (
						'updated_time',
						'更新记录时间' 
				),
				array (
						'root_passwd',
						'root密码' 
				),
				array (
						'insconn_passwd',
						'insconn密码' 
				),
				array (
						'map_server',
						'映射机器' 
				),
				array (
						'map_network',
						'映射内网地址' 
				),
				array (
						'map_port',
						'映射端口' 
				),
				array (
						'note',
						'备注' 
				),
				array (
						'status',
						'使用状态' 
				) 
		);
		$xlsModel = M ( "mysql" );
		$xlsData = $xlsModel->Field ( 'id,name,network_ip,type,charset,disk,created_time,updated_time,root_passwd,insconn_passwd,map_server,map_network,map_port,note,status' )->select ();
		$this->exportExcel ( $xlsName, $xlsCell, $xlsData );
	}
	
	// 导出数据库表格通用函数
	function export() {
		// 查询数据中表和对应的注释名称->导出execl表格的时候需要中文名称项目
		// 通过information_schema数据库中的columns表中的column_name, column_comment,字段名和注释来查询
		$tb = $this->_param ( 'exptb', 'htmlspecialchars', 'mysql_manage' );
		$expAll = $this->_param ( 'expAll', 'htmlspecialchars', '1' ); // 默认导出所有字段
		$expStr = $this->_param ( 'expString', 'htmlspecialchars', '' ); // 如果只是导出某些字段则需传参,缺省为空
		$xlsName = $tb;
		
		$intb = C ( 'DB_PREFIX' ) . $tb;
		if ($expAll) {
			// 先查询到所有数据
			$xlsModel = M ( "$tb" );
			$xlsData = $xlsModel->select ();
			$Info = M ( "columns", "", "DB_INFO" );
			// 查询id和注释对应关系
			$map ['table_name'] = $intb;
			$xlsCell = $Info->where ( $map )->table ( "columns" )->field ( "column_name, column_comment" )->select ();
			// 转化为索引数组
			for($i = 0; $i < count ( $xlsCell ); $i ++) {
				$each_arr = $xlsCell [$i];
				$new_arr [] = array_values ( $each_arr ); // 返回所有键值
			}
			$new_key [] = array_keys ( $xlsCell [0] ); // 返回所有索引值
			$xlsTranCell = array (
					excel_title => $new_key [0],
					excel_ceils => $new_arr 
			);
			$this->exportExcel ( $xlsName, $xlsTranCell ['excel_ceils'], $xlsData );
		} else {
			// 先查询到所有数据
			$xlsModel = M ( "$tb" );
			$xlsData = $xlsModel->field ( "$expStr" )->select ();
			$Info = M ( "columns", "", "DB_INFO" );
			// 查询id和注释对应关系
			$map ['table_name'] = $intb;
			$map ['column_name'] = array (
					'in',
					"$expStr" 
			);
			$xlsCell = $Info->where ( $map )->table ( "columns" )->field ( "column_name, column_comment" )->select ();
			// 转化为索引数组
			for($i = 0; $i < count ( $xlsCell ); $i ++) {
				$each_arr = $xlsCell [$i];
				$new_arr [] = array_values ( $each_arr ); // 返回所有键值
			}
			$new_key [] = array_keys ( $xlsCell [0] ); // 返回所有索引值
			$xlsTranCell = array (
					excel_title => $new_key [0],
					excel_ceils => $new_arr 
			);
			$this->exportExcel ( $xlsName, $xlsTranCell ['excel_ceils'], $xlsData );
		}
	}
	
	// 导入任何表格通用函数
	function import() {
		$import_tb = $_GET[ 'import_tb']; // 要导入表的名称
		$starline =  $_GET[ 'starline']; // 默认从第三行开始导入
		$distiing_string = $_GET[ 'distiing_string']; // 数据库中区别的字段
		$excel_string_rownum = $_GET[  'excel_string_rownum']; // excel中区别的列(从0开始,4是指第五列)和上面的$distiing_string相对应,相同的数据则不导入

//        dump($import_tb);die();
        import ( 'ORG.Util.ExcelToArrary' ); // 导入excelToArray类
		$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
		$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
		$file_type = $file_types [count ( $file_types ) - 1];
		
		/* 判别是不是.xls文件，判别是不是excel文件 */
		if (strtolower ( $file_type ) != "xlsx" && strtolower ( $file_type ) != "xls") {
			$this->error ( '不是Excel文件，请重新上传!' );
		}
		
		/* 设置上传路径 */
		$savePath = C ( 'UPLOAD_DIR' ); // 定义上传路径
		
		/* 以时间来命名上传的文件 */
		$str = date ( 'Ymdhis' );
		$file_name = $str . "." . $file_type;
		
		/* 是否上传成功 */
		if (! copy ( $tmp_file, $savePath . $file_name )) {
                        var_dump($tmp_file);
			echo "====" ;
			 dump($savePath . $file_name) ;die(); 
			$this->error ( '上传失败' );
		}
		$ExcelToArrary = new ExcelToArrary (); // 实例化
		$res = $ExcelToArrary->read ( C ( 'UPLOAD_DIR' ) . $file_name, "UTF-8", $file_type ); // 传参,判断office2007还是office2003
		                                                                          
		// 查询原有的数据库表种是否存在相同的记录,通过'network_ip'字段来区别
		$disting = M ( "$import_tb" );
		$disting_array = $disting->field ( "$distiing_string" )->select (); // 关联数组
		$disting_arr = array (); // 返回索引数组
		foreach ( $disting_array as $val ) {
			$disting_arr [] = $val ["$distiing_string"];
		}
		
		// 查询该表的字段和注释的值
		$Info = M ( "columns", "", "DB_INFO" );
		$map ['table_name'] = C ( 'DB_PREFIX' ) . $import_tb;
		$xlsCell = $Info->where ( $map )->table ( "columns" )->field ( "column_name, column_comment" )->select ();
		
		// dump($xlsCell);die();
		$arg = array ();
		/*
		 * $arg['id']=0; $arg['apply_server_name']=1; $arg['apply_server_purpose']=2; $arg['apply_people']=3; $arg['isslave']=4;
		 */
		// 表字段和表格的列相对应 默认方式为 所有表字段对应为第一列到最后一列
		foreach ( $xlsCell as $xkey => $xval ) {
			$argkeyname = $xval ["column_name"];
			$arg ["$argkeyname"] = $xkey;
		}
		
		// 业务逻辑处理
		$data_arr = array ();
		foreach ( $res as $k => $v ) 		// 循环excel表
		{
			if ($k >= $starline) {
				if (! in_array ( $v ["$excel_string_rownum"], $disting_arr )) { // 如果不相同,则构建data数组
					$k = $k - 1; // addAll方法要求数组必须有0索引
					foreach ( $xlsCell as $column ) {
						$columnkey = $column ["column_name"];
						// $rightkey=$this->_param("$columnkey");
						// echo $data_arr[$k][$columnkey]"="$v[$rightkey];
						if ($columnkey != "id") {
							if ($arg ["$columnkey"]) {
								// echo $k."-".$columnkey."=".$arg["$columnkey"]."<br/>";
								$numval = $arg ["$columnkey"];
								$data_arr [$k] [$columnkey] = $v [$numval];
							}
						}
					}
				}
			}
		}
		
		$data = array ();
		foreach ( $data_arr as $val ) {
			$data [] = $val;
		}
		
		$export_num = count ( $data );
		if ($export_num == "0") {
			$this->error ( '没有最新的导入记录!' );
			exit ();
		}
		$result = $disting->addAll ( $data );
		if (! $result) {
			$this->error ( '导入数据库失败' );
			exit ();
		} else {
			$this->success ( "成功导入" . $export_num . "条记录!" );
		}
	}
}
