<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
    <style>
        .list-img {
            padding: 2px!important;
        }
        .list-img img {
            max-width: 60px;
            max-height: 60px;
        }
    </style>
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product viewFramework-product-col-1">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        {include file="public/product-navbar-finance"}
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a href="{:Url('finance/skjilu')}" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-inline marginTop10">
                        <div class="col-lg-12">
                            <div class="sub-button-line marginTop10 form-inline">
                                <div class="pull-left">
                                    <div class="form-group">
                                        <label class="control-label" for="projectNameInput">搜索名称 :</label>
                                        <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="top" title="单号 / 客户名称">
                                        <button type="button" class="btn btn-primary" id="searchprojectName" style="margin-left: 0;">搜索</button>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="projectNameInput">日期搜索 :</label>
                                        <input type="text" class="form-control layui-input" name="testDate6" id="testDate6" placeholder=" 选择日期范围 " readonly><button type="button" class="btn btn-primary" id="searchDateNumber" style="margin-left: 4px;">搜索</button>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    {eq name="$Request.param.m" value=""}
                                    <a class="btn btn-primary" href="{:Url('finance/skjilu_excel')}" target="_blank">导出Excel</a> {else/}
                                    <a class="btn btn-primary" href="{:Url('finance/skjilu_excel',['m'=>$Request.param.m,'k'=>$Request.param.k])}" target="_blank">导出Excel</a>
                                    {/eq}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table">
                            <thead>
                            <tr>
                                <th width="80">图片</th>
                                <th>销售单号</th>
                                <th>客户名称</th>
                                <th>
                                    <div class="dropdown" id="DropdownSort">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>收款类目</span><span class="title"></span><span class="caret"></span></a>
                                        <ul class="dropdown-menu aliyun-console-table-search-list">
                                            <li>
                                                <a href="{:Url('finance/skjilu')}">
                                                    <span>全部</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('finance/skjilu',['m'=>'sort','k'=>'1'])}" id="sort_1">
                                                    <span>订单订金</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('finance/skjilu',['m'=>'sort','k'=>'2'])}" id="sort_2">
                                                    <span>订单尾款</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>收款金额</th>
                                <th>收款人</th>
                                <th>收款日期</th>
                                <th>收款内容</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo" empty="$empty"}
                            <tr>
                                <td class="list-img">{eq name="$vo.schedule.fs_img" value=""}<img src="/uploads/noimage.png">{else/}<img src="{$vo.schedule.fs_img}" class="test-popup-link" href="{$vo.schedule.fs_img}">{/eq}</td>
                                <td><a href="javascript:;" onclick="DialogModelPnumber('{$vo.fpnumber}')">{$vo.fpnumber}</a></td>
                                <td>{$vo.fcus_name}</td>
                                <td>{$vo.sort.sname}</td>
                                <td>￥{$vo.amount}</td>
                                <td>{$vo.fuid}</td>
                                <td>{$vo.shoukuan_time|date="Y-m-d",###}</td>
                                <td style="width: 300px!important;padding:0 2px;">{$vo.schedule.fs_remark}</td>
                            </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="8">
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
<script type="text/javascript" src="/assets/plugins/jquery-magnific-popup/jquery.magnific-popup.min.js" ></script>
<link rel="stylesheet" type="text/css" href="/assets/plugins/jquery-magnific-popup/magnific-popup.css">
<script type="text/javascript">
    window.item = '';
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-finance").addClass("sidebar-nav-active"); // 大分类
        $("#finance-receivables").addClass("active"); // 小分类
        $("#product-finance-skjilu").addClass("active"); // 小分类

        //图片放大
        $('.test-popup-link').magnificPopup({
            type: 'image'
            // other options
        });

        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        // 单号搜索
        $("#searchprojectName").on('click keyup', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            //var patrn = /^\+?[0-9]*$/;　　//判断是否为正整数 patrn.exec(NameInput) == null
            if (NameInput.length < '2' || NameInput.length > '16') {
                toastr.warning('请输2-16个字符');
                return false
            } else {
                window.location.href="{:Url('finance/skjilu',['m'=>'pnumber'])}?k="+NameInput;
            }
        });

        // 日期搜索
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //日期范围
            laydate.render({
                elem: '#testDate6'
                ,min: '2015-01-01'
                ,max: '2080-12-31'
                ,type: 'month'
                ,range: '~'
            });
        });
        $("#searchDateNumber").on('click keyup', function () {
            var NameInput = $("input[name='testDate6']").val();
            if (NameInput !== '') {
                //console.log(NameInput);
                window.location.href="{:Url('finance/skjilu',['m'=>'date'])}?k="+NameInput.replace(/[ ]/g,"");
            }
        });

        //查询条件显示
        var sName = '{$Request.param.m}';
        var kName = '{$Request.param.k}';
        if (sName!== '') {
            //客户确认搜索显示 DropdownAffirm
            if (sName == 'sort') {
                var id='DropdownSort';
                listDropdownSearch({i:id,k:kName,s:sName});
            }
        }
    });

    //查询单号详情
    function DialogModelPnumber(e) {
        //'m'='pnumber','k'=e
        if (!isNaN(e) && e !== null && e !== '') {
            layui.use(['layer', 'form'], function(){
                var layer = layui.layer;
                layer.open({
                    offset: '80px', //顶部距离
                    type: 2, //窗口模式
                    title: '收款详情' ,//不显示标题栏
                    area: ['80%', '550px'],
                    content: '{:Url("finance/dialog_pnumber")}?m=pnumber&k='+e,
                    btn: ['关闭'],
                    btn1: function(index, layero){
                        layer.close(index);
                    }
                })
            })
            //console.log(e);
        }
    }
</script>
</body>
</html>