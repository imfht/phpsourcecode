<?php /*a:2:{s:51:"D:\phpstudy_pro\WWW\tp\view\index\admins\edits.html";i:1601008685;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;}*/ ?>
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
                            <span class="x-red">*</span>姓名
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="surname" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($staff['surname']); ?>">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>手机号
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="tel" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($staff['tel']); ?>">
                        </div>
                    </div>
                  <div class="layui-form-item">
                      <label for="username" class="layui-form-label">
                          <span class="x-red">*</span>登录名
                      </label>
                      <div class="layui-input-inline">
                          <input type="text" id="user" name="username" required="" lay-verify="required"
                          autocomplete="off" class="layui-input" value="<?php echo htmlentities($staff['username']); ?>">
                      </div>
                  </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>密码
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="password" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input" placeholder="不填则不需要修改">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>楼栋</label>
                        <div class="layui-input-inline">
                            <select name="shipping" class="valid" id="building_id">
                                <?php if(is_array($building) || $building instanceof \think\Collection || $building instanceof \think\Paginator): $i = 0; $__LIST__ = $building;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;if($vv['id'] == $staff['building_id']): ?>
                                        <option value="<?php echo htmlentities($vv['id']); ?>"><?php echo htmlentities($vv['building']); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; endif; else: echo "" ;endif; if(is_array($building) || $building instanceof \think\Collection || $building instanceof \think\Paginator): $i = 0; $__LIST__ = $building;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities($vv['id']); ?>"><?php echo htmlentities($vv['building']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

                            </select>
                        </div>
                    </div>

                  <div class="layui-form-item">
                      <label for="L_repass" class="layui-form-label">
                      </label>
                      <button  class="layui-btn" type="button" onclick="edits(<?php echo htmlentities($staff['id']); ?>)">
                          增加
                      </button>
                  </div>
              </form>
            </div>
        </div>

    </body>
<script>

    layui.use(['form', 'layer'],
            function() {
                $ = layui.jquery;
                var form = layui.form,
                        layer = layui.layer;
            });
    function edits(id){
        $.ajax({
            type:"post",
            url: "<?php echo url('index/admins/edits'); ?>",
            data: {
                id:id,
                username:$('#user').val(),
                password:$('#password').val(),
                building_id:$('#building_id').val(),
                tel:$('#tel').val(),
                surname:$('#surname').val()
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
