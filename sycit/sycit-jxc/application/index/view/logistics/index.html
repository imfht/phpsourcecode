<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
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
                                <a class="btn btn-primary" id="AddLogisticsModal">新增物流信息</a>
                                <a href="{:Url('logistics/index')}" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sub-button-line marginTop10">
                            <form class="pull-left marginBottom10 form-inline">
                                <div class="form-group">
                                    <label class="control-label" for="projectNameInput">物流名称 :</label>
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
                                <th>&nbsp;</th>
                                <th>编号</th>
                                <th>名称</th>
                                <th>电话</th>
                                <th>传真</th>
                                <th>地址</th>
                                <th class="text-right">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="data" id="vo" empty="$empty"}
                            <tr>
                                <td><input type="checkbox" name="ckbox[]" value="{$vo.log_id}"></td>
                                <td>{$vo.log_id}</td>
                                <td>{$vo.log_name}</td>
                                <td>{$vo.log_phone}</td>
                                <td>{$vo.log_fax}</td>
                                <td>{$vo.log_address}</td>
                                <td class="text-right">
                                    <a href="{:Url('logistics/edit',['id'=>$vo.log_id])}">修改</a>
                                    <span class="text-explode">|</span>
                                    <a href="javascript:void(0);" onclick="deleteLogisticsOne({$vo.log_id});">删除</a>
                                </td>
                            </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td width="10">
                                    <input type="checkbox" class="mydomain-checkbox" id="ckSelectAll" name="ckSelectAll">
                                </td>
                                <td colspan="6">
                                    <div class="pull-left">
                                        <button id="DelAllAttr" type="button" class="btn btn-default">选中删除</button>
                                    </div>
                                    <div class="pull-right page-box">
                                        {$page}
                                    </div>
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
        $("#sidebar-sales").addClass("sidebar-nav-active"); // 大分类
        $("#logis-index").addClass("active"); // 小分类

        //新增物流无信息
        $("#AddLogisticsModal").click(function () {
           //
            bDialog.open({
                title : '新增物流',
                width: '800',
                height: '320',
                url : '{:Url(\'logistics/add\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        //window.location.href = data.results[0].url;
                        window.location.reload();
                    }
                }
            });
        });

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('logistics/index')}?q="+NameInput;
            }
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
                    var data={name:'delallattr',uid:valArr.join(',')};
                    $.sycToAjax("{:Url('logistics/delete')}", data);
                };
            };
            return false;
        });

    })
    // 单条删除操作
    function deleteLogisticsOne(e) {
        if(confirm("是否删除？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'delone',uid:e};
                $.sycToAjax("{:Url('logistics/delete')}", data);
            }
        };
        return false;
    }
</script>
</body>
</html>