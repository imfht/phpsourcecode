<?php
class coreControllerAdmin extends coreFrameworkController
{
	function __construct()
	{
		parent::__construct();
		$this->checkAuth();
	}
	
	function setExceptionList()
	{
		$this->auth = array(			
			"login" => 'NONE'
			,"loginCheck" => "NONE"
			,"logOut" => "NONE"
			,"main" => "NONE"
			,"adminPass" => "NONE"
			,"savePass" => "NONE"
			,"ShowMessage" => "NONE"
			,"groupForm" => "admin-core-group-insert"
			,"saveGroup" => "admin-core-group-insert"
			,"delGroup" => "admin-core-group-delete"
			,"saveConfig" => "admin-core-system-insert"
			//,"menu" => "admin-core-menu-insert"
			,"saveMenu" => "admin-core-menu-insert"
			,"delMenu" => "admin-core-menu-delete"
				
			,"saveManagerMenu" => "admin-core-menuManager-insert"
			,"delManagerMenu" => "admin-core-menuManager-delete"
			//,"delImagesCache" => "HTTP_REFERER"
			,"adminForm" => "admin-core-adminList-insert"
			,"saveAdminData" => "admin-core-adminList-insert"
			,"delAdmin" => "admin-core-adminList-delete"
			,"ajaxUploadImg" => "NONE"
		);
	}
}

?>