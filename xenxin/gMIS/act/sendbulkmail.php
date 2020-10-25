<?php

require("../comm/header.inc.php");

include("../comm/tblconf.php");

$act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$tbl = $_REQUEST['tbl'];
$mydb = $_CONFIG['appname'].'db';
$db = $_REQUEST['db']==''?$mydb:$_REQUEST['db'];
$field = $_REQUEST['field'];

$out = "";

$emailsubject = "新品上架";
$emailfrom = "lingshang";

if($act == 'sendbulkmail'){
    
    $out .= "<fieldset style=\"border-color:#5f8ac5;border: 1px solid #5f8ac5\">
			<legend><h4>群发邮件</h4></legend>
			<form id=\"subscriber\" name=\"subscriber\" action=\"".$jdo."&act=sendbulkmail-do&isheader=0&tbl="
			        .$tbl."\" method=\"post\">
			<table align=\"center\" width=\"95%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0px\">";
    $out .= "<tr>输入发送内容</tr><tr><td><textarea style=\"overflow:visible\" name=\"mailcontent\" rows=\"10\" cols=\"100\"></textarea></td></tr>";

    $out .= "<tr>
			<td colspan=\"".$form_cols."\" align=\"center\">
				<input type=\"submit\" name=\"sendbtn\" id=\"sendbtn\" value=\"发送\" onclick=\"javascript:doActionEx(this.form.name, 'contentarea');\"/>
				<input type=\"button\" name=\"cancelbtn\" id=\"cancelbtn\" value=\"关闭\" onclick=\"javascript:parent.switchArea('contentarea_outer','off');\" />
			</td>
		</tr>";
		
$out .= "</table> 
		 </form>
		 </fieldset>
		 <br/>";

}
else if($act == 'sendbulkmail-do'){

	#print_r($tbl);
    $hmorig = $gtbl->getBy("email", "state=1");
	if($_REQUEST['mailcontent'] != ""){
		$emailcontent = $_REQUEST['mailcontent'];
		if($hmorig[0]){
			$hmorig = $hmorig[1];
			foreach($hmorig as $k=>$v){
				$emailto = $v['email'];
				sendMail($emailto, $emailsubject, $emailcontent, $emailfrom);
			}
		}else{
			error_log(__FILE__.": can not read email from database.");
		}
	}
	
   $out .= "what you want to send is:<br/>[".$_REQUEST['mailcontent']."]";
   

}

require("../comm/footer.inc.php");

print $out ;


?>
