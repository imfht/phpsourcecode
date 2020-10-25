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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{:Url('customers/add')}">新增客户</a>
                                <a href="{:Url('customers/index')}" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="sub-button-line marginTop10 form-inline">
                            <form class="pull-left">
                                <div class="form-group">
                                    <label class="control-label" for="projectNameInput">搜索名称 :</label>
                                    <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="top" title="可搜索 公司名称 / 客户名称">
                                    <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                                </div>
                            </form>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{:Url('customers/excel')}" target="_blank">导出Excel</a>
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
                                    <th>编号</th>
                                    <th>公司名称</th>
                                    <th>客户名称</th>
                                    <th>手机</th>
                                    <th>固话</th>
                                    <th>传真</th>
                                    <th>详细地址</th>
                                    <th>发货物流</th>
                                    <th class="text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            {volist name="data" id="vo" empty="$empty"}
                                <tr>
                                    <td><input type="checkbox" name="ckbox[]" value="{$vo.cus_id}"></td>
                                    <td>{$vo.cus_id}</td>
                                    <td><a href="{:Url('customers/view',['id'=>$vo.cus_id])}">{$vo.cus_name}</a></td>
                                    <td>{$vo.cus_duty}</td>
                                    <td>{$vo.cus_moble}</td>
                                    <td>{$vo.cus_phome}</td>
                                    <td>{$vo.cus_fax}</td>
                                    <td>{$vo.cus_prov} {$vo.cus_city} {$vo.cus_dist} {$vo.cus_street}</td>
                                    <td>{$vo.cus_log_id.log_name}</td>
                                    <td class="text-right">
                                        <a href="{:Url('customers/view',['id'=>$vo.cus_id])}">查看</a>
                                        <span class="text-explode">|</span>
                                        <a href="{:Url('customers/edit',['id'=>$vo.cus_id])}">修改</a>
                                        <span class="text-explode">|</span>
                                        <a href="javascript:void(0);" onclick="deleteLogisticsOne('{$vo.cus_id}');">删除</a>
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
                                    </div>
                                    <div class="pull-right page-box">{$page}</div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

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
        $("#sidebar-customers").addClass("active"); // 小分类

        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('customers/index')}?q="+NameInput;
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
                    $.sycToAjax("{:Url('customers/delete')}", data);
                };
            };
            return false;
        });
    });
    // 单条删除操作
    function deleteLogisticsOne(e) {
        if(confirm("是否删除？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'delone',uid:e};
                $.sycToAjax("{:Url('customers/delete')}", data);
            }
        };
        return false;
    }
</script>
</body>
</html>