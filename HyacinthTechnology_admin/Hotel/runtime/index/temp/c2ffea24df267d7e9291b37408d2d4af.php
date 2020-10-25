<?php /*a:3:{s:56:"D:\phpstudy_pro\WWW\tp\view\index\pricesystem\index.html";i:1601194390;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;s:55:"D:\phpstudy_pro\WWW\tp\view\index\common\resources.html";i:1591061599;}*/ ?>
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
                                    <select name="contrller">
                                        <option>支付方式</option>
                                        <option>支付宝</option>
                                        <option>微信</option>
                                        <option>货到付款</option></select>
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
                                    <th>房间号</th>
                                    <th>房间类型</th>
                                    <!--     <th>价格</th>
                                         <th>下单时间</th>-->
                                    <?php if(is_array($week) || $week instanceof \think\Collection || $week instanceof \think\Paginator): $i = 0; $__LIST__ = $week;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$w): $mod = ($i % 2 );++$i;?>
                                    <th><?php echo htmlentities($w); ?></th>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                    <th>
                                        <form class="layui-form layui-col-space5" method="post">

                                            <div class="layui-input-inline layui-show-xs-block">
                                                <button class="layui-btn" lay-submit="" lay-filter="sreach" name="type" value="0">
                                                    <i class="layui-icon">&#xe615;</i>上一周
                                                </button>
                                                <button class="layui-btn" lay-submit="" lay-filter="sreach" name="type" value="1">
                                                    <i class="layui-icon">&#xe615;</i>下一周
                                                </button>
                                            </div>
                                        </form>

                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php if($types == '0'): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                    <tr>
                                        <td><?php echo htmlentities($v['id']); ?></td>
                                        <td><?php echo htmlentities($v['room_num']); ?></td>
                                        <td><?php echo htmlentities($v['type_name']); ?></td>

                                        <td>
                                            <input type="text" lay-verify="required" id="monday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['monday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="tuesday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['tuesday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="wednesday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['wednesday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="thursday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['thursday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="friday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['friday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="saturday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['saturday']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="sunday<?php echo htmlentities($v['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($v['sunday']); ?>">
                                        </td>
                                        <td class="td-manage">
                                            <a title="查看" onclick="edits(<?php echo htmlentities($v['id']); ?>)">
                                                <i class="layui-icon">&#xe63c;</i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; else: echo "" ;endif; else: if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <tr>
                                        <td><?php echo htmlentities($vo['id']); ?></td>
                                        <td><?php echo htmlentities($vo['room_num']); ?></td>
                                        <td><?php echo htmlentities($vo['type_name']); ?></td>

                                        <td>
                                            <input type="text" lay-verify="required" id="eight<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['eight']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="nine<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['nine']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="ten<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['ten']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="eleven<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['eleven']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="twelve<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['twelve']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="thirteen<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['thirteen']); ?>">
                                        </td>
                                        <td>
                                            <input type="text" lay-verify="required" id="fourteen<?php echo htmlentities($vo['id']); ?>"
                                                   autocomplete="off" class="layui-input" value="<?php echo htmlentities($vo['fourteen']); ?>">
                                        </td>
                                        <td class="td-manage">
                                            <a title="查看" onclick="edits(<?php echo htmlentities($vo['id']); ?>)">
                                                <i class="layui-icon">&#xe63c;</i></a>
                                        </td>

                                    </tr>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                <?php endif; ?>

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

function edits(id){
    $.ajax({
        type:"post",
        url: "<?php echo url('index/pricesystem/edits'); ?>",
        data: {
            id:id,
            monday:$('#monday'+id).val(),
            tuesday:$('#tuesday'+id).val(),
            wednesday:$('#wednesday'+id).val(),
            thursday:$('#thursday'+id).val(),
            friday:$('#friday'+id).val(),
            saturday:$('#saturday'+id).val(),
            sunday:$('#sunday'+id).val(),
            eight:$('#eight'+id).val(),
            nine:$('#nine'+id).val(),
            ten:$('#ten'+id).val(),
            eleven:$('#eleven'+id).val(),
            twelve:$('#twelve'+id).val(),
            thirteen:$('#thirteen'+id).val(),
            fourteen:$('#fourteen'+id).val()
        },
        success: function(data){
            console.log(data);
            toastr.error(data.msg);
            if(data.code == 100){
                setTimeout(function () {
                    parent.location.reload();
                },1000);
            }
        }});
}


</script>

</html>