<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
    <style>
        .container{margin-left: 30px; margin-top: 20px;}
        h1{padding-bottom: 10px; color: darkmagenta; font-weight: bolder;}
        img{cursor: pointer;}
        #pic{position: absolute; display: none;float:right}
        #pic1{ max-width: 300px; max-height:300px;border-radius: 5px; -webkit-box-shadow: 5px 5px 5px 5px hsla(0,0%,5%,1.00); box-shadow: 5px 5px 5px 0px hsla(0,0%,5%,0.3); }
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
                <div class="row syc-bg-fff">
                    <div class="col-lg-12 syc-border-bs">
                        <div class="console-title">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" id="AddProductNumberModal">新增锁具</a>
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>ID</th>
                                <th class="text-center" width="40">图片</th>
                                <th>名称</th>
                                <th>定价</th>
                                <th>品牌</th>
                                <th>添加员</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th class="text-right">操作</th>
                            </tr>
                            </thead>
                            <tbody class="container">
                            {volist name="list" id="vo" empty="$empty"}
                            <tr>
                                <td><input type="checkbox" name="ckbox[]" value="{$vo.lid}"></td>
                                <td>{$vo.lid}</td>
                                <td class="text-center img"><img src='{eq name="$vo.limg" value=""}/uploads/noimage.png{else/}{$vo.limg}{/eq}' width="35"></td>
                                <td>{$vo.lname}</td>
                                <td>￥{$vo.lprice}</td>
                                <td>{$vo.laddress}</td>
                                <td>{$vo.luser_nick.user_nick}</td>
                                <td>{$vo.update_time}</td>
                                <td>{$vo.status}</td>
                                <td class="text-right">
                                    <a href="{:Url('product/suoju_edit',['pid'=>$vo.lid])}">修改</a>
                                    <span class="text-explode">|</span>
                                    <a href="javascript:void(0);" onclick="deleteProductNumberOne('{$vo.lid}');">删除</a>
                                </td>
                            </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td width="10">
                                    <input type="checkbox" class="mydomain-checkbox" id="ckSelectAll" name="ckSelectAll">
                                </td>
                                <td colspan="9">
                                    <div class="pull-left">
                                        <button id="DelAllAttr" type="button" class="btn btn-default">选中删除</button>
                                        </button>
                                    </div>
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
        $("#sidebar-product").addClass("sidebar-nav-active"); // 大分类
        $("#product-lock").addClass("active"); // 小分类

        //新增产品编号动作
        $("#AddProductNumberModal").click(function () {
            var title = $(this).text();
            bDialog.open({
                title : title,
                height: '520',
                url : '{:Url(\'product/suoju_add\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        //console.log();
                        window.location.reload();
                    }
                }
            });
        });

        // 使用prop实现全选和反选
        $("#ckSelectAll").on('click', function () {
            $("input[name='ckbox[]']").prop("checked", $(this).prop("checked"));
        });

        // 获取选中元素删除操作
        $("#DelAllAttr").on('click', function () {
            if(confirm("是否删除所选？")){
                // 获取所有选中的项并把选中项的文本组成一个字符串
                var valArr = new Array;
                $("input[name='ckbox[]']:checked").each(function(i){
                    valArr[i] = $(this).val();
                });
                if (valArr.length !== 0 && valArr !== null && valArr !== '') {
                    var data={name:'delallattr',pid:valArr.join(',')};
                    $.sycToAjax("{:Url('product/suoju_delete')}", data);
                };
            };
            return false;
        });
    });
    //单条删除操作
    function deleteProductNumberOne(e) {
        if(confirm("是否删除？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'delone',pid:e};
                $.sycToAjax("{:Url('product/suoju_delete')}", data);
            }
        };
        return false;
    }
</script>
<script>
    $(function(){
        //鼠标经过放大图片
        $(".container .img").hover(function(){
            var src = $(this).find('img').attr('src'); //获取图片
            $(this).append("<p id='pic'><img src='"+src+"' id='pic1'></p>");
            $(window).mousemove(function(e){
                $("#pic").css({
                    //"top":(e.pageY)+"px",
                    "left": "13%",
                }).fadeIn("fast");
                //$("#pic").fadeIn("fast");
                //console.log(e.pageY);
            });
        },function(){
            $("#pic").remove();
        });
    });
</script>
</body>
</html>