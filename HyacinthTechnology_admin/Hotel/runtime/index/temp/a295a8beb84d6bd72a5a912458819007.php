<?php /*a:3:{s:52:"D:\phpstudy_pro\WWW\tp\view\index\charges\index.html";i:1598739998;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;s:55:"D:\phpstudy_pro\WWW\tp\view\index\common\resources.html";i:1591061599;}*/ ?>
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
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">普通用户</div>
                <div class="layui-card-body ">
                    <table class="layui-table">
                        <tbody>
                        <tr>
                            <th>客户离店超时可以宽限
                                <select name="shipping" class="valid" id="overtime">
                                    <option value="<?php echo htmlentities($list['overtime']); ?>"><?php echo htmlentities($list['overtime']); ?></option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="30">40</option>
                                    <option value="30">50</option>
                                    <option value="30">60</option>
                                </select>
                                分钟
                            </th>
                            <td>客户退房时间

                                <div class="layui-input-inline layui-show-xs-block">
                                    <input type="text" name="username" placeholder="请输入时间" autocomplete="off" class="layui-input" value="<?php echo htmlentities($list['check_out']); ?>" id="check_out">
                                </div>
                                点
                            </td>
                        </tr>

                        <tr>
                            <th>入住次日后离店(过夜审后)，离店时间超过退房时间加收全天房价*
                                <select name="shipping" class="valid" id="exceed">
                                    <option value="<?php echo htmlentities($list['exceed']); ?>"><?php echo htmlentities($list['exceed']); ?></option>
                                    <option value="0.1">0.1</option>
                                    <option value="0.2">0.2</option>
                                    <option value="0.3">0.3</option>
                                    <option value="0.4">0.4</option>
                                    <option value="0.5">0.5</option>
                                </select>
                                ，</br>离店时间在

                                <div class="layui-input-inline layui-show-xs-block">
                                    <input type="text" name="username" placeholder="请输入时间" autocomplete="off" class="layui-input" value="<?php echo htmlentities($list['leave']); ?>" id="leave">
                                </div>
                                点以后
                                直接加收一天费用
                                <select name="shipping" class="valid" id="additional">
                                    <option value="<?php echo htmlentities($list['additional']); ?>"><?php echo htmlentities($list['additional']); ?></option>
                                    <option value="0.1">0.1</option>
                                    <option value="0.2">0.2</option>
                                    <option value="0.3">0.3</option>
                                    <option value="0.4">0.4</option>
                                    <option value="0.5">0.5</option>
                                </select>

                            </th>
                            <td>当日入住，当日离店(未过夜审)超过退房时间加收全天房价*
                                <select name="shipping" class="valid" id="exceed_plus">
                                    <option value="<?php echo htmlentities($list['exceed_plus']); ?>"><?php echo htmlentities($list['exceed_plus']); ?></option>
                                    <option value="0.1">0.1</option>
                                    <option value="0.2">0.2</option>
                                    <option value="0.3">0.3</option>
                                    <option value="0.4">0.4</option>
                                    <option value="0.5">0.5</option>
                                </select>
                                ，</br>离店时间在

                                <div class="layui-input-inline layui-show-xs-block">
                                    <input type="text" name="username" placeholder="请输入时间" autocomplete="off" class="layui-input" value="<?php echo htmlentities($list['leave_plus']); ?>" id="leave_plus">
                                </div>
                                点以后
                                直接加收一天费用
                                <select name="shipping" class="valid" id="additional_plus">
                                    <option value="<?php echo htmlentities($list['additional_plus']); ?>"><?php echo htmlentities($list['additional_plus']); ?></option>
                                    <option value="0.1">0.1</option>
                                    <option value="0.2">0.2</option>
                                    <option value="0.3">0.3</option>
                                    <option value="0.4">0.4</option>
                                    <option value="0.5">0.5</option>
                                </select>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <button class="layui-btn"  value="保存设置" onclick="edits(<?php echo htmlentities($list['id']); ?>)">保存设置
                </div>
            </div>
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
            url: "<?php echo url('index/charges/edits'); ?>",
            data: {
                id:id,
                overtime:$('#overtime').val(),
                check_out:$('#check_out').val(),
                exceed:$('#exceed').val(),
                leave:$('#leave').val(),
                additional:$('#additional').val(),
                exceed_plus:$('#exceed_plus').val(),
                leave_plus:$('#leave_plus').val(),
                additional_plus:$('#additional_plus').val()
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