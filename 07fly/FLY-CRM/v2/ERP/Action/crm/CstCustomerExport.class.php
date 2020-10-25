<?php
/*
 *
 * crm.CstCustomerExportExport  客户列表数据导出操作   
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
class CstCustomerExport extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->dict=_instance('Action/crm/CstDict');
		$this->user=_instance('Action/sysmanage/User');
		$this->field_ext=_instance('Action/crm/CstFieldExt');
	}	
	
	public function cst_customer(){
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$owner_user_id	= $this->_REQUEST("owner_user_id");
		$create_user_id	= $this->_REQUEST("create_user_id");	
		$where_str = " c.customer_id > 0";
		if( !empty($owner_user_id) ){
			$where_str .=" and c.owner_user_id ='$owner_user_id'";
		}
		if( !empty($create_user_id) ){
			$where_str .=" and c.create_user_id ='$create_user_id'";
		}		
		//**************************************************************************

		$sql = "select c.* from cst_customer as c where $where_str";	
		$list= $this->C($this->cacheDir)->findAll($sql);
		return $list;
	}

	//列表显示
	public function cst_customer_export_show(){
		$sys_user=$this->user->user_list();
		$smarty = $this->setSmarty();
		$smarty->assign(array("sys_user"=>$sys_user));
		$smarty->display('crm/cst_customer_export_show.html');	
	}	
	
	//生成CVS数据
	public function cst_customer_export_cvs(){
		$list=$this->cst_customer();
		$field_ext=$this->field_ext->cst_field_ext_list('cst_customer');
		$tit_ext_cel=array();
		$val_ext_cel=array();
		if(!empty($field_ext)){
			foreach($field_ext as $row){
				$tit_ext_cel[]=$row['show_name'];
				$val_ext_cel[]=$row['field_name'];
			}
		}
		//主要系统字段必须
		$title_cel=array('客户名称','创建人','归属人','最近联系时间','联系内容','下次联系时间','创建时间');
		$title_cel=array_merge($title_cel,$tit_ext_cel); 
		$body_cel =array();
		foreach($list as $key=>$cells){
			$row=array();
			foreach($cells as $jkey=>$cell){
				$row[$jkey]=$cell."\t";//强制转为字符串
			}
			$row['create_user']=$this->user->user_get_one($row['create_user_id']);
			$row['owner_user']=$this->user->user_get_one($row['owner_user_id']);
			
			//必须字段数据
			$main_cel=array( 
							$row['name'],
							$row['create_user']['name'],
							$row['owner_user']['name'],$row['conn_time'],$row['conn_body'],$row['next_time'],$row['create_time']
						   );
			//得到扩展数字段=+增加到字段
			foreach($val_ext_cel as $ext_field){
				$main_cel[]=$row[$ext_field];
			}
			$body_cel[$key]=$main_cel;
		}
		$this->export_to_cvs('客户信息_'.time().'.csv',$title_cel,$body_cel);
		exit;			
	}
		/**
     * @data 2018/1/05
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-test.csv'
     * @param array $tileArray 所有列名称
     * @param array $dataArray 所有列数据
     */
    public function export_to_cvs($filename, $tileArray=array(), $dataArray=array()){
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=".$filename);
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        fputcsv($fp,$tileArray);
        $index = 0;
        foreach ($dataArray as $item) {
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$item);
        }
        ob_flush();
        flush();
        ob_end_clean();
    }
		
}//end class
?>