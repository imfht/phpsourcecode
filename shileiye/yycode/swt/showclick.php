<?php header("Content-type: text/html; charset=utf-8");
require 'config.php';
@$urltype=$_SERVER['QUERY_STRING'];			//获取显示类型
if(empty($urltype))exit();
$canshu=explode('|',$urltype);
@$b=safe_string($canshu[0]);
@$fbl=safe_string($canshu[1]);
@$xy=safe_string($canshu[2]);
@$url=urldecode(safe_string($canshu[3]));
if(empty($url) || empty($xy) || empty($fbl) || empty($b) || strlen($url)>100 ||strlen($xy)>15 ||strlen($fbl)>15 ||strlen($b)>15){
	echo "有参数错误，请检查（浏览器：".$b."）（分辨率：".$fbl."）（坐标：".$xy."）（URL：".$url."）";
	exit();
}
$fbl=explode(",",$fbl);
if(!preg_match("/\.(html|htm|asp|php|jsp|aspx)$/",$url) && !preg_match("/\/$/",$url))$url.="/";
$snoopy = new Snoopy;
$snoopy->fetch($url);
//判断是否站内连接
if(!preg_match("/^(http:\/\/".$_SERVER['HTTP_HOST'].")/",$url)){
	$gethtml=formaturl($snoopy->results, $url);		//非站内连接补全URL
}else{
	$gethtml=$snoopy->results;
}
//echo $gethtml;
?>
<title><?php if(my_get_browser()!=$b){echo "（提示：请使用".$b."浏览器查看，以保证数据的大致精确!）";};?>点击元素标记页面PC测试版</title>
<?php echo $gethtml; ?>
<script>
document.body.style.width=<?php echo $fbl[0]; ?>;
document.body.style.height=<?php echo $fbl[1]; ?>;
setTimeout("click_show(<?php echo $xy;?>)",1500);
//查看点击位置方法，可扩展查找CSS、ID等
function click_show(x,y){
	//var a=[541,138];
	//var x=a[0],y=a[1];
	var b=document.createElement('div');
	var bs=b.style;
	b.id="clickshow";
	bs.width=document.body.scrollWidth-1+"px";
	bs.height=document.body.scrollHeight+"px";
	bs.borderTop="0";
	bs.borderLeft="0";
	bs.position="absolute";
	bs.top="0";
	bs.left="0";
	bs.zIndex="2147483647";
	document.body.appendChild(b);
	var d=document.createElement('div');
	var ds=d.style;
	ds.width=x+"px";
	ds.height=y+"px";
	ds.border="1px solid red";
	ds.borderTop="0";
	ds.borderLeft="0";
	ds.position="absolute";
	ds.top="0";
	ds.left="0";
	ds.zIndex="2147483647";
	ds.background="url('<?php echo $info["swtdir"];?>/img/clickthist.gif') no-repeat bottom right"; 
	document.getElementById("clickshow").appendChild(d);
	var t=document.createElement('div');
	var ts=t.style;
	ts.width=document.body.scrollWidth-x-1+"px";
	ts.height=document.body.scrollHeight-y+"px";
	ts.border="1px solid red";
	ts.borderRight="0";
	ts.borderBottom="0";
	ts.position="absolute";
	ts.top=y+"px";
	ts.left=x+"px";
	ts.zIndex="2147483647";
	ts.background="url('<?php echo $info["swtdir"];?>/img/clickthis.gif') no-repeat top left"; 
	document.getElementById("clickshow").appendChild(t);
	//document.body.appendChild(t);
	window.scrollTo(0,y-100);
}
</script>