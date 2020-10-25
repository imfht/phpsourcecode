<?php /*a:2:{s:49:"D:\phpstudy_pro\WWW\tp\view\home\goods\edits.html";i:1601600647;s:51:"D:\phpstudy_pro\WWW\tp\view\home\common\static.html";i:1601423018;}*/ ?>
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

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <script src="/static/jquery.printarea.js"></script>

    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


    <link href="/static/toastr/toastr.css" rel="stylesheet"/>
    <script src="/static/toastr/toastr.js"></script>

</head>
    <body>
        <div class="layui-fluid">
            <div class="layui-row">
                <form class="layui-form">
                  <div class="layui-form-item">
                      <label for="username" class="layui-form-label">
                          <span class="x-red">*</span>商品名称
                      </label>
                      <div class="layui-input-inline">
                          <input type="text" id="name" name="username" required="" lay-verify="required"
                          autocomplete="off" class="layui-input" value="<?php echo htmlentities($list['name']); ?>">
                      </div>
                  </div>
                    <div class="layui-form-item">
                        <label for="username" class="layui-form-label">
                            <span class="x-red">*</span>商品价格
                        </label>
                        <div class="layui-input-inline">
                            <input type="text" id="price" name="username" required="" lay-verify="required"
                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($list['price']); ?>">
                        </div>
                    </div>
                    <input type="hidden" value="<?php echo htmlentities($list['id']); ?>" id="id">

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
    function adds(){
        $.ajax({
            type:"post",
            url: "<?php echo url('home/goods/edits'); ?>",
            data: {
                name:$('#name').val(),
                price:$('#price').val(),
                id:$('#id').val(),
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
