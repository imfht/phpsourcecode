<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
$msginfo = $database->select("book", "*", ["id[=]" =>$_GET['sid']]);
extract($msginfo[0]); 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>编辑留言</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="message.css" media="all" />
  <link rel="stylesheet" href="/plugins/font-awesome/css/font-awesome.min.css" media="all" />
</head>
<body class="childrenBody">
  <div class="layui-tab">
<?php
$TAB = <<< EOT
  <ul class="layui-tab-title">
    <li class="layui-this">留言编辑</li>
    <li>查看回复</li>
  </ul>
EOT;
$rename = <<< EOT
  <label class="layui-form-label" style="padding:9px 0px; width:auto; text-align:left;font-size: 16px">姓名：</label>
  <div class="layui-input-inline" >
    <input type="text" name="rename" class="layui-input linksTime " autocomplete="off" lay-verify="required|verifytext|cd2t10" >
  </div>
EOT;
  if(!empty($_GET['index']) && $_GET['index'] =="yes"){
    $showtab=1;
  }else{
    session();//权限控制
    $showtab=0;
    echo $TAB;
  }
?>
  <div class="layui-tab-content">
    <?php if($showtab==0){
      include "inc.php";
    } ?>
    <div class="layui-tab-item <?php echo $showtab==0 ? "" : "layui-show" ?>">
    	<!--回复区域-->
      <div class="layui-fluid layadmin-message-fluid">
          <div class="layui-row">
              <div class="layui-col-md12">
                  <form class="layui-form" id="messageform">
                      <div class="layui-form-item layui-form-text">
                          <div class="layui-input-block" style="margin-bottom: 25px; background: #fff">
                              <textarea name="recontent" placeholder="请输入内容" class="layui-textarea" lay-verify="required" id="replay"></textarea>
                          </div>
                          <div class="layadmin-messag-icon">
                            <?php echo $showtab==0 ? "" : $rename; ?>
                          </div>
                        <div class="layui-inline layui-input-right" > 
                         <a class="layui-btn" lay-submit="" lay-submit="" lay-filter="message" id="send">发表</a>
                       </div>
                      </div>
                  </form>
              </div>
              <div class="layui-col-md12  message-content" id="flow-manual"> </div>
            </div>
          </div>
      </div>
  </div>
</div>
<script type="text/javascript" src="/src/layui/layui.js"></script>
<script type="text/javascript" src="edit.js"></script>
</body>
</html>