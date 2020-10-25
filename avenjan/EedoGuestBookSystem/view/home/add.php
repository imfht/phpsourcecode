<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $sitename;?></title>
  <link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
  <script type="text/javascript" src="/src/js/jquery.js" ></script>
</head>
<body>
	<form class="layui-form" id="addmsg">
    <br/>
    <div class="layui-form-item">
      <div class="layui-inline" style="width: 40%">   
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block" >
          <input type="text" name="title" class="layui-input linksTime " autocomplete="off" lay-verify="required|verifytext|cd2t30" >
        </div>
      </div>
      <div class="layui-inline">
        <label class="layui-form-label">反馈类型</label>
        <div class="layui-input-inline">
          <select name="type" class="newsLook" lay-filter="browseLook" lay-verify="required" lay-filter="type">
            <option value="">请选择类型</option>
            <?php 
            foreach(gettypelist() as $value){ 
            echo '<option value="'.$value["id"].'">'.$value["name"].'</option>'; 
            } 
            ?>
            </select>
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-form-item">
        <label class="layui-form-label">反馈信息</label>
        <div class="layui-input-block">
          <textarea placeholder="请输入您要反馈的内容" name="content" class="layui-textarea linksDesc" lay-verify="required" id="content"></textarea>
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-inline" >   
        <label class="layui-form-label">姓名</label>
        <div class="layui-input-block" >
          <input type="text" name="name" class="layui-input linksTime " lay-verify="required|verifytext|cd2t10" autocomplete="off"  >
        </div>
      </div>
      <div class="layui-inline" >   
        <label class="layui-form-label">联系电话</label>
        <div class="layui-input-block" >
          <input type="text" name="phone" class="layui-input linksTime "  autocomplete="off" lay-verify="required|phone|number">
        </div>
      </div>
     </div> 
    <div class="layui-form-item">
      <div class="layui-inline">
      <label class="layui-form-label">邮件回复</label>
          <div class="layui-input-block">
            <input type="checkbox" name="closed" class="homePage" lay-filter="closed"  title="开启"  >
          </div>
       </div>
       <div class="layui-inline" id="email" style="display: none;">    
        <label class="layui-form-label">邮件地址</label>
        <div class="layui-input-inline">
          <input type="text" name="email" id="emailinput" class="layui-input linksTime" autocomplete="off">
        </div>
      </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="position: absolute;">人工验证：</label>
        <div class="layui-input-block" >
         <div class="layui-form-item form_code" id="verify"></div>
        </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-input-block">
        <a class="layui-btn" lay-submit="" lay-filter="addmsg" id="btn">提交留言</a>
        <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
        </div>
    </div>
    <blockquote class="layui-elem-quote">留言须知：<br/>
      1、严禁对个人、实体、民族、国家等进行漫骂、污蔑与诽谤<br/>

      2、网友应遵守我国互联网的相关法规<br/>

      3、网友应对所发布的信息承担全部责任<br/>

      4、网站管理人员有权保留或删除留言中的信息内容<br/>

      5、发表留言即表明已阅读并接受以上条款
    </blockquote>
  </form>
<script type="text/javascript" src="/src/layui/layui.js"></script>	
<script type="text/javascript" src="/src/js/add.js"></script> 
<script type="text/javascript" src="/src/js/verify.js" ></script> 
</body>
</html>