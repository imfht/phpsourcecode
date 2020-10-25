<?php  
/** 
 * php - websocket 
 * http://www.linuxidc.com/Linux/2012-02/54013p2.htm
 * http://blog.csdn.net/trace332/article/details/6325986
 */  

//error_reporting(E_ALL);  
set_time_limit(0);  
ob_implicit_flush(true);  

date_default_timezone_set("Asia/shanghai");  
$sockets = array();  
$users   = array();  

$master  = WebSocket('121.40.76.248',81);  
$sockets[] = $master;  

$token = ''; //用于登录验证
set_token();

while(true){  
	$changed = $sockets;  
	@socket_select($changed,$write=NULL,$except=NULL,NULL);  
	foreach ($changed as $socket) {  
		if ($socket == $master) {  
			$client=socket_accept($master);  
			if ($client !== false) {  
				skConnect($client);  
			}  
		} else {  
			$data = @socket_recv($socket,$buf,2048,0);  

			if ($data != 0) {  
				$user = getuserbysocket($socket);  

				if (!$user->handshake) {  
					dohandshake($user,$buf);  
				} else {  
					$buf = unwrap($buf);
					if (0 == $user->login) {
						$buf = json_decode($buf);
						if ($token == @$buf->token) {
							$user->login = 1; 
							$user->u_id = doubleval($buf->u_id); 
							$user->u_name = filter_var($buf->u_name, FILTER_SANITIZE_STRING); 
							set_token();
						} else $user->login = -1; 
					} else if (1 == $user->login) {
						$buf = filter_var($buf, FILTER_SANITIZE_STRING);
						send_all($user->u_id.'.htm">['.$user->u_name.']</a> '.$buf); 
					}
				}  
			} else disconnect($socket);
		}  
	}  

	sleep(1);  
}  

function send_all($buf) {  
	global $users;  
	foreach($users as $user) {  
		process($user->socket,$buf);  
	}  
}  

function set_token() {  
	global $users;  
	global $token;  
	$token = md5(rand());
	file_put_contents('token.php', '<?php echo "var count = '.count($users).';"; if (!empty($_SESSION["auth"])) echo "var user = ".json_encode($_SESSION["auth"]).", token = '."'".$token."'".';";'); 
}

//---------------------------------------------------------------   

function WebSocket($address,$port) {  
	$master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");  
	socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");  
	socket_bind($master, $address, $port)                    or die("socket_bind() failed");  
	socket_listen($master,20)                                or die("socket_listen() failed");  
	echo "Server Started : ".date('Y-m-d H:i:s')."\n";  
	echo "Master socket  : ".$master."\n";  
	echo "Listening on   : ".$address." port ".$port."\n\n";  
	return $master;  
}  

function getuserbysocket($socket){  
	global $users;  
	$found=null;  
	foreach($users as $user){  
		if($user->socket==$socket){ $found=$user; break; }  
	}  
	return $found;  
}  

function skConnect($socket){  
	global $sockets,$users;  

	$user = new User();  

	$user->id = uniqid();  
	$user->socket = $socket;  

	$users[] = $user;  
	$sockets[] = $socket;  
}  

function disconnect($socket){  
	global $sockets,$users;  
	$found=null;  
	$n=count($users);  
	for($i=0;$i<$n;$i++){  
		if($users[$i]->socket==$socket){ $found=$i; break; }  
	}  
	if(!is_null($found)){ array_splice($users,$found,1); }  
		$index = array_search($socket,$sockets);  
	socket_close($socket);  
	if($index>=0){ array_splice($sockets,$index,1); }  
}  

function getheaders($req){  
	$r=$h=$o=null;  
	if(preg_match("/GET (.*) HTTP\/1\.1\r\n/"   ,$req,$match)){ $r=$match[1]; }  
	if(preg_match("/Host: (.*)\r\n/"  ,$req,$match)){ $h=$match[1]; }  
		if(preg_match("/Sec-WebSocket-Origin: (.*)\r\n/",$req,$match)){ $o=$match[1]; }  
			if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/",$req,$match)){ $key=$match[1]; }  
				return array($r,$h,$o,$key);  
}  

function dohandshake($user,$buf){  
	list($resource,$host,$origin,$strkey) = getheaders($buf);  
	$strkey .= "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";  
	$hash_data = base64_encode(sha1($strkey,true));  

	$upgrade  = "HTTP/1.1 101 Switching Protocols\r\n" .  
		"Upgrade: websocket\r\n" .  
		"Connection: Upgrade\r\n" .  
		"Sec-WebSocket-Accept: " . $hash_data . "\r\n" .  
		//"Sec-WebSocket-Protocol: websocket\r\n" .  
		"\r\n";  

	socket_write($user->socket,$upgrade,strlen($upgrade));  
	$user->handshake=true;  
	return true;  
}  

function process($socket,$msg){  //自定义处理程序
	//$action = unwrap($msg);  
	//say("< ".$action);  
	send($socket, $msg);  
}  

function send($client,$msg){  
	//say("> ".$msg);  
	$msg = wrap($msg);  
	@socket_write($client,$msg,strlen($msg));  
	return true;  
}  

function ord_hex($data)  
{  
	$msg = "";  
	$l = strlen($data);  

	for ($i= 0; $i< $l; $i++) {  
		$msg .= dechex(ord($data{$i}));  
	}  

	return $msg;  
}  

function wrap($msg="") {  
	$frame = array();  
	$frame[0] = "81";  
	//$msg .= " is ok!";  
	$len = strlen($msg);  
	$frame[1] = $len<16?"0".dechex($len):dechex($len);  
	$frame[2] = ord_hex($msg);  
	$data = implode("",$frame);  
	return pack("H*", $data);  
}  

function unwrap($msg="") {  
	$mask = array();  
	$data = "";  
	$msg = unpack("H*",$msg);  

	$head = substr($msg[1],0,2);  

	if (hexdec($head{1}) === 8) {  
		$data = false;  
	} else if (hexdec($head{1}) === 1) {  
		$mask[] = hexdec(substr($msg[1],4,2));  
		$mask[] = hexdec(substr($msg[1],6,2));  
		$mask[] = hexdec(substr($msg[1],8,2));  
		$mask[] = hexdec(substr($msg[1],10,2));  

		$s = 12;  
		$e = strlen($msg[1])-2;  
		$n = 0;  
		for ($i= $s; $i<= $e; $i+= 2) {  
			$data .= chr($mask[$n%4]^hexdec(substr($msg[1],$i,2)));  
			$n++;  
		}  
	}  

	return $data;  
}  

function say($msg=""){ print($msg."\n"); }  

class User{  
	var $id;  
	var $socket;  
	var $handshake;  
	var $login = 0;  
	var $u_id;
	var $u_name;
}  
