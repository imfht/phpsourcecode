<?php
class createMap
{
    public function getTmpl()
    {
        $tmpl = file_get_contents('map_template.html');
        return $tmpl;
    }
    public function uploadImg($files)
    {
        $ext = '';
        $size = '';
        $type = $files['type'];
        $size = getimagesize($files['tmp_name']);
        //if($size[0] != 32 || $size[1] != 32) {
        //    return array('code' => 0, 'message' => '您的图片尺寸是：' . $size[0] . '*' . $size[1] . ', 标准应为：32*32.');
        //}
        switch($type) {
            case 'image/png':
                $ext = '.png';
                break;
            case 'image/jpeg':
                $ext = '.jpg';
                break;
            case 'image/gif':
                $ext = '.gif';
        }
        if(empty($ext)) {
            return array('code' => 0, 'message' => '上传文件格式不对。');
        } else {
            $upfile = 'upload/'. time() . $ext;
            if(move_uploaded_file($files['tmp_name'], $upfile)) {
                return array('code' => 1, 'message' => $upfile);
            }
        }
    }
}
$mapurl = 'map.html';
if(isset($_POST) && !empty($_POST)) {
    extract($_POST);
    $createmap = new createMap;
    $upfileinfo = $createmap->uploadImg($_FILES['icon']);
    if(!$upfileinfo['code']) {
        echo '<div class="content">'.$upfileinfo['message'].'</div>';
    } else {
        $contents = strtr($createmap->getTmpl(), array('{$key}' => $key, '{$tableID}' => $tableid, '{$icon}' => '../'.$upfileinfo['message']));
        $filename = time() . '.html';
        file_put_contents('html/' . $filename, $contents);
        $mapurl = 'http://zhaoziang.com/amap/maptool/html/' . $filename;
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>公众号地图工具</title>
<link rel="shortcut icon" href="http://www.zhaoziang.com/favicon.ico" />
<link rel="stylesheet" href="static/base.css" type="text/css" />
<meta name="description" content="公众号一键生成地图工具。为微博、微信、支付宝公众号，提供免费的开源的全国实体店分布地图。" />
<meta name="keywords" content="高德地图,公众号地图工具," />
</head>
<body>
<div class="header clearfix">
	<img src="static/header.png" />
</div>
<div class="container clearfix">
	<div class="sider">
		<form id="map_form" action="index.php" enctype="multipart/form-data">
			<h2>第一步 输入您的key<a href="detail.html#q1" target="_blank"><img class="question" alt="如何获得key" title="如何获得key" src="static/q.png" /></a></h2>
			<p><input class="input_text" name="key" id="key" type="text" value="" /></p>
			<p>&nbsp;</p>
			<h2>第二步 输入您的tableID<a href="detail.html#q2" target="_blank"><img class="question" alt="如何获得tableID" title="如何获得tableID" src="static/q.png" /></a></h2>
			<p><input class="input_text" name="tableid" id="tableid" type="text" value="" /></p>
			<p>&nbsp;</p>
			<h2>第三步 上传您的个性化图标</h2>
			<p><input class="input_file" name="icon" id="icon" type="file" />（请上传64*64px的图片）</p>
			<p>&nbsp;</p>
			<h2>第四步 生成地图链接</h2>
			<p><input type="button" value="生成" id="submit_btn" /></p>
			<p>&nbsp;</p>
			<h2>第五步 放入公众服务中<a href="detail.html#q3" target="_blank"><img class="question" alt="如何将地图链接放入公众服务中" title="如何将地图链接放入公众服务中" src="static/q.png" /></a></h2>
			<p><input class="input_text" type="text" value="<?php echo $mapurl; ?>" /></p>
            <p><a href="index.php">重填信息</a></p>
		</form>
	</div>
	<div class="wider">
		<ul class="menu clearfix">
			<li><a onclick="clk_map();" href="javascript:void(0);">地图</a></li>		
			<li><a onclick="clk_code();" href="javascript:void(0);">源代码</a></li>		
		</ul>		
		<iframe id="mapView" name="mapView" width='560' height='440' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='<?php echo $mapurl; ?>'></iframe>
		<textarea id="mapCode"></textarea>
	</div>
</div>
<div class="footer clearfix">
	<h3>成功案例<a href="detail.html#q4" target="_blank"><img class="question" alt="如何出现在这里" title="如何出现在这里" src="static/q.png" /></a></h3>
	<div class="flinks clearfix">
		<a href="http://www.amfaqua.com/pinpailingshoudian/pinpailingshoudian_map.html" target="_blank"><img src="static/amf.png" /><span>AMF海水农场</span></a>
		<a href="http://www.51park.com.cn/" target="_blank"><img src="static/wuyou.png" /><span>无忧停车</span></a>
		<a href="http://changba.com/yunying/ktvStaticList.php" target="_blank"><img src="static/changba.png" /><span>唱吧</span></a>
	</div>
</div>
</body>
<script type="text/javascript">
(function() {
	var $btn = document.getElementById('submit_btn');
	$btn.onclick = function() {
		var $key = document.getElementById('key');
		var $tableid = document.getElementById('tableid');
		var $icon = document.getElementById('icon');
		
		if(!$key.value) {alert('key 不能为空！'); return;}
		if(!$tableid.value) {alert('tableID 不能为空！'); return;}
		if(!$icon.value) {alert('请选择上传图片！'); return;}
        document.getElementById('map_form').method = 'post';
		document.getElementById('map_form').submit();
		
		getresource();
	};
})();
function clk_map(){
	document.getElementById('mapCode').style.display = "none";
	document.getElementById('mapView').style.display = "block";
}
function clk_code(){
	//var a = frames["mapView"].document.documentElement.outerHTML;
	//document.getElementById("mapCode").value = a;
	getresource();
	document.getElementById('mapCode').style.display = "block";
	document.getElementById('mapView').style.display = "none";
}
function getresource(){
    function createXmlHttpRequest(){
        try {
            return new XMLHttpRequest();
        }
        catch(e){
            return new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    var mylink = frames['mapView'].document.URL;
    var xmlHttp = createXmlHttpRequest();
    xmlHttp.open("get",mylink,false);
    xmlHttp.send();
    if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
        str = xmlHttp.responseText;//str即为返回的html内容
		document.getElementById("mapCode").value = str;
    }	
}
</script>
</html>