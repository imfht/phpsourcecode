<?php
session_start();
include_once ('../config.php');
$sid=$_POST['sid'];
$passkey=$_POST['passkey'];
$serverip=$_POST['serverip'];
$con=mysql_connect($mysql_address,$mysql_user,$mysql_password);
if (!$con){
	echo "error1";//服务器解析错误代码:数据库连接失败
	exit;
}else{
	mysql_select_db($mysql_dbname,$con);
	$query="SELECT * FROM admin_server WHERE sid=".$sid;
	mysql_query("SET NAMES utf8");
	$result=mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($sid == $row['sid']){
		$passkeymd5=md5($passkey);
		if ($passkeymd5 == $row['passkey']){
			//总验证过程完毕 开始分类处理
			if ($row['place']==0){
				//综合服务器
				//POST参数 sid=服务器SID passkey=服务器密钥 serverip=服务器IP time=任务次数
				$runtime0=$_POST['time'];
				if(empty($runtime0)){
					echo "posttime1";//服务器解析错误代码:循环错误
					mysql_close($con);
					exit;
				}
				if ($runtime0==1){
					//服务器脚本小循环第一次:发送队列ID进行记录
					//POST参数 无
					//发送参数 id=队列ID
					$query11="select * from queue where type=0";
					$result11=mysql_query($query11);
					$row11=mysql_fetch_array($result11);
					echo $row11['id'];
					mysql_close($con);
					exit;
				}elseif($runtime0==2){
					//服务器脚本小循环第二次:接受视频ID 发送URL
					//POST参数 id=队列ID
					//发送参数:url=视频地址
					$getid=$_POST['id'];
					$query12="UPDATE `queue` SET  `type` =  '1' WHERE  `queue`.`id` =".$getid;
					mysql_query($query12);
					$query12="SELECT * FROM queue WHERE id=".$getid;
					$result12=mysql_query($query12);
					$row12=mysql_fetch_array($result12);
					echo $row12['URL'];
					mysql_close($con);
					exit;
				}elseif($runtime0==3){
					//服务器脚本小循环第三次:发送视频信息(多次)
					//POST参数 如下
					$do=$_POST['do'];//是否完毕 1=完毕 0=继续接收
					$getid=$_POST['vid'];//队列ID
					//发送参数 无
					if ($do==1){
						$query131="UPDATE `queue` SET `type`='3' WHERE `queue`.`id` =".$getid;
						mysql_query($query131);
						mysql_close($con);
						echo "Query Successful Done!";
						exit;
					}elseif($do==0){
						$code=$_POST['code'];//Youtube格式识别符
						$ext=$_POST['ext'];//视频格式
						$res=$_POST['res'];//视频分辨率
						$note=$_POST['note'];//描述信息
						$query132="INSERT INTO `video_info` (`vid`, `code`, `ext`, `res`, `note`, `link`, `id`) VALUES ('".$getid."', '".$code."', '".$ext."', '".$res."', '".$note."', 'Waiting', NULL);";
						mysql_query($query132);
						echo "GetID=",$getid,"|Code=",$code,"|Ext=",$ext,"|Res=",$res,"|Note=",$note;
					}
				}elseif($runtime0==4){
					//服务器脚本小循环第四次:下载
					//POST参数 time=执行次数
					$time=$_POST['do'];
					if ($time==1){
						$query141='SELECT * FROM video_info WHERE link="Waiting"';
						$result141=mysql_query($query141);
						$row141=mysql_fetch_array($result141);
						echo $row141['id'];
					}elseif($time==2){
						$infoid=$_POST['id'];
						$query142='SELECT * FROM video_info WHERE id="'.$infoid.'"';
						$result142=mysql_query($query142);
						$row142=mysql_fetch_array($result142);
						echo $row142['code'];
					}elseif($time==3){
						$infoid=$_POST['id'];
						$query1431='SELECT * FROM video_info WHERE id="'.$infoid.'"';
						$result1431=mysql_query($query1431);
						$row1431=mysql_fetch_array($result1431);
						$query1432='SELECT * FROM queue WHERE id="'.$row1431['vid'].'"';
						$result1432=mysql_query($query1432);
						$row1432=mysql_fetch_array($result1432);
						echo $row1432['URL'];
					}elseif($time==4){
						$infoid=$_POST['id'];
						$query144='SELECT * FROM video_info WHERE id="'.$infoid.'"';
						$result144=mysql_query($query144);
						$row144=mysql_fetch_array($result144);
						echo $row144['ext'];
					}elseif($time==5){
						$infoid=$_POST['id'];
						$filename=$_POST['name'];
						$url="http://".$serverip."/video/".$filename;
						$query145="UPDATE `video_info` SET  `link` =  '".$url."' WHERE `id` =".$infoid.";";
						mysql_query($query145);
						echo "Done! The FileName=".$filename." The URL=".$url;
					}
				}
			}elseif($row['place']==1){
				//解析服务器
			}elseif($row['place']==2){
				//转码服务器
			}elseif($row['place']==3){
				//下载服务器
			}else{
				echo "data1";//服务器解析错误代码:数据库错误
			}
		}else{
			echo $passkeymd5;
			echo "error3";//服务器解析错误代码:服务器密钥配置错误
			exit;
		}
	}else{
		echo "error2";//服务器解析错误代码:服务器SID配置错误
		mysql_close($con);
		exit;
	}
}
?>