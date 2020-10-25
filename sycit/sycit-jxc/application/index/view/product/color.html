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
                                <a class="btn btn-primary" id="AddProductColorModal">新增产品颜色</a>
                                <a href="{:Url('product/color')}" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sub-button-line marginTop10">
                            <form class="marginBottom10 form-inline">
                                <div class="form-group">
                                    <label class="control-label" for="projectNameInput">颜色名称 :</label>
                                    <input name="projectNameInput" id="projectNameInput" class="ipt form-control">
                                    <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="35">ID</th>
                                <th class="text-center" width="40">图片</th>
                                <th>名称</th>
                                <th>添加员</th>
                                <th>产地</th>
                                <th>简述</th>
                                <th>状态</th>
                                <th>更新时间</th>
                                <th class="text-right">操作</th>
                            </tr>
                            </thead>
                            <tbody class="container">
                            {volist name="list" id="vo" empty="$empty"}
                            <tr>
                                <td>{$vo.pc_id}</td>
                                <td class="text-center img"><img src='{eq name="$vo.pc_img" value=""}/uploads/noimage.png{else/}{$vo.pc_img}{/eq}' width="35"></td>
                                <td>{$vo.pc_name}</td>
                                <td>{$vo.pc_user_nick.user_nick}</td>
                                <td>{$vo.pc_address}</td>
                                <td>{$vo.pc_description}</td>
                                <td>{$vo.status}</td>
                                <td>{$vo.update_time}</td>
                                <td class="text-right">
                                    <a href="{:Url('product/color_edit',['pid'=>$vo.pc_id])}">修改</a>
                                    <span class="text-explode">|</span>
                                    <a href="javascript:void(0);" onclick="deleteProductColorOne('{$vo.pc_id}');">删除</a>
                                </td>
                            </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="9">
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
        $("#product-color").addClass("active"); // 小分类

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('product/color')}?q="+NameInput;
            }
        });
        
        //新增产品颜色动作
        $("#AddProductColorModal").click(function () {
            var title = $(this).text();
            bDialog.open({
                title : title,
                height: '520',
                url : '{:Url(\'product/color_add\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        //console.log();
                        window.location.reload();
                    }
                }
            });
        });

    });
    //单条删除操作
    function deleteProductColorOne(e) {
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
                    content: '<div class="layui-msg">请确认是否执行操作，一旦删除，也将现有的关联库存数据一起删除。</div>',
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['确认', '取消'],
                    yes: function(index, layero) {
                        layer.close(index); //如果设定了yes回调，需进行手工关闭
                        var data={name:'delone',pid:e};
                        $.sycToAjax("{:Url('product/color_delete')}", data);
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                })
            })
        }
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