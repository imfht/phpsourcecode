<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  
  <head>
    <meta charset="UTF-8">
    <title><?php echo (C("app_name")); echo (C("app_copy")); ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="/web/CourseSEL/Public/layui/css/layui.css" media="all">
    <link rel="stylesheet" type="text/css" href="/web/CourseSEL/Public/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="./css/font.css"> -->
    <link rel="stylesheet" href="/web/CourseSEL/Public/css/xadmin.css">
    <script src="/web/CourseSEL/Public/layui/layui.js"></script>
    <script src="/web/CourseSEL/Public/js/jquery-1.11.1.min.js"></script>
   <script type="text/javascript" src="/web/CourseSEL/Public/js/echarts.min.js"></script>
    <script type="text/javascript" src="/web/CourseSEL/Public/js/xadmin.js"></script>
    
    
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
<body>
        <div class="layui-container" style="margin-top: 20px;">
            <div class="layui-row">
                <div class="layui-col-md10">
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                  <legend>修改密码</legend>
                </fieldset>
                    <form class="layui-form" >
                        <div class="layui-form-item">
                          <label class="layui-form-label">原密码</label>
                          <div class="layui-input-inline">
                            <input type="password" name="pass" required  lay-verify="required" placeholder="请输入原密码" autocomplete="off" class="layui-input" id="tpass">
                          </div>
                          <div class="layui-form-mid layui-word-aux" style="color:red;">原密码</div>
                        </div>
                         <div class="layui-form-item">
                          <label class="layui-form-label">新密码</label>
                          <div class="layui-input-inline">
                            <input type="password" name="newpass1" required  lay-verify="required" placeholder="请输入新密码" autocomplete="off" class="layui-input" id="newpass1">
                          </div>
                          <div class="layui-form-mid layui-word-aux" style="color:red;">请牢记新密码</div>
                        </div>
                         <div class="layui-form-item">
                          <label class="layui-form-label">新密码</label>
                          <div class="layui-input-inline">
                            <input type="password" name="newpass2" required  lay-verify="required" placeholder="请再次输入新密码" autocomplete="off" class="layui-input" id="newpass2">
                          </div>
                          <div class="layui-form-mid layui-word-aux" style="color:red;">新密码验证</div>
                        </div>
                        <div class="layui-form-item">
                          <div class="layui-input-block">
                          <!-- <a class="layui-btn" href="javascript:;" lay-submit onclick="sub('<?php echo ($te["tid"]); ?>')">立即提交</a> -->
                          <!-- <button class="layui-btn" lay-submit onsubmit="sub('<?php echo ($te["tid"]); ?>')">立即提交</button> -->
                            <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                          </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>  
    <script>
layui.use(['layer','element','form'], function(){
    var layer = layui.layer,element=layui.element,form=layui.form;
        form.on('submit(formDemo)', function(){
        // layer.msg(JSON.stringify(data.field));
        //alert(1);
        var pass=$('#tpass').val(),newpass1=$('#newpass1').val(),newpass2=$('#newpass2').val();
        //alert(tname);
        $.post("<?php echo U('check_tpass');?>",{pass:pass},function(data,status){
              //alert("Data: " + data + "\nStatus: " + status);
              if (data == 0) {
                layer.msg('您输入的原密码错误!',{icon: 5,time:2000});
                // setTimeout("x_admin_close()",1000);
                // setTimeout("window.parent.location.reload()",1000);
              } else{
                if (newpass1!=newpass2) {
                    layer.msg('您两次输入的新密码不一致!',{icon: 5,time:2000});
                } else {
                    $.post("<?php echo U('do_tpass');?>",{pass:newpass2},function(data1,status){
                         //alert("Data: " + data + "\nStatus: " + status);
                         //alert(data1)
                        layer.msg('修改成功！',{icon: 6,time:1000});
                        setTimeout("x_admin_close()",1000);
                    })
                    
                }
              } 
          });
        return false;
      });
});
 </script>

</body>

</html>