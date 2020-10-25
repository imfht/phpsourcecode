<?php /*a:3:{s:50:"D:\phpstudy_pro\WWW\tp\view\apply\voice\index.html";i:1601170783;s:52:"D:\phpstudy_pro\WWW\tp\view\apply\common\static.html";i:1591060588;s:55:"D:\phpstudy_pro\WWW\tp\view\apply\common\resources.html";i:1591061599;}*/ ?>
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
<link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.0/css/bootstrap.css" rel="stylesheet">
    <body>
        <div class="x-nav">
            <span class="layui-breadcrumb">
                <a href="">首页</a>
                <a href="">演示</a>
                <a>
                    <cite>导航元素</cite></a>
            </span>
            <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>
            </a>
        </div>
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">

                        <form class="layui-form" action="">

                            <div class="layui-form-item">
                                <label class="layui-form-label">语音设置</label>
                                <div class="layui-input-block">

                                    <input type="radio" name="sex" value="on" title="开"
                                           <?php if($list['status'] == '1'): ?>checked<?php endif; ?>
                                    >
                                    <input type="radio" name="sex" value="off" title="关"
                                           <?php if($list['status'] == '0'): ?>checked<?php endif; ?>
                                    >
                                </div>
                            </div>
                            <div class="layui-input-inline layui-show-xs-block" style="margin-left: 15px;">
                                <select name="contrller" id="types">

                                    <?php if($list['types'] == '1'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">磁性男声</option>
                                    <?php elseif($list['types'] == '2'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">甜美女生</option>
                                    <?php elseif($list['types'] == '3'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">情感小萌</option>
                                    <?php elseif($list['types'] == '4'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">情感小娇</option>
                                    <?php elseif($list['types'] == '5'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">情感男生</option>
                                    <?php elseif($list['types'] == '6'): ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">可爱米朵</option>
                                    <?php else: ?>
                                        <option value="<?php echo htmlentities($list['types']); ?>">可爱小童</option>
                                    <?php endif; ?>

                                    <option value="1">磁性男声</option>
                                    <option value="2">甜美女生</option>
                                    <option value="3">情感小萌</option>
                                    <option value="4">情感小娇</option>
                                    <option value="5">情感男生</option>
                                    <option value="6">可爱米朵</option>
                                    <option value="7">可爱小童</option>
                                </select>
                            </div>
                        </form>

                        <div class="layui-card-header">

                            <button class="layui-btn" onclick="edits()">
                                <i class="layui-icon"></i>保存
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </body>

<script>


layui.use('form', function(){
    var form = layui.form;
});

function edits(){
    $.ajax({
        type:"post",
        url: "<?php echo url('apply/voice/edits'); ?>",
        data: {
            types:$('#types').val(),
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

    /*班次-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？',
        function(index) {
            $.ajax({
                type:"post",
                url: "<?php echo url('index/classe/deletes'); ?>",
                data: {
                    id:id
                },
                success: function(data){
                    console.log(data);
                    toastr.error(data.msg);
                    if(data.code == 100){

                        layer.closeAll();
                        $(obj).parents("tr").remove();
                        setTimeout(function () {
                            location.reload();
                        },1000);
                    }
                }});
        });
    }

</script>


</html>