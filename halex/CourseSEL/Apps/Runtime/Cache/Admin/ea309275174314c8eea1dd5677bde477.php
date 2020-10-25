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
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">学生管理</a>
        <a>
          <cite>学生列表</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" action="/web/CourseSEL/index.php/Admin/Stu/search" method="post">
        <div class="layui-form-item">
        <?php if($_SESSION['type']== 2 ): ?><div class="layui-inline">
            <div class="layui-input-inline">
              <select name="schoolid"id="schoolid">
                  <option value="">请选择学校</option>
                  <option value="1">孝义中学</option>
                  <option value="2">孝义二中</option>
                  <option value="3">孝义三中</option>
                  <option value="4">孝义四中</option>
                  <option value="5">孝义五中</option>
                  <option value="6">孝义实验中学</option>
                  <option value="7">孝义华杰中学</option>
                  <option value="8">孝义艺苑中学</option>
                </select>
            </div>
          </div><?php endif; ?>
          <div class="layui-inline">
            <div class="layui-input-inline">
              <input type="text" name="class"  placeholder="请输入班级" autocomplete="off" class="layui-input">
            </div>
          </div>
          <div class="layui-inline">
            <div class="layui-input-inline">
              <input type="text" name="sname"  placeholder="请输入学生姓名，支持模糊查询" autocomplete="off" class="layui-input">
            </div>
             <div class="layui-input-inline" style="margin-left: -60px;">
              <button class="layui-btn"  lay-submit lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
            </div>
          </div>
        </div>
        
          <!-- <input class="layui-input" placeholder="开始日" name="start" id="start">
          <input class="layui-input" placeholder="截止日" name="end" id="end"> -->
         <!--  <input type="text" name="tname"  placeholder="请输入帐号，支持模糊查询" autocomplete="off" class="layui-input" required  lay-verify="required">
          <button class="layui-btn"  lay-submit lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button> -->
        </form>
      </div>
      <xblock>
        <button class="layui-btn layui-btn-danger" onclick="check_admin();"><i class="layui-icon"></i>批量删除</button>
        <button class="layui-btn"  onclick=" x_admin_show('添加','/web/CourseSEL/index.php/Admin/Stu/stuadd');"><i class="layui-icon"></i>添加</button>
        
        <span class="x-right" style="line-height:40px">共有数据：<?php echo ($num); ?> 条</span>
      </xblock>
      <table class="layui-table">
        <thead>
          <tr>
            <th>
              <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>导入名称</th>
            <th>学(籍)号</th>
            <th>姓名</th>
            <th>性别</th>
            <th>班级</th>
            <th>入学年</th>
            <th>学校</th>
            <th>状态</th>
            <th>注册时间</th>
            <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($stu)): $i = 0; $__LIST__ = $stu;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
                  <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='<?php echo ($vo["sid"]); ?>'><i class="layui-icon">&#xe605;</i></div>
                </td>
                <td ><?php echo ($vo["sid"]); ?></td>
                <td ><?php echo ($vo["stuin_title"]); ?></td>
                <td><?php echo ($vo["stuid"]); ?></td>
                <td ><?php echo ($vo["sname"]); ?></td>
                <td ><?php echo ($vo['sex']==1?'男':'女'); ?></td>
                <td ><?php echo ($vo["class"]); ?></td>
                <td ><?php echo ($vo["year"]); ?></td>
                <td ><?php echo (getschool($vo["schoolid"])); ?></td>
                <!-- <td class="td-status"><span class="layui-btn layui-btn-small "><?php echo ($vo['status']==1?'启用':'禁用'); ?></span></td> -->
                <td class="td-status"><?php if($vo['status'] == 1 ): ?><span class="layui-btn layui-btn-small ">启用</span>
                <?php else: ?> 
                <span class="layui-btn layui-btn-small layui-btn-disabled">禁用</span><?php endif; ?></td>
                <td ><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
                <td class="td-manage">
                
                    <a onclick="user_stop(this,'<?php echo ($vo["sid"]); ?>')" href="javascript:;"  title="<?php echo ($vo['status']==1?'启用':'禁用'); ?>">
                       <?php if($vo['status'] == 1 ): ?><i class="layui-icon">&#xe616;</i>
                        <?php else: ?> 
                        <i class="layui-icon">&#x1007;</i><?php endif; ?>

                    </a>
                    <a title="编辑"  onclick="x_admin_show('编辑','/web/CourseSEL/index.php/Admin/Stu/stuedit/id/<?php echo ($vo["sid"]); ?>')" href="javascript:;">
                      <i class="layui-icon">&#xe642;</i>
                    </a>
                    <a title="删除" onclick="member_del(this,'<?php echo ($vo["sid"]); ?>')" v="<?php echo ($vo["sname"]); ?>" href="javascript:;">
                      <i class="layui-icon">&#xe640;</i>
                    </a>
              </td>
              </tr><?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
          <!-- <tr>
            <td>
              <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='2'><i class="layui-icon">&#xe605;</i></div>
            </td>
            <td>1</td>
            <td>admin</td>
            <td>18925139194</td>
            <td>113664000@qq.com</td>
            <td>超级管理员</td>
            <td>2017-01-01 11:11:42</td>
            <td class="td-status">
              <span class="layui-btn layui-btn-normal layui-btn-mini">已启用</span></td>
            <td class="td-manage">
              <a onclick="member_stop(this,'10001')" href="javascript:;"  title="启用">
                <i class="layui-icon">&#xe601;</i>
              </a>
              <a title="编辑"  onclick="x_admin_show('编辑','admin-edit.html')" href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
              <a title="删除" onclick="member_del(this,'要删除的id')" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>
            </td>
          </tr> -->
        </tbody>
      </table> 
      <div class="page" style="text-align: center;">
            <?php echo ($page); ?>
        <!-- <div>
          <a class="prev" href="">&lt;&lt;</a>
          <a class="num" href="">1</a>
          <span class="current">2</span>
          <a class="num" href="">3</a>
          <a class="num" href="">489</a>
          <a class="next" href="">&gt;&gt;</a>
        </div> -->
      </div>

    </div>
    <script>
      // layui.use('laydate', function(){
      //   var laydate = layui.laydate;
        
      //   //执行一个laydate实例
      //   laydate.render({
      //     elem: '#start' //指定元素
      //   });

      //   //执行一个laydate实例
      //   laydate.render({
      //     elem: '#end' //指定元素
      //   });
      // });
      layui.use('table', function(){
        var table = layui.table;
        
      });
       /*用户-停用*/
      function user_stop(obj,id){
            if ($(obj).attr('title')=='启用') {
                var mes='确定要禁用该学生吗？';
            } else {
              mes='确定要启用该学生吗？';
            }
          layer.confirm(mes+id,function(index){

              if($(obj).attr('title')=='启用'){
                //发异步把用户状态进行更改
                  $.get("<?php echo U('user_stop');?>",{sid:id},function(data,status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (data >= 1) {
                          $(obj).attr('title','禁用')
                          $(obj).find('i').html('&#x1007;');

                          $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已禁用');
                          layer.msg('已禁用!',{icon: 5,time:1000});
                        } else{
                          layer.msg('修改学生状态失败！，请联系管理员！');
                        } 
                  });
              }else{
                  $.get("<?php echo U('user_open');?>",{sid:id},function(data,status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (data >= 1) {
                            $(obj).attr('title','启用')
                            $(obj).find('i').html('&#xe616;');

                            $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                            layer.msg('已启用!',{icon: 6,time:1000});
                        } else {
                            layer.msg('修改学生状态失败！，请联系管理员！');
                        }
                  });
              }
              
          });
      };

      /*用户-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除['+$(obj).attr('v')+']吗？',function(index){
              //发异步删除数据
              $.get("<?php echo U('tdel');?>",{sid:id},function(data,status){
                  //alert("Data: " + data + "\nStatus: " + status);
                  if (data >= 1) {
                    $(obj).parents("tr").remove();
                    layer.msg('已删除!',{icon:1,time:2000});
                    //layer.msg('删除成功!',{icon: 6,time:2000});
                  } else{
                    layer.msg('删除失败！请联系管理员！',{icon: 5,time:1000});
                    //x_admin_close();
                  } 
                 
              });
              
          });
      };
      function delAll (argument) {
        var data = tableCheck.getData();
        //alert(data);
        layer.confirm('确认要删除吗？'+data,function(index){
            //捉到所有被选中的，发异步进行删除
            $.get("<?php echo U('delall');?>",{sid:data},function(data,status){
                  //alert("Data: " + data + "\nStatus: " + status);
                  if (data >= 1) {
                    layer.msg('成功删除'+data+'条数据', {icon: 1,time:2000});
                    $(".layui-form-checked").not('.header').parents('tr').remove();
                  } else{
                    layer.msg('删除失败！请联系管理员！',{icon: 5,time:1000});
                    //x_admin_close();
                  } 
                 
              });
           
        });
      };
      function check_admin(){
        if ('<?php echo (session('type')); ?>'!=0) {
            delAll ();
          } else {
            layer.msg('无此权限，请联系学校管理员!',{icon: 5,time:2000});
          }
      }
    </script>
    <script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
      })();</script>
  </body>

</html>