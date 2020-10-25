<?php /*a:3:{s:50:"D:\phpstudy_pro\WWW\tp\view\home\index\handle.html";i:1601619035;s:51:"D:\phpstudy_pro\WWW\tp\view\home\common\static.html";i:1601423018;s:54:"D:\phpstudy_pro\WWW\tp\view\home\common\resources.html";i:1601423784;}*/ ?>
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
        <div class="layui-fluid" id="app">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">

                        <div class="layui-card-header">
                            <a class="layui-btn" href="<?php echo url('home/index/welcome'); ?>">
                                <i class="layui-icon"></i>返回房态
                            </a>
                            <button class="layui-btn" onclick="xadmin.open('随客管理','/home/index/peers/room_id/<?php echo htmlentities($list['id']); ?>',1000,700)">
                                <i class="layui-icon"></i>随客管理
                            </button>
                            <a class="layui-btn" href="/home/invoice/into_house/id/<?php echo htmlentities($list['id']); ?>">
                                <i class="layui-icon"></i>打印单据
                            </a>
                        </div>
                        <div class="layui-card-body ">


                            <table class="layui-table" lay-skin="line">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">主客姓名</label>
                                            <div class="layui-input-inline">
                                                <input type="text" id="guest_name" value="<?php echo htmlentities($list['guest_name']); ?>" required lay-verify="required" placeholder="请输入姓名" autocomplete="off" class="layui-input">
                                            </div>
                                            <div class="layui-form-mid layui-word-aux">
                                                <a href="#myModal" role="button" class="btn icon iconfont" data-toggle="modal">会员</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">促销活动</label>
                                                <div class="layui-input-block">
                                                    <select name="city" lay-verify="required" id="activity_id">
                                                        <?php if(is_array($activity) || $activity instanceof \think\Collection || $activity instanceof \think\Paginator): $i = 0; $__LIST__ = $activity;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                            <option value=<?php echo htmlentities($vo['id']); ?>><?php echo htmlentities($vo['name']); ?></option>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item" style="width: 240px;float: left;">
                                                <label class="layui-form-label">证件类型</label>
                                                <div class="layui-input-block">
                                                    <select name="city" lay-verify="required" >
                                                        <?php if(is_array($identity) || $identity instanceof \think\Collection || $identity instanceof \think\Paginator): $i = 0; $__LIST__ = $identity;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                                        <option value=<?php echo htmlentities($v['id']); ?>><?php echo htmlentities($v['identity']); ?></option>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="layui-form-item" >
                                            <label class="layui-form-label">证件号码</label>
                                            <div class="layui-input-inline">
                                                <input type="text" id="credentials" value="<?php echo htmlentities($list['credentials']); ?>" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item" style="width: 300px;float: left;">
                                                <label class="layui-form-label">宾客性别</label>
                                                <div class="layui-input-block">
                                                    <select name="city" lay-verify="required" id="guest_sex">
                                                        <option value="0">男</option>
                                                        <option value="1">女</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item" style="width: 300px;float: right;margin-top: -55px;margin-right: 440px;">
                                                <label class="layui-form-label">宾客来源</label>
                                                <div class="layui-input-block">
                                                    <select name="city" lay-verify="required" id="guest_source">
                                                        <?php if(is_array($guest) || $guest instanceof \think\Collection || $guest instanceof \think\Paginator): $i = 0; $__LIST__ = $guest;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?>
                                                            <option value=<?php echo htmlentities($vv['id']); ?>><?php echo htmlentities($vv['guest']); ?></option>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">宾客人数</label>
                                            <div class="layui-input-inline">
                                                <input type="text" id="guest_number" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="layui-form-item" >
                                            <label class="layui-form-label">预住时长</label>
                                            <div class="layui-input-inline">
                                                <input type="text" id="move_duration" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>

                                        <div class="layui-input-inline layui-show-xs-block" style="width: 200px;float: left;margin-left: 350px;margin-top: -60px;">
                                            <input class="layui-input" placeholder="预离时间" name="end" id="end">
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <form class="layui-form" action="">
                                            <div class="layui-form-item">
                                                <label class="layui-form-label">收款方式</label>
                                                <div class="layui-input-block">
                                                    <select name="city" lay-verify="required" id="payment_id">
                                                        <?php if(is_array($payment) || $payment instanceof \think\Collection || $payment instanceof \think\Paginator): $i = 0; $__LIST__ = $payment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>
                                                        <option value=<?php echo htmlentities($v1['id']); ?>><?php echo htmlentities($v1['pay_name']); ?></option>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                            <input type="text" id="member_id" value="0">

                                    </td>

                                </tr>
                                </tbody>
                            </table>


<!----------------------------------------------------------------------------------------------------------------->
                            <div class="layui-card-header">
                                <button class="layui-btn" onclick="xadmin.open('添加用户','/home/index/addroom/room_id/<?php echo htmlentities($list['id']); ?>',800,600)">
                                    <i class="layui-icon"></i>追加房间
                                </button>
