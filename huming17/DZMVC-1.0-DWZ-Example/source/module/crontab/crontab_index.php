<?php
/**
 * @author HumingXu E-mail:huming17@126.com
 */
@set_time_limit(0);
@set_magic_quotes_runtime(0);
ini_set('memory_limit', '128M');

if(empty($mod)){
	require_once '../../../dz_framework/init.php';
	require SITE_ROOT . 'config/config_global.php';
	$do=isset($_REQUEST['do']) ? $_REQUEST['do']:'index';
}
switch($do){
	case "mysql_backup":
		list($dbhost, $dbport) = explode(':', $dbhost);

		$query = DB::query("SHOW VARIABLES LIKE 'basedir'");
		list(, $mysql_base) = DB::fetch($query, MYSQL_NUM);

		$defaultfilename = date('Y_m_d_H_i_s', TIMESTAMP);
		$dumpfile_dir = SITE_ROOT.'data/mysql_backup/';
		clearstatcache();
		if(!is_dir($dumpfile_dir)){
			dmkdir($dumpfile_dir);
		}
		$dumpfile = $dumpfile_dir.$defaultfilename.'.sql';
		@unlink($dumpfile);

		$db = & DB::object();
		$tabletype = $db->version() > '4.1' ? 'Engine' : 'Type';
		$tablepre = $_G['config']['db'][1]['tablepre'];
		$dbcharset = $_G['config']['db'][1]['dbcharset'];
		$get_type='xuhm_mysqlbak';
		$get_method='shell';
		$volume = 1;
		$idstring = '# Identify: '.base64_encode("TIMESTAMP,".$_G['setting']['version'].",{$get_type},{$get_method},{$volume},{$tablepre},{$dbcharset}")."\n";
		$dumpcharset = $_GET['sqlcharset'] ? $_GET['sqlcharset'] : str_replace('-', '', $_G['charset']);
		$setnames = ($_GET['sqlcharset'] && $db->version() > '4.1' && (!$_GET['sqlcompat'] || $_GET['sqlcompat'] == 'MYSQL41')) ? "SET NAMES '$dumpcharset';\n\n" : '';

		$dbhost = $_config["db"]['1']["dbhost"];
		$dbuser = $_config["db"]['1']["dbuser"];
		$dbpw = $_config["db"]['1']["dbpw"];
		$dbname = $_config["db"]['1']["dbname"];
		$dbcharset = $_config["db"]['1']["dbcharset"];

		$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'/bin/';

		@shell_exec($mysqlbin.'mysqldump --force --quick  --add-drop-table --host="'.$dbhost.($dbport ? (is_numeric($dbport) ? ' --port='.$dbport : ' --socket="'.$dbport.'"') : '').'" --user="'.$dbuser.'" --password="'.$dbpw.'" "'.$dbname.'" > '.$dumpfile);
		if(@is_writeable($dumpfile)) {
		    $fp = fopen($dumpfile, 'rb+');
		    @fwrite($fp, $idstring."# <?php exit();?>\n ".$setnames."\n #");
		    fclose($fp);
		}

		$subject = "MYSQL BACKUP";
		$message = 'http://'.$_SERVER['HTTP_HOST']."/data/mysql_backup/".$defaultfilename.'.sql';
		$to1 = '';
		$to2 = '';
		$to3 = '13962362615@139.com';
		$to4 = '28315279@qq.com';
		$from = "huming235@163.com";

		require SITE_ROOT.'source/lib/phpmail/class.phpmailer.php';
		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->IsSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug  = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host       = "smtp.163.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port       = 25;
		//Whether to use SMTP authentication
		$mail->SMTPAuth   = true;
		//Username to use for SMTP authentication
		$mail->Username   = "huming235";
		//Password to use for SMTP authentication
		$mail->Password   = "humingpass";
		//Set who the message is to be sent from
		$mail->SetFrom($from, 'HumingXu');
		//Set an alternative reply-to address
		$mail->AddReplyTo($from,'HumingXu');
		//Set who the message is to be sent to
		//$mail->AddAddress($to1, 'sun');
		//$mail->AddAddress($to2, 'anwendai');
		$mail->AddAddress($to3, 'yidong');
		$mail->AddAddress($to4, 'xuhm');
		//$mail->AddCC($from);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
		$mail->MsgHTML($message, dirname(__FILE__));
		//Replace the plain text body with one created manually
		$mail->AltBody = $message;
		//Attach an image file
		$mail->AddAttachment($dumpfile);
		//$mail->AddAttachment('data/'.$defaultfilename.'_sns.sql');

		//Send the message, check for errors
		if(!$mail->Send()) {
		  //echo "Mailer Error: " . $mail->ErrorInfo;
		  echo '2';
		} else {
		  echo '1';
		  //echo "Message sent!";
		}
	break;

	case "site_effective":
		require SITE_ROOT.'source/lib/simple_html_dom/simple_html_dom.php';
		$time1 = get_total_millisecond();
		$html = file_get_html('http://58.222.157.2:8115/index.php?m=member&c=index&a=login');
		$ret = $html->find('title', 0)->innertext;
		$time2 = get_total_millisecond();
		if(strpos($ret,'-')){
			$subject = "泰兴报表系统登陆页面自动化响应检查";
			$response_time = $time2 - $time1;
			$message = date('Y-m-d H:i:s').'测试,报表系统登陆页面正常,响应时间为:'.$response_time.'ms';
			$to1 = '';
			$to2 = '';
			$to3 = '13962362615@139.com';
			$to4 = 'xuhm@lemote.com';
			$from = "huming235@163.com";

			require SITE_ROOT.'source/lib/phpmail/class.phpmailer.php';
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->IsSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug  = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host       = "smtp.163.com";
			//Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port       = 25;
			//Whether to use SMTP authentication
			$mail->SMTPAuth   = true;
			//Username to use for SMTP authentication
			$mail->Username   = "huming235";
			//Password to use for SMTP authentication
			$mail->Password   = "humingpass";
			//Set who the message is to be sent from
			$mail->SetFrom($from, 'HumingXu');
			//Set an alternative reply-to address
			$mail->AddReplyTo($from,'HumingXu');
			//Set who the message is to be sent to
			//$mail->AddAddress($to1, 'sun');
			//$mail->AddAddress($to2, 'anwendai');
			$mail->AddAddress($to3, 'yidong');
			$mail->AddAddress($to4, 'xuhm');
			//$mail->AddCC($from);
			//Set the subject line
			$mail->Subject = $subject;
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			$mail->MsgHTML($message, dirname(__FILE__));
			//Replace the plain text body with one created manually
			$mail->AltBody = $message;
			//Attach an image file
			$mail->AddAttachment('/tmp/cron_temp.html');

			//Send the message, check for errors
			if(!$mail->Send()) {
			  //echo "Mailer Error: " . $mail->ErrorInfo;
			  echo 2;
			} else {
			  echo 1;
			  //echo "Message sent!";
			}
		}else{
			echo 3;
		}
	break;

}