<?php
require("./database.php");
$_SESSION['token'] = md5(microtime(true));
$update_bz=0;
$update_nor=0;
	$msql="select * from cms_advertiser_pmb t where t.t_num='".intval($_GET['pnum'])."'";
	$mquery=mysql_query($msql);
	$mnum=mysql_num_rows($mquery);
	if($mnum>0){
		$result=mysql_fetch_array($mquery);
		$result_2='';
		if($_SESSION['createdByUserNum']!=$result['createdByUserNum']){
			$msql_2="select * from cms_user_pmb t where t.t_num='".intval($_GET['pnum'])."'";
					$mquery_2=mysql_query($msql_2);
					$mnum_2=mysql_num_rows($mquery_2);
					if($mnum_2>0){
						$result_2=mysql_fetch_array($mquery_2);
						if($_SESSION['createdByUserNum']!=$result_2['createdByUserNum']){
						echo file_get_contents("./no_permission.html");
						exit;
					}
						if($result_2['message_read']=='0'){
							mysql_query("update cms_user_pmb set message_read=1 where t_num='".intval($_GET['pnum'])."'");
						}
					}else{					
			echo file_get_contents("./no_permission.html");
			exit;
		}
	}
		if(empty($result_2)){
			if($result['message_read']=='0'){
				mysql_query("update cms_advertiser_pmb set message_read=1 where t_num='".intval($_GET['pnum'])."'");
			}
		}else{
			if($result['message_read']=='0'){
				mysql_query("update cms_user_pmb set message_read=1 where t_num='".intval($_GET['pnum'])."'");
			}
		}
	}else{
				echo file_get_contents("./no_record.html");
				exit;
	}
