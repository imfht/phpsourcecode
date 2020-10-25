<?php
/*
 *
 * crm.CstCustomerImportExport  客户列表数据导出操作   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class CstCustomerImport extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->fp=_instance('Extend/File');
		$this->txt=_instance('Extend/FileTxt');
		$this->user=_instance('Action/sysmanage/User');
		$this->field_ext=_instance('Action/crm/CstFieldExt');
	}	

	//列表显示
	public function cst_customer_import_show(){
		if(empty($_FILES)){
			$smarty = $this->setSmarty();
			$smarty->display('crm/cst_customer_import_show.html');			
		}else{
			$date =date("ymd",time());
			$files=$_FILES["filename"];
			$path ="upload/customer/";
			$this->fp->create_dir($path);
			//允许上传文件格式
			$typeArr = array("xlsx", "csv");
			$name 	 = $_FILES['filename']['name'];
			$size   = $_FILES['filename']['size'];
			$name_tmp= $_FILES['filename']['tmp_name'];
			if (empty($name)) {
				echo "您还未选择文件";
				echo '<a href="javascript:window.history.back();">返回</a>';
				exit;
			}
			//获取文件类型
			$file_ext=strtolower(pathinfo($name, PATHINFO_EXTENSION));
			if (!in_array($file_ext, $typeArr)) {
				echo "请上传csv,xlsx类型的文件";
				echo '<a href="javascript:window.history.back();">返回</a>';
				exit;
			}
			//上传大小
			if ($size > 5 * 1024 * 1024) {
				echo "文件大小已超过5m！";
				echo '<a href="javascript:window.history.back();">返回</a>';
				exit;
			}
			$time_str= time() . rand(10000, 99999);
			$pic_name= $time_str . "." . $file_ext;
			$pic_url = $path . $pic_name;
			
			if (move_uploaded_file($name_tmp, $pic_url)) {
				$url = ACT . "/crm/CstCustomerImport/cst_customer_import_cvs/filename/{$pic_name}/action/start/";
				echo '<p>文件上传成功,是否确认导入数据?</p>';
				echo '<p><a href="'.$url.'">确认</a></p>';
				echo '<p><a href="javascript:window.history.back();">返回</a></p>';
			} else {
				echo '上传失败';
			}
		}
		
	}	
	
	//读取CVS数据
	public function cst_customer_import_cvs(){
		$action 	= $this->_REQUEST( "action" );
		$filename 	= $this->_REQUEST( "filename" );
		$start 		= $this->_REQUEST( "start" );
		$total 		= $this->_REQUEST( "total" );
		$start 		= ( $start == 1 ) ? 0 : ( $start - 1 );
		$length 	= 10;
		$info 		= "";
		$isbreak 	= false;
		$dirname 	= ROOT."/upload/customer/";
		$logname 	= "$filename-log.txt";
		$logtxt 	= $this->fp->dir_replace( $dirname . $logname );
		$this->txt->set_file( $logtxt );
		
		if ( $action ) {
			$path = $this->fp->dir_replace( $dirname . $filename );
			$csv = $this->L( 'CsvReader' );
			$csv->set_csv_file( $path );
			$total = $csv->get_lines();
			$total = $total-1;
			$csvarr = $csv->get_data( $length, $start );
			foreach ( $csvarr as $key => $row ) {
				if(count($row)<=1) continue; 
				$data = array(
					"name" => $row[ 0 ],
					"create_user_id" => $this->user->user_get_id($row[ 1 ]),
					"owner_user_id" => $this->user->user_get_id($row[ 2 ]),
					"conn_time" => $row[ 3 ],
					"conn_body" => $row[ 4 ],
					"next_time" => $row[ 5 ],
					"create_time" => $row[ 6 ]
				);
				$nowrow = ++$start;

				
				//排除重复数据
				/*$sql="select car_vin_no from cst_customer where car_vin_no='".$data[ "car_vin_no" ]."'";
				$one=$this->C( $this->cacheDir )->findOne( $sql );
				if ( !empty($one) ) {
					$info .="第{$nowrow}行：" . $data[ "car_vi_no" ] . "输入号已经存在\n";
					continue;
				}*/
				if ( $data[ "name" ] == "客户名称" ) {
					$info .="第{$nowrow}行：" . $data[ "name" ] . "为标题帐号\n";
					continue;
				}
				if ( !empty( $data[ "name" ] ) ) {
					$customer_id=$this->C($this->cacheDir)->insert('cst_customer',$data);
					if ( $customer_id>0 ) {
						$info .="插入数据：第{$nowrow}行：" . $data[ "name" ] . "\n";
					} else {
						$info .="插入数据有误：第{$nowrow}行：" . $data[ "name" ] . "输入数据有误\n";
					}
				} else {
					$info .="跳过数据：第{$nowrow}行：" . $data[ "name" ] . "\n";
				}
				flush();
			} //end foreach
			$this->txt->add_write( "$info" );//写入日志
			if ( $nowrow <= $total ) { 
				//sleep(30);
				$url = ACT . "/crm/CstCustomerImport/cst_customer_import_cvs/filename/{$filename}/action/start/total/{$total}/start/{$nowrow}/";
				echo "<a href='{$url}'>当前执行到第{$nowrow}条记录，如果浏览器没有反应</a>";
				echo "<script language='javascript' type='text/javascript'>";
				echo "window.location.href='$url'";
				echo "</script>";
			} else {
				echo "<p><a href='#'>当前执行到第{$nowrow}条记录，数据导入完成</a></p>";
				//echo "<p><a href='" . ACT . "/crm/CstCustomerImport/user_import_data_log_down/filename/$logname/' target='dwzExport' targetType='navTab'><font color='red'>点击下载导入结果记录</font></a></p>";
			}

		}		
	}
		
}//end class
?>