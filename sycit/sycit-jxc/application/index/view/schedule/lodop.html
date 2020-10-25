<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <!--头部文件-->
    <meta charset="UTF-8">
    <title>{$title}-{:Config('syc_webname')}</title>
    <meta name="author" content="www.sycit.cn, hyzwd@outlook.com"/>
    <link href="/favicon.ico" type="image/x-icon" rel="icon"/>
    <link href="/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/console1412.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/sycit.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/assets/plugins/jquery-2.2.4.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
    <!--AJAX插件-->
    <script type="text/javascript" src="/assets/plugins/jquery.form.js" ></script>
    <!--弹窗插件-->
    <script type="text/javascript" src="/assets/plugins/toastr.js" ></script>
    <!-- bootstrap3-dialog -->
    <script type="text/javascript" src="/assets/plugins/b.dialog.js" ></script>
    <!-- layui -->
    <link rel="stylesheet" type="text/css" href="/assets/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/assets/plugins/layui/layui.js"></script>

    <!--打印-->
    <script type="text/javascript" src="/assets/plugins/print/jQuery.print.js" ></script>
    <style>
        .table {
            margin-top:0px;
            margin-bottom:0px;
        }
        .table.order-lodop tr td {
            /*width: 40px;*/
            height: 69px;
            font-size: 22px;
            font-weight: bold;
        }
        input.lodop {
            font-weight: bold;
        }
        .table.order-lodop .cs1 {
            /*width: 100px;*/
            font-size: 30px;
        }
        .table.order-lodop .cs3 {
            /*width: 50px;*/
            font-size: 44px;
        }
        .table.order-lodop tr td.nb-1 {
            border-top: 1px solid rgba(0, 0, 0, 0);
            border-left: 1px solid rgba(0, 0, 0, 0);
            border-right: 1px solid rgba(0, 0, 0, 0);
        }
        .table.order-lodop tr td.nb-2 {
            border: 1px solid rgba(0, 0, 0, 0);
        }
    </style>
</head>
<body>
<div class="console-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="console-title console-title-border clearfix">
                <div class="col-md-4 pull-left order-title">
                    <h3><span>{$title}</span></h3>
                    <span class="text-explode">|</span>
                    <span>{$data.pnumber}</span>
                </div>
                <div class="col-md-5 text-left order-title">
                </div>
                <div class="col-md-3 text-right">
                    <a href="{:Url('schedule/lodop_excel',['pid'=>$data.pnumber])}" target="_blank"  class="btn btn-primary">导出标签</a>
                    <a href="javascript:window.close();" class="btn btn-default"><span>关闭窗口</span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3" style="margin-bottom:20px;width: 700px;">
            {volist name="list" id="vo" empty="$empty"}
            <div style="padding: 15px 0 3px 0;">
                <a class="btn btn-primary" onclick="PrintMytable('{$i}');">打印标签 {$count}-{$i}</a>
            </div>
            <div id="orderList-{$i}">
                <div style="height: 8px;"></div>
                <table class="table order-lodop">
                    <tbody>
                    <tr>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 55px;height: 0;" class="nb-1"></td>
                        <td style="width: 12px;height: 0;border-bottom: 1px solid rgba(0, 0, 0, 0);" class="nb-1"></td>
                    </tr>
                    <tr>
                        <td>客户<br>名称</td>
                        <td colspan="3" class="cs1" style="font-weight: bold;">{$data.pcsname}</td>
                        <td>包装<br>编号</td>
                        <td colspan="2" class="cs2"><input type="text" class="lodop" style="font-size: 22px;" value="{$count}-{$i}"></td>
                        <td class="nb-2"></td>
                    </tr>
                    <tr>
                        <td>收货<br>地址</td>
                        <td colspan="2" class="cs3"><input type="text" class="lodop" value="{:mb_substr($data.pcus_id.cus_prov,0,2,'utf-8')}"></td>
                        <td colspan="2" class="cs3"><input type="text" class="lodop" value="{:mb_substr($data.pcus_id.cus_city,0,2,'utf-8')}"></td>
                        <td colspan="2" class="cs3"><input type="text" class="lodop" value="{:mb_substr($data.pcus_id.cus_dist,0,2,'utf-8')}"></td>
                        <td class="nb-2"></td>
                    </tr>
                    <tr>
                        <td>产品<br>信息</td>
                        <td colspan="2" style="height: 69px;font-size: 16px;">订单号：<br>{$data.pnumber}</td>
                        <td colspan="4" style="height: 69px;font-size: 16px;">{$vo.yanse}&nbsp;&nbsp;&nbsp;{$vo.suoxiang}<BR>{$vo.products}{$vo.chanph}&nbsp;&nbsp;{$vo.breadth}*{$vo.heiget}*{$vo.thick}</td>
                        <td class="nb-2"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            {/volist}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-inline" style="padding:0 15px;">
                <div class="alert alert-tip"><b>打印说明：</b>
                    <p></p>
                    <p>需要打印最好使用Charome(谷歌浏览器)、或360浏览器。</p>
                    <p>因为打印是调用系统接口，没有权限，所以默认打印会显示页眉和页脚。</p>
                    <p>如需要去除页眉和页脚，可以在浏览打印时候，点击【+更多设置】，取消勾选【页眉和页脚】。</p>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="handle_status" value="">
<!--返回顶部-->
<a href="javascript:;" title="返回顶部" class="syc-top" id="syc-view-top">Top</a>
<!--全局JS-->
<script type="text/javascript" src="/assets/admin/scripts/sycit.js" ></script>
<!--订单JS-->
<script type="text/javascript" src="/assets/admin/scripts/syc-order.js"></script>
<script type="text/javascript">
    $(function () {
        //

    });
    //打印设置
    function PrintMytable(e) {
        $("#orderList-"+e).print({mediaPrint : false,});
    };
</script>
</body>
</html>