<?php /*a:3:{s:58:"D:\phpstudy_pro\WWW\tp\view\home\purchase\warehousing.html";i:1601600835;s:51:"D:\phpstudy_pro\WWW\tp\view\home\common\static.html";i:1601423018;s:54:"D:\phpstudy_pro\WWW\tp\view\home\common\resources.html";i:1601423784;}*/ ?>
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
<!--<link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.0/css/bootstrap.css" rel="stylesheet">-->
<link rel="stylesheet" href="/static/bootstrap/css/bootstrap.css">
<script src="/static/bootstrap/js/bootstrap.js"></script>
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
                                    <select name="contrller">
                                        <option>未入库</option>
                                        <option>已入库</option>
                                    </select>
                                </div>

                                <div class="layui-input-inline layui-show-xs-block">
                                    <input type="text" name="username" placeholder="请输入订单号" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-input-inline layui-show-xs-block">
                                    <button class="layui-btn" lay-submit="" lay-filter="sreach">
                                        <i class="layui-icon">&#xe615;</i></button>
                                </div>
                            </form>
                        </div>

                        <div class="layui-card-body ">
                            <table class="layui-table layui-form">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>订单编号</th>
                                        <th>创建时间</th>
                                        <th>操作</th></tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <td><?php echo htmlentities($vo['id']); ?></td>
                                    <td><?php echo htmlentities($vo['order_num']); ?></td>

                                    <td><?php echo htmlentities($vo['create_time']); ?></td>
                                    <td class="td-manage">

                                        <a title="查看"  href="/home/purchase/details/id/<?php echo htmlentities($vo['id']); ?>">
                                            <i class="layui-icon">&#xe63c;</i>入库
                                        </a>
<!--                                        <a title="删除" onclick="member_del(this,<?php echo htmlentities($vo['id']); ?>)" href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i>
                                        </a>-->
                                    </td>
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

<script>layui.use(['laydate', 'form'],
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
        url: "<?php echo url('home/purchase/add_order'); ?>",
        data: {
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