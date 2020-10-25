<?php
/*
    iEdon Mini Portal System
*/
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title><?php echo constant("PORTAL_NAME");?> Authentication</title>
	<link rel="stylesheet" href="css/pintuer.css">
	<style type="text/css">
		<!--
			*{ margin:0px; padding:0px;}
			.error-container{ background:#fff; border:1px solid #0ae;  text-align:center; width:300px; margin:50px auto; font-family:Microsoft Yahei; padding-bottom:30px; border-top-left-radius:5px; border-top-right-radius:5px;  }
			.error-container h1{ font-size:16px; padding:12px 0; background:#0ae; color:#fff;} 
			.errorcon{ padding:35px 0; text-align:center; color:#0ae; font-size:18px;}
			.errorcon i{ display:block; margin:12px auto; font-size:30px; }
			.errorcon span{color:red;}
			h4{ font-size:14px; color:#666;}
			a{color:#0ae;}
		-->
	</style>
</head>
<body class="no-skin">
<?php
if(!isset($_GET['nas']) || !isset($_GET['ip']) || !isset($_GET['mac']) || !isset($_POST['usr']) || !isset($_POST['pwd']))
{
	echo '<br /><h1 style="padding-left:20px">Unknown status, please try it again later.</h1></body>';
	exit;
}
?>
<div class="error-container"> 
    <h1><?php echo constant("PORTAL_NAME");?> Authentication</h1>   
    <div class="errorcon">
	<?php
		function verify_user() {
			$success = false;
			$conn = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE_NAME);
			if ($conn->connect_errno) {
				echo '<i class="icon-smile-o"></i>Unable to connect to database.<br />' . $conn->connect_error. '</div><h4 class="smaller"><a id="href" href="javascript:self.location=document.referrer;">Click here to go back</a></h4></div></body></html>';
				exit;
			}
			$stmt = $conn->prepare('SELECT * FROM accounts WHERE id = ? AND password = ?;');
			$stmt->bind_param('ss', $_POST['usr'], $_POST['pwd']);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$success = true;
			}
			$conn->close();
			return $success;
		}
		if(verify_user())
		{
				$url = constant($_GET['nas'] . '_URL') . '?act=logged';
				$post_data = array ("ip" => $_GET['ip'],"mac" => $_GET['mac']);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				$output = curl_exec($ch);
				curl_close($ch);
				if($output != "OK")
				{
					echo '<i class="icon-smile-o"></i>Error: Exception on callback';
				} else {
					if(isset($_GET['redirect']))
					{
						echo '<i class="icon-smile-o"></i>ACCESS GRANTED</div><h4 class="smaller"><a id="href" href="' . $_GET['redirect'] . '">Surf the Internet!</a></h4></div></body></html>';
						exit;
					} else {
						header("Location: " . DEFAULT_REDIRECT_URL);
					}
				}
		} else {
			echo '<i class="icon-smile-o"></i>ACCESS DENIED';
		}
	?>
   </div>
    <h4 class="smaller"><a id="href" href="javascript:self.location=document.referrer;">Click here to go back</a></h4>
</div>
</body>
</html>