<!--                                <button class="layui-btn" onclick="xadmin.open('添加用户','/index/storeys/adds',500,200)">
                                    <i class="layui-icon"></i>移除房间
                                </button>-->
                                <button class="layui-btn" onclick="adds(<?php echo htmlentities($list['id']); ?>)">
                                    <i class="layui-icon"></i>确定保存
                                </button>
                                <a class="layui-btn" href="/home/invoice/bill/id/<?php echo htmlentities($list['id']); ?>">
                                    <i class="layui-icon"></i>查看票据
                                </a>
                                <span>主客房间：<?php echo htmlentities($list['room_num']); ?></span>
                            </div>
                            <table class="layui-table">
                                <colgroup>
                                    <col width="150">
                                    <col width="200">
                                    <col>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>房间号</th>
                                    <th>房间类型</th>
                                    <th>房间价格</th>
                                    <th>定金</th>
                                    <th>楼层</th>
                                    <th>预离时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
<!--                                <tr>
                                    <td><?php echo htmlentities($list['room_num']); ?></td>
                                    <td>游戏间</td>
                                    <td>168￥</td>
                                    <td>2016-11-29</td>
                                </tr>-->
                                <?php if(is_array($show) || $show instanceof \think\Collection || $show instanceof \think\Paginator): $i = 0; $__LIST__ = $show;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <td><?php echo htmlentities($vo['room_num']); ?></td>
                                    <td><?php echo htmlentities($vo['type_name']); ?></td>
                                    <td><?php echo htmlentities($vo['price']); ?></td>
                                    <td><?php echo htmlentities($vo['deposit']); ?></td>
                                    <td><?php echo htmlentities($vo['storey']); ?></td>
                                    <td>2016-11-29</td>
                                    <td class="td-manage">
                                        <a title="删除" onclick="member_del(this,<?php echo htmlentities($vo['id']); ?>)" href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">会员</h3>
            </div>
            <div class="modal-body">

                <table class="layui-table layui-form">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>会员</th>
                        <th>证据号码</th>
                        <th>操作</th></tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($member) || $member instanceof \think\Collection || $member instanceof \think\Paginator): $i = 0; $__LIST__ = $member;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?>
                    <tr>
                        <td><?php echo htmlentities($m['id']); ?></td>
                        <td><?php echo htmlentities($m['name']); ?></td>
                        <td><?php echo htmlentities($m['identity']); ?></td>
                        <td class="td-manage">
                            <a title="查看" onclick="member(<?php echo htmlentities($m['id']); ?>)" href="javascript:;">
                                <i class="layui-icon">&#xe63c;</i>选择
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
            </div>
        </div>




    </body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    //Demo
    layui.use('form', function(){
        var form = layui.form;

        //监听提交
        form.on('submit(formDemo)', function(data){
            layer.msg(JSON.stringify(data.field));
            return false;
        });
    });
    layui.use(['laydate', 'form'],
            function() {
                var laydate = layui.laydate;

                //执行一个laydate实例
                laydate.render({
                    elem: '#end' //指定元素
                });
            });
    $('#myModal').show();
    ws = new WebSocket("ws://127.0.0.1:8282");

    /*
    * 办理入住
    * */
    function adds(id){
        $.ajax({
            type:"post",
            url: "<?php echo url('home/index/handle'); ?>",
            data: {
                id:id,
                payment_id:$('#payment_id').val(),
                guest_name:$('#guest_name').val(),
                activity_id:$('#activity_id').val(),
                credentials:$('#credentials').val(),
                guest_sex:$('#guest_sex').val(),
                guest_source:$('#guest_source').val(),
                guest_number:$('#guest_number').val(),
                move_duration:$('#move_duration').val(),
                move_time:$('#end').val(),
                status:2,
                member_id:$('#member_id').val()
            },
            success: function(data){
                console.log(data);
                toastr.error(data.msg);
                if(data.code == 100){
                    // 假设服务端ip为127.0.0.1
                    ws = new WebSocket("ws://127.0.0.1:8282");
                    ws.onopen = function() {

                        //右下弹出
                        layer.open({
                            type: 1
                            ,offset: 'rb'
                            ,content: '<div style="padding: 20px 80px;">入住成功</div>'
                            ,btn: '关闭全部'
                            ,btnAlign: 'c' //按钮居中
                            ,shade: 0 //不显示遮罩
                            ,anim: 2
                            ,yes: function(){
                                layer.closeAll();
                            }
                        });

                        var audio= new Audio("/static/voice/2/welcome.mp3");

                        audio.play();//播放
                        ws.send('tom');

                    };
                    ws.onmessage = function(e) {
                        // alert("收到服务端的消息：" + e.data);
                        console.log("收到服务端的消息：" + e.data);
                    };

                    setTimeout(function () {
                        // window.location.href="<?php echo url('/home/index/bill/id/'); ?>"+id;
                    },4000);
                }
            }});
    }

    /*会员选择*/
    function member(id) {
        $.ajax({
            type:"post",
            url: "<?php echo url('home/index/select_member'); ?>",
            data: {
                id:id,
            },
            success: function(data){
                console.log(data);
                console.log(data.name);
                $('#guest_name').val(data.name);
                $('#credentials').val(data.identity);
                $('#member_id').val(data.id);
                // $('#myModal').hide();
            }});
    }

    /*房间移除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？',
                function(index) {
                    $.ajax({
                        type:"post",
                        url: "<?php echo url('/home/index/remove'); ?>",
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
                                    // location.reload();
                                },1000);
                            }
                        }});
                });
    }


</script>
</html>