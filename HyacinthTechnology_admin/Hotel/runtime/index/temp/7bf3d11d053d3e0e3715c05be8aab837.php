<?php /*a:2:{s:49:"D:\phpstudy_pro\WWW\tp\view\index\rooms\adds.html";i:1601010022;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;}*/ ?>
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
                          <span class="x-red">*</span>房间号码
                      </label>
                      <div class="layui-input-inline">
                          <input type="text" id="room_num" name="username" required="" lay-verify="required"
                          autocomplete="off" class="layui-input">
                      </div>

                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">
                              <span class="x-red">*</span>房间名称</label>
                          <div class="layui-input-inline">
                              <input type="text" id="room_name" name="username" required="" lay-verify="required" autocomplete="off" class="layui-input"></div>
                      </div>

                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">
                              <span class="x-red">*</span>房间类型</label>
                          <div class="layui-input-inline">
                              <select name="shipping" class="valid" id="type_id">

                                  <?php if(is_array($layout) || $layout instanceof \think\Collection || $layout instanceof \think\Paginator): $i = 0; $__LIST__ = $layout;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                      <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['type_name']); ?></option>
                                  <?php endforeach; endif; else: echo "" ;endif; ?>

                              </select>
                          </div>
                      </div>
                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">
                              <span class="x-red">*</span>所在楼栋</label>
                          <div class="layui-input-inline">
                              <select name="shipping" class="valid" id="building_id">

                                  <?php if(is_array($building) || $building instanceof \think\Collection || $building instanceof \think\Paginator): $i = 0; $__LIST__ = $building;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?>
                                  <option value="<?php echo htmlentities($vv['id']); ?>"><?php echo htmlentities($vv['building']); ?></option>
                                  <?php endforeach; endif; else: echo "" ;endif; ?>

                              </select>
                          </div>
                      </div>
                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">
                              <span class="x-red">*</span>所在楼层</label>
                          <div class="layui-input-inline">
                              <select name="shipping" class="valid" id="storey_id">

                                  <?php if(is_array($storey) || $storey instanceof \think\Collection || $storey instanceof \think\Paginator): $i = 0; $__LIST__ = $storey;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                  <option value="<?php echo htmlentities($v['id']); ?>"><?php echo htmlentities($v['storey']); ?></option>
                                  <?php endforeach; endif; else: echo "" ;endif; ?>

                              </select>
                          </div>
                      </div>

                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">
                              <span class="x-red">*</span>酒店早餐</label>
                          <div class="layui-input-inline">
                              <select name="shipping" class="valid" id="breakfast">
                                  <option value="0">无</option>
                                  <option value="1">有</option>
                              </select>
                          </div>
                      </div>
                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">酒店床位</label>
                          <div class="layui-input-inline">
                              <input type="text" id="bed" name="username" required="" lay-verify="required" autocomplete="off" class="layui-input"></div>
                      </div>

                      <div class="layui-form-item">
                          <label for="username" class="layui-form-label">电话号码</label>
                          <div class="layui-input-inline">
                              <input type="text" id="tel" name="username" required="" lay-verify="required" autocomplete="off" class="layui-input"></div>
                      </div>

                      <div class="layui-form-item layui-form-text">
                          <label for="desc" class="layui-form-label">房间描述</label>
                          <div class="layui-input-block">
                              <textarea placeholder="请输入内容" id="desc" name="desc" class="layui-textarea"></textarea>
                          </div>
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
    layui.use(['form', 'layer'],
            function() {
                $ = layui.jquery;
                var form = layui.form,
                        layer = layui.layer;
            });
    function adds(){
        $.ajax({
            type:"post",
            url: "<?php echo url('index/rooms/adds'); ?>",
            data: {
                room_num:$('#room_num').val(),
                room_name:$('#room_name').val(),
                type_id:$('#type_id').val(),
                building_id:$('#building_id').val(),
                storey_id:$('#storey_id').val(),
                tel:$('#tel').val(),
                desc:$('#desc').val(),
                breakfast:$('#breakfast').val(),
                bed:$('#bed').val()
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
