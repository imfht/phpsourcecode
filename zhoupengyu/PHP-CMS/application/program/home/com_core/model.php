<?php
/** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：数据模型
 ** ***********************/
class coreModelHome extends coreFrameworkModel {
	function test(){
		//默认的数据库源
		$sql=SqlToolsClass::SelectItem("admin");
		$datas=$this->GetAll($sql);
		
		//切换数据库源
		/*$sql=SqlToolsClass::SelectItem("activities_company_bind");
		$datas=$this->GetRow($sql,"test");*/
		
		//切换回默认的数据源
		$sql=SqlToolsClass::SelectItem("admin");
		$datas=$this->GetAll($sql);
	}
}
?>