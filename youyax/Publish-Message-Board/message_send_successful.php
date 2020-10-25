<?php
require("./database.php");
if(isset($_POST['sub'])){
 $sql_tmp="select * from `cms_accounts` where num='".addslashes($_POST['num'])."'";
 $query_tmp=mysql_query($sql_tmp);
 $num_tmp=mysql_num_rows($query_tmp);
 if($num_tmp>0){
 $arr_tmp=mysql_fetch_array($query_tmp);
 
	mysql_query("INSERT INTO `cms_advertiser_pmb` SET             
                      createdDate               = '".time()."',
                      updatedDate               = '".time()."',
                      createdByUserNum          = '".$arr_tmp['num']."',
                      updatedByUserNum          = '".$_SESSION['createdByUserNum']."',
                      message_read				='0',
                      email						='".$_SESSION['email']."',
                      phone						='".$_SESSION['telephone_number']."',
                      message					='".nl2br(addslashes(htmlspecialchars($_POST['ta'])))."',
                      first_name				='".$_SESSION['first_name']."',
                      last_name					='".$_SESSION['last_name']."',
                      organisation				='".$_SESSION['organisation_name']."',
                      subject           		='".addslashes(htmlspecialchars($_POST['subject']))."'"
		      )
      or die("MySQL Error:<br/>\n". htmlspecialchars(mysql_error()) . "\n");
      $mid=mysql_insert_id();       
      mysql_query("update `cms_advertiser_pmb` SET t_num=".$mid." where num=".$mid);
 
    mysql_query("INSERT INTO `cms_user_pmb` SET             
                      createdDate               = '".time()."',
                      updatedDate               = '".time()."',
                      createdByUserNum          = '".$_SESSION['createdByUserNum']."',
                      updatedByUserNum          = '',
                      message_read				='1',
                      email						='".addslashes($arr_tmp['email'])."',
                      phone						='".addslashes($arr_tmp['telephone_number'])."',
                      message					= '',
                      first_name				='".addslashes($arr_tmp['first_name'])."',
                      last_name					='".addslashes($arr_tmp['last_name'])."',
                      organisation				='".addslashes($arr_tmp['organisation_name'])."',
                      subject           		='".addslashes(htmlspecialchars($_POST['subject']))."',
                      t_num						='".$mid."'"
		      )
      or die("MySQL Error:<br/>\n". htmlspecialchars(mysql_error()) . "\n");
      if(!empty($_FILES["attachfile"]["tmp_name"])){
     $fileTypes = array('jpg','jpeg','gif','png','bmp','zip','pdf','txt','doc','xlsx','xls','wps','rar'); // File extensions
	$fileParts = pathinfo($_FILES['attachfile']['name']);	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
			$pos=strrpos($_FILES['attachfile']['name'],"."); 
	 		$font=substr($_FILES['attachfile']['name'],0,$pos);
    	$_FILES['attachfile']['name']=preg_replace("/\s+/","-",$font).substr($_FILES['attachfile']['name'],$pos);
    	$_FILES['attachfile']['name']=preg_replace("/[\x{4e00}-\x{9fa5}]+/u",'',$_FILES['attachfile']['name']);
			if(!is_dir("./imap/example/attachments/".$mid)){
				mkdir("./imap/example/attachments/".$mid, 0777);
			}
			move_uploaded_file($_FILES["attachfile"]["tmp_name"],"./imap/example/attachments/".$mid."/".$_FILES["attachfile"]["name"]);
			if(in_array(strtolower($fileParts['extension']),array('jpg','jpeg','gif','png','bmp'))){
				$m_tmp="img";
			}
    	$atta='<div style="border-top:1px dashed #ddd;height:2px;"></div><ul class="file-icons">';
    	$atta.="<li class='".strtolower($fileParts['extension'])."'><a target='_blank' href='./imap/example/attachments/".$mid."/".$_FILES["attachfile"]["name"]."'>".$_FILES["attachfile"]["name"]."</a></li>";
    	$atta.='</ul>';
    	$mcon=nl2br(addslashes($_POST['ta'].$atta));
    	mysql_query("update `cms_advertiser_pmb` SET message='".$mcon."' where num=".$mid);
		}
	}
    }else{
    		echo file_get_contents("./no_email.html");
			exit;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="3; url=./index.php" />
<title>Publish Message Board -- 发布信息系统</title>
<link rel="icon" href="./favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
<link rel="bookmark" href="./favicon.ico" type="image/x-icon">

<!-- CSS -->
<link href="style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie7.css" /><![endif]-->

<!-- JavaScripts-->
<script type="text/javascript" src="style/js/jquery.js"></script>
<script type="text/javascript" src="style/js/jNice.js"></script>
</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="./index.php"><span>Publish Message Board</span></a></h1>
        
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <p style="float:right;color:#9b9b9b;"><?php echo $_SESSION['email'];?></p>
        <div style="clear:both;"></div>
        <ul id="mainNav">
        	<li><a href="./index.php">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
                
                <div id="main">
					<h3>发送信息成功，正在跳转……</h3>
                </div>
                <!-- // #main -->
                
                <div class="clear"></div>
            <!-- // #container -->
        </div>
        <!-- // #containerHolder -->
        
        <p id="footer">个人自主开发PMB系统. <a href="http://www.youyax.com">Powered BY YouYaX</a></p>
    </div>
    <!-- // #wrapper -->
</body>
</html>
