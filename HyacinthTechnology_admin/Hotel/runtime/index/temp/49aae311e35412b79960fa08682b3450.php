<?php /*a:2:{s:52:"D:\phpstudy_pro\WWW\tp\view\index\activity\adds.html";i:1598834280;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;}*/ ?>
<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>BOOL酒店管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="/static/admin/css/font.css">
    <link rel="stylesheet" href="/static/admin/css/xadmin.css">
    <script src="/static/admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/admin/js/xadmin.js"></script>

    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <link href="/static/toastr/toastr.css" rel="stylesheet"/>
    <script src="/static/toastr/toastr.js"></script>
</head>
    <body>
        <div class="layui-fluid">
            <div class="layui-row">
                <form class="layui-form">
                  <div class="layui-form-item">

                      <label for="username" class="layui-form-label">
                          <span class="x-red">*</span>开始日
                      </label>
                      <div class="layui-input-inline layui-show-xs-block">
                          <input class="layui-input" placeholder="开始日" name="start" id="start">
                      </div>
                  </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>截止日
                        </label>
                        <div class="layui-input-inline layui-show-xs-block">
                            <input class="layui-input" placeholder="截止日" name="end" id="end">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>活动名称
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="name" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>房型
                        </label>
                        <div class="layui-input-inline layui-show-xs-block">
                            <select name="contrller" id="layout">
                                <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['type_name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>价格
                        </label>
                        <div class="layui-input-inline layui-show-xs-block">
                            <select name="contrller" id="price">
                                <option value="0.9">9折</option>
                                <option value="0.8">8折</option>
                                <option value="0.7">7折</option>
                                <option value="0.6">6折</option>
                                <option value="0.5">5折</option>
<!--                                <option>4折</option>
                                <option>3折</option>
                                <option>2折</option>
                                <option>1折</option>-->
                            </select>
                        </div>
                    </div>

                  <div class="layui-form-item">
                      <label for="L_repass" class="layui-form-label">
                      </label>
                      <button  class="layui-btn" type="button" onclick="adds()">
                          增加
                      </button>
                  </div>
              </form>
            </div>
        </div>

    </body>
<script>

    layui.use(['laydate', 'form'],
            function() {
                var laydate = layui.laydate;

                //执行一个laydate实例
                laydate.render({
                    elem: '#start' //指定元素
                });

                //执行一个laydate实例
                laydate.render({
                    elem: '#end' //指定元素
                });
            });

    function adds(){
        $.ajax({
            type:"post",
            url: "<?php echo url('index/activity/adds'); ?>",
            data: {
                start:$('#start').val(),
                end:$('#end').val(),
                layout_id:$('#layout').val(),
                price:$('#price').val(),
                name:$('#name').val()
            },
            success: function(data){
                console.log(data);
                toastr.error(data.msg);
                if(data.code == 100){
                    setTimeout(function () {
                        layer.closeAll();
                        parent.location.reload();
                    },1500);
                }
            }});
    }
</script>

</html>
