<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
    <style>
        label{font-weight:normal}
    </style>
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{:Url('config/produce_add')}">新增设定</a>
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="marginTop10"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-tip">
                            <b>说明：</b>
                            <p></p>
                            <p>关联料型是以所关联的铝材名称做相对数量的减除库存，此操作在【生产订单】中【开始生产】开始执行。</p>
                        </div>
                        <table class="table syc-table border">
                            <thead>
                            <tr>
                                <th width="80">产品系列</th>
                                <th width="80">产品编号</th>
                                <th colspan="2">包边线设置</th>
                                <th>关联料型</th>
                                <th>添加员</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="$list" id="vo" empty="$empty"}
                            <tr>
                                <td>{$vo.ms_pnid}</td>
                                <td>{$vo.ms_blname}</td>
                                <td width="60">{$vo.ms_maname}</td>
                                <td width="60">{$vo.ms_baobian}</td>
                                <td>
                                    <div class="input-group">
                                    <div class="icheck-inline left">
                                    {volist name="$vo.son" id="son" empty="没有关联"}
                                        <label style="margin-right:0;">
                                            <div class="icheckbox_square-orange checked" style="position: relative;">
                                                <ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins>
                                            </div>
                                            {$son.on}
                                            <span>【数量 {$son.val}】</span>
                                        </label>
                                    {/volist}
                                    </div>
                                    </div>
                                </td>
                                <td width="80">{$vo.ms_uid}</td>
                                <td width="100">
                                    <a href="{:Url('config/produce_edit',['pid'=>$vo.msid])}">修改</a>
                                    <span class="text-explode">|</span>
                                    <a href="javascript:void(0);" onclick="deleteOne('{$vo.msid}');">删除</a>
                                </td>
                            </tr>
                            {/volist}

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7">
                                    <div class="pull-right page-box">{$page}</div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!--内容结束-->
            </div>
        </div>
    </div>
</div>

{// 引入底部公共JS文件}
{include file="public/footer"}
<script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
<link href="/assets/plugins/fileinput/fileinput.css" rel="stylesheet" type="text/css" />
<script src="/assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
<!--icheck-->
<link href="/assets/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-config").addClass("sidebar-nav-active"); // 大分类
        $("#config-shengchan").addClass("active"); // 小分类
    });
    //单条删除操作
    function deleteOne(e) {
        if (!isNaN(e) && e !== null && e !== '') {
            layui.use(['layer', 'form'], function(){
                var layer = layui.layer;
                layer.open({
                    offset: '150px',
                    type: 1, //窗口模式
                    title: false ,//不显示标题栏
                    area: '300px;',
                    closeBtn: false,
                    shade: 0.8, //遮罩层深度
                    content: '<div class="layui-msg">请确认是否执行操作，一旦删除，操作【生产订单】时候不会扣除相应关联库存。</div>',
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['确认', '取消'],
                    yes: function(index, layero) {
                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                        var data={name:'delone',pid:e};
                        $.sycToAjax("{:Url('config/bancai_delete')}", data);
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                })
            })
        }
    }
</script>
</body>
</html>