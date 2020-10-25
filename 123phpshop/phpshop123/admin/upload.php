<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php 

if($_SERVER['REQUEST_METHOD']=='POST' && $_FILES['upload']['name']!=''){
		// 我们这里需要对上传文件进行检查
  		include($_SERVER['DOCUMENT_ROOT'].'/Connections/lib/upload.php'); 
	  
		$up = new fileupload();
		//设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
		$up -> set("path", $_SERVER['DOCUMENT_ROOT']."/uploads/");
		$up -> set("maxsize", 2000000);
		$up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
		$up -> set("israndname", true);
	  	
		//使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
		if($up->upload("upload")) {
		   $image_path="/uploads/".$up->getFileName();
		   $callback = $_REQUEST["CKEditorFuncNum"];  
 			echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$image_path."','');</script>";  die; 
		}else {
			echo '<pre>';
			//获取上传失败以后的错误提示
			var_dump($up->getErrorMsg());
			echo '</pre>';
			die;
		}
}
