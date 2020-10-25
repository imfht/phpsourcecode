<?php /*a:2:{s:51:"D:\phpstudy_pro\WWW\tp\view\index\layouts\adds.html";i:1601014537;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;}*/ ?>
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
                          <span class="x-red">*</span>房型
                      </label>
                      <div class="layui-input-inline">
                          <input type="text" id="type_name" name="username" required="" lay-verify="required"
                          autocomplete="off" class="layui-input">
                      </div>
                  </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>价格
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="price" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>押金
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="deposit" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>类型</label>
                        <div class="layui-input-inline">
                            <select name="shipping" class="valid" id="type">

                                <option value="1">全天房</option>
                                <option value="2">钟点1小时</option>
                                <option value="3">钟点2小时</option>
                                <option value="4">钟点3小时</option>
                                <option value="5">钟点4小时</option>

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
    layui.use(['form', 'layer'],
        function() {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;
        });

    function adds(){
        $.ajax({
            type:"post",
            url: "<?php echo url('index/layouts/adds'); ?>",
            data: {
                type_name:$('#type_name').val(),
                price:$('#price').val(),
                deposit:$('#deposit').val(),
                types:$('#type').val(),
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
