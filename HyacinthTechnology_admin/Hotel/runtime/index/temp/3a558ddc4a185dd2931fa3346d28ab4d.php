<?php /*a:3:{s:57:"D:\phpstudy_pro\WWW\tp\view\index\goods\select_goods.html";i:1601027639;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;s:55:"D:\phpstudy_pro\WWW\tp\view\index\common\resources.html";i:1591061599;}*/ ?>
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
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space5">
                        <div class="layui-input-inline layui-show-xs-block">
                            <input class="layui-input" placeholder="开始日" name="start" id="start"></div>
                        <div class="layui-input-inline layui-show-xs-block">
                            <input class="layui-input" placeholder="截止日" name="end" id="end"></div>

                        <div class="layui-input-inline layui-show-xs-block">
                            <input type="text" name="username" placeholder="请输入订单号" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-input-inline layui-show-xs-block">
                            <button class="layui-btn" lay-submit="" lay-filter="sreach">
                                <i class="layui-icon">&#xe615;</i></button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-header">

                    <button class="layui-btn" onclick="xadmin.open('添加用户','/index/goods/adds',500,250)">
                        <i class="layui-icon"></i>添加</button>
                </div>
                <div class="layui-card-body ">
                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>商品名称</th>
                            <th>价格</th>
                            <th>总数量</th>
                            <th>创建时间</th>
                        </thead>
                        <tbody>
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td><?php echo htmlentities($vo['id']); ?></td>
                            <td><?php echo htmlentities($vo['name']); ?></td>
                            <td><?php echo htmlentities($vo['price']); ?></td>
                            <td><?php echo htmlentities($vo['number']); ?></td>
                            <td><?php echo htmlentities($vo['create_time']); ?></td>
                <!--            <td class="td-manage">
                                <a title="查看" onclick="show(<?php echo htmlentities($vo['id']); ?>)" href="javascript:;">
                                    <i class="layui-icon">&#xe63c;</i>查看数量</a>

                            </td>-->
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                    <?php echo $list; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    function show(id) {
        alert(id);
        $.ajax({
            type:"post",
            url: "<?php echo url('index/goods/edits'); ?>",
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