$marr=array();
		$msql2="select * from cms_advertiser_pmb r where r.r_num='".intval($_GET['pnum'])."' order by num desc";
		$mquery2=mysql_query($msql2);
		$mnum2=mysql_num_rows($mquery2);
		if($mnum2>0){
			while($result2=mysql_fetch_array($mquery2)){
				$marr[]=$result2;
			}
		}
	if(isset($_POST['sub'])){
		$msql2="select * from cms_advertiser_pmb r where r.r_num='".intval($_POST['hid'])."' order by num desc";
		$mquery2=mysql_query($msql2);
		$mnum2=mysql_num_rows($mquery2);
		if($mnum2>0){
			$result2=mysql_fetch_array($mquery2);
			$uid=$result2['createdByUserNum'];
  		$tmp="select first_name,last_name,email from cms_accounts where num='".$uid."' order by num desc";
			$query_tmp=mysql_query($tmp);
			$num_tmp=mysql_num_rows($query_tmp);
			if($num_tmp>0){
				$arr_tmp=mysql_fetch_array($query_tmp);
			}
		}
if(!empty($result_2)){
	$toemail=$result_2['email'];
	$toname=$result_2['first_name'].' '.$result_2['last_name'];
}else{
	$toemail=$result['email'];
	$toname=$result['first_name'].' '.$result['last_name'];
}

	if(!empty($_POST['attaches_pmb'])){
		$atta='<div style="border-top:1px dashed #ddd;height:2px;"></div><ul class="file-icons">';
		$attaches_pmb=explode("attach::attach",$_POST['attaches_pmb']);
		array_pop($attaches_pmb);
		foreach($attaches_pmb as $m){
			$m_tmp=substr($m,strrpos($m,".")+1);
			if(in_array($m_tmp,array('jpg','jpeg','gif','png','bmp'))){
				$m_tmp="img";
			}
			if(!is_dir("./imap/example/attachments/".intval($_POST['hid']))){
				mkdir("./imap/example/attachments/".intval($_POST['hid']), 0777);
			}
			$atta.="<li class='".$m_tmp."'><a target='_blank' href='./imap/example/attachments/".intval($_POST['hid'])."/".$m."'>".$m."</a></li>";
		}
		$atta.='</ul>';
	}else{
		$atta='';
	}
	$tmp_sql="select * from `cms_user_pmb` where t_num='".intval($_POST['hid'])."'";
	$tmp_num=mysql_num_rows(mysql_query($tmp_sql));
	if($tmp_num<=0){
  	$sql="insert into `cms_advertiser_pmb`(createdDate,createdByUserNum,email,message,r_num)  values('".time()."','".$_SESSION['createdByUserNum']."','".addslashes($_SESSION['email'])."','".addslashes(nl2br($_POST['ta']).$atta)."','".addslashes(intval($_POST['hid']))."')";
    mysql_query($sql);
    
    $cnum_sql="select * from `cms_accounts` where email='".$result['email']."'";
    $cnum_arr=mysql_fetch_array(mysql_query($cnum_sql));
    $createdByUserNum_copy=$cnum_arr['num'];
    $updatedByUserNum_copy=$cnum_arr['num'];
    $email_copy=$_SESSION['email'];
    $phone_copy=$_SESSION['telephone_number'];
    $message_copy=nl2br(addslashes(htmlspecialchars($_POST['ta'])));
    $first_name_copy=$_SESSION['first_name'];
    $last_name_copy=$_SESSION['last_name'];
    $organisation_copy=$_SESSION['organisation_name'];
    $subject_copy=$result['subject'];;
    
   mysql_query("INSERT INTO `cms_user_pmb` SET             
                      createdDate               = '".time()."',
                      updatedDate               = '".time()."',
                      createdByUserNum          = '".$createdByUserNum_copy."',
                      updatedByUserNum          = '".$updatedByUserNum_copy."',
                      message_read				='0',
                      email						='".$email_copy."',
                      phone						='".$phone_copy."',
                      message					='".$message_copy."',
                      first_name				='".$first_name_copy."',
                      last_name					='".$last_name_copy."',
                      organisation				='".$organisation_copy."',
                      subject           		='".$subject_copy."',
                      t_num						='".intval($_POST['hid'])."'"
		      )
      or die("MySQL Error Creating Record:<br/>\n". htmlspecialchars(mysql_error()) . "\n");
    }
    else{
    	$arr_sql=mysql_fetch_array(mysql_query($tmp_sql));
    	if(empty($result_2)){
	    	$sql="update cms_user_pmb set message_read=0 where t_num='".intval($_POST['hid'])."'";
				mysql_query($sql);
			}else{
				$sql="update cms_advertiser_pmb set message_read=0 where t_num='".intval($_POST['hid'])."'";
				mysql_query($sql);
			}
			if($arr_sql['email']==$_SESSION['email']){
				$sql="update cms_user_pmb set updatedByUserNum=".$_SESSION['createdByUserNum']." where t_num='".intval($_POST['hid'])."'";
				mysql_query($sql);
			}else{
				$sql="update cms_user_pmb set updatedByUserNum=0 where t_num='".intval($_POST['hid'])."'";
				mysql_query($sql);
			}
    	$sql="insert into `cms_advertiser_pmb`(createdDate,createdByUserNum,email,message,r_num)  values('".time()."','".$_SESSION['createdByUserNum']."','".addslashes($_SESSION['email'])."','".addslashes(nl2br($_POST['ta']).$atta)."','".addslashes(intval($_POST['hid']))."')";
	    mysql_query($sql);
    }
    echo '<script>window.location.href="./message_view.php?pnum='.intval($_POST['hid']).'";</script>';
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
<style>#main #message_content_tb tr td{line-height:20px;padding:20px 10px;}</style>
<script src="./uploadify/jquery.uploadify.js" type="text/javascript"></script>
<script>
function check(){
	if(document.getElementById("attachessizes").value>20*1024*1024){
		alert('附件总容量不能超过20M');
		return false;
	}
	if(document.getElementById("ta").value==''){
		document.getElementById("ta").style.background='#ffffd5';	
		return false;
	}else{
		document.getElementById("ta").style.background='#ffffff';	
	}
	return true;
}
</script>
<link rel="stylesheet" type="text/css" href="./uploadify/uploadify.css">
<style>#info_box dd{margin-left:10px;}</style>
</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="./index.php"><span>Publish Message Board</span></a></h1>
        
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <p style="float:right;color:#9b9b9b;"><?php echo $_SESSION['email'];?></p>
        <div style="clear:both;"></div>
        <ul id="mainNav">
        	<li><a href="./index.php" class="active">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        	<li><a href="./message_send.php">发送信息</a></li>
        	<li><a href="./messager.php">联系人列表</a></li>
        	<li class="logout"><a href="./logout.php">注销</a></li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">

                <div id="main" style="position:relative;">
                	<h3>详细内容</h3>
                	<form action="" class="jNice" method="post" onsubmit="return check()">
                			<fieldset>
                				<p><label>内容:</label><textarea rows="1" cols="1" name="ta" id="ta"></textarea></p>
                				<input type="hidden" name="hid" value="<?php echo intval($_GET['pnum']); ?>">
                				<input type="hidden" name="attaches_pmb" id="attaches_pmb" value="">
							<input type="hidden" name="attachessizes" id="attachessizes" value="0">
                      		<input name="sub" type="submit" value="回复消息" />
                      	</fieldset>
                     <div id="info_box" style="position:absolute;width:200px;top:20px;left:500px;line-height:18px;">
                     	<h3 style="padding:0;">联系人资料</h3>
							<dl>
								<dt>名</dt>
								<dd><?php if(!empty($result_2)){echo $result_2['first_name'];}else{echo $result['first_name'];} ?></dd>
								<dt>姓</dt>
								<dd><?php if(!empty($result_2)){echo $result_2['last_name'];}else{echo $result['last_name'];} ?></dd>
								<dt>电话</dt>
								<dd><?php if(!empty($result_2['phone'])){echo $result_2['phone'];}else if(!empty($result['phone'])){echo $result['phone'];}else{echo 'Not provided';} ?></dd>
								<dt>公司名 (地址)</dt>
								<dd><?php if(!empty($result_2['organisation'])){echo $result_2['organisation'];}else if(!empty($result['organisation'])){echo $result['organisation'];}else{echo 'Not provided';} ?></dd>
							</dl>
						</div>
                      	<div id="queue"></div>
						<input id="file_upload" name="file_upload" type="file" multiple="true">
						<div id="lists" style="padding:10px;background:#fdfcf6;"></div>
						<script type="text/javascript">
							<?php $timestamp = time();?>
							$(function() {
								$('#file_upload').uploadify({
									'formData'     : {
										'token'     : '<?php echo $_SESSION['token'] ?>',
										'pnum':'<?php echo intval($_GET['pnum']); ?>',
										'sessid':'<?php echo session_id();?>'
									},
									'fileSizeLimit' : '5MB',
									'multi'    : false,
									'swf'      : './uploadify/uploadify.swf',
									'uploader' : './uploadify/uploadify_pmb.php',
									'onUploadSuccess' : function(file, data, response) {
					var fname=data;
					var c=parseInt($("#attachessizes").val())+parseInt(file.size);
					fname_pos=fname.lastIndexOf(".");
					fname_2=fname.substring(fname_pos);
					fname=fname.substring(0,fname_pos);
					fname_1=fname.replace(/\s+/,'-');
					fname=fname_1+fname_2;
            $("#lists").html($("#lists").html()+"<div>"+fname+"<span style='margin-left:20px;text-decoration:underline;cursor:pointer;font-size:12px;color:#ff0000;' class='imgatta' title='"+fname+"' >删除</span></div>");
            $("#attaches_pmb").val($("#attaches_pmb").val()+fname+"attach::attach");
            $("#attachessizes").val(c);
        },
        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert(errorString);
        }
								});
		$(".imgatta").live("click",function(){
			$(this).parent().remove();
			$.post("./uploadify/uploadify_pmb_del.php",{sessid:"<?php echo session_id();?>",token:"<?php echo $_SESSION['token']; ?>",pnum:"<?php echo intval($_GET['pnum']); ?>",name:$(this).attr('title')},function(dat){
				var dat = eval("(" + dat + ")");
				document.getElementById("attaches_pmb").value=document.getElementById("attaches_pmb").value.replace(dat.name+"attach::attach",'');
				var c=parseInt($("#attachessizes").val())-parseInt(dat.size);
				$("#attachessizes").val(c);
			})
		})
							});
						</script>
                    	<table cellpadding="0" cellspacing="0" id="message_content_tb">
                    		<tr>
                    			<td width="15%">发送人</td>
							<td width="65%">内容</td>
							<td width="20%">时间</td>
						</tr>
						<?php
			if(!empty($marr)){
				foreach($marr as $v){
			?>
							<tr>
								<td><?php 
									$uemail=$v['email'];
									$uid=$v['createdByUserNum'];
								if($uid!=0){
										$tmp="select first_name,last_name from cms_accounts where num='".$uid."'";
									}else{
										$tmp="select first_name,last_name from cms_accounts where email='".$uemail."'";
									}
									
										$query_tmp=mysql_query($tmp);
										$num_tmp=mysql_num_rows($query_tmp);
										if($num_tmp>0){
											$arr_tmp=mysql_fetch_array($query_tmp);
											echo $arr_tmp['last_name']."&nbsp;".$arr_tmp['first_name'];
										}
									?></td>
								<td><?php 
									$v['message']=preg_replace('/<([^@<>\s]*@[^\.\s]*\.[^<>\s]*)>/','&lt;$1&gt;',$v['message']);
									echo $v['message']; ?></td>
								<td><?php
											echo date("Y-m-d H:i:s",$v['createdDate']);
										?></td>
							</tr>	
						<?php  }  } ?>
		<?php
		if(!empty($result)){
		?>
							<tr>
								<td><?php echo $result['last_name']."&nbsp;".$result['first_name']; ?></td>
								<td><?php 
									$result['message']=preg_replace('/<([^@<>\s]*@[^\.\s]*\.[^<>\s]*)>/','&lt;$1&gt;',$result['message']);
									echo $result['message']; ?></td>
								<td><?php
											echo date("Y-m-d H:i:s",$result['createdDate']);
										?></td>
							</tr>
				<?php
			}
			?>
                        </table>
                    </form>
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
