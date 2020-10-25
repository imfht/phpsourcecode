<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-mini">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product viewFramework-product-col-1">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        {include file="public/storage-nav"}
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
                        <table class="table syc-table border">
                            <thead>
                            <tr>
                                <th width="85" rowspan="2">图形</th>
                                <th width="60" rowspan="2">名称</th>
                                <th width="80" rowspan="2">品牌</th>
                                <th width="60" rowspan="2">库存数量</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="$list" id="vo" empty="$empty"}
                            <tr>
                                <td class="st-list-img">
                                    {eq name="$vo.st_lid.limg" value=""}
                                    <img src="/uploads/noimage.png">
                                    {else/}
                                    <img src="{$vo.st_lid.limg}">
                                    {/eq}
                                </td>
                                <td>{$vo.st_lid.lname}</td>
                                <td>{$vo.st_lid.laddress}</td>
                                <td>
                                    {if condition="$vo.st_quantity gt 20"}
                                    <b style="color: #5cb85c;">{$vo.st_quantity}</b>
                                    {else/}
                                    {between name="$vo.st_quantity" value="11,20"}
                                    <b style="color: #fca103;">{$vo.st_quantity}</b>
                                    {else/}
                                    <b style="color: #fc0303;">{$vo.st_quantity}</b>
                                    {/between}
                                    {/if}
                                </td>
                            </tr>
                            {/volist}

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4">
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
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-storage").addClass("sidebar-nav-active"); // 大分类
        $("#storage-index").addClass("active"); // 小分类
        $("#storage-nav-peijiansum").addClass("active"); // 小分类

        //查询条件显示
        var sName = '{$Request.param.q}';
        if (sName == '') {
            $("#listname").find("li:first-child").addClass('active');
        } else {
            //pills-item-
            $("#pills-item-"+sName).addClass('active');
        }
    })
</script>
<script>
    $(function(){
        //
        $('td[id^="quantity-"]').dblclick(function(){
            var $text = $(this).text();
            if(!$(this).is('.input')){
                $(this).addClass('input').html('<input type="text" value="'+ $text +'" />').find('input').focus().blur(
                    function(){
                        var thisvalue=$(this).val();
                        $(this).keydowns();
                        if ($text !== thisvalue) {
                            $.ajax({
                                type: 'POST',
                                url: 'update.php',
                                data: "thisvalue="+thisvalue
                            });
                        }

                        $(this).parent().removeClass('input').html("<span class=\"label label-sm status-5\">"+$(this).val() || 0+"</span>");
                    }
                );
            }
        }).hover(function(){
            $(this).addClass('hover');
        },function(){
            $(this).removeClass('hover');
        });
    });

    //修改数量
    function editQuantity(e) {

    }
</script>

</body>
</html>