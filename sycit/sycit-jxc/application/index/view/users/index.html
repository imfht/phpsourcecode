<!DOCTYPE html>
<html lang="zh-CN">
<head>
{include file="public/header"}
    <style>
        body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;overflow: hidden;}
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
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" id="AddUserModal">新增账户</a>
                                <a href="{:Url('users/index')}" class="btn btn-default">
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
                                    <label class="control-label" for="projectNameInput">账户名称 :</label>
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
                                <th>姓名</th>
                                <th>性别</th>
                                <th>部门<span class="caret"></span></th>
                                <th>入职时间</th>
                                <th>登录次数</th>
                                <th>
                                    <div class="dropdown" id="DropdownStasus">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>账户状态</span><span class="title"></span><span class="caret"></span></a>
                                        <ul class="dropdown-menu aliyun-console-table-search-list">
                                            <li>
                                                <a href="{:Url('users/index')}"><span>全部</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('users/index',['m'=>'status','k'=>'1'])}" id="status_1">
                                                    <span>正常</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('users/index',['m'=>'status','k'=>'2'])}" id="status_2">
                                                    <span>审核</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('users/index',['m'=>'status','k'=>'0'])}" id="status_0">
                                                    <span>禁用</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('users/index',['m'=>'status','k'=>'-1'])}" id="status_-1">
                                                    <span>删除</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th class="text-right">操作</th>
                            </tr>
                            <tbody>
                            {volist name="data" id="vo" empty="$empty"}
                            <tr>
                                <td><input type="checkbox" name="ckbox[]" value="{$vo.id}"></td>
                                <td>{$vo.id}</td>
                                <td>{$vo.user_nick}</td>
                                <td>{switch $vo.user_sex}{case 1}男{/case}{case 2}女{/case}{default}未知{/switch}</td>
                                <td>{$vo.user_auth.title}</td>
                                <td>{$vo.entry_time}</td>
                                <td><span class="label label-sm label-warning">{$vo.user_count} 次</span></td>
                                <td>{$vo.status}</td>
                                <td class="text-right">
                                    <a href="{:Url('users/edit',['uid'=>$vo.id])}">修改</a>
                                    <span class="text-explode">|</span>
                                    <a href="javascript:void(0);" onclick="deleteUserOne({$vo.id});">删除</a>
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
                                        <button id="transferID" type="button" class="btn btn-default">转至其他账号
                                        </button>
                                    </div>
                                    <div class="pull-right page-box">
                                        {$page}
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-tip">
                            <b>账户删除说明：</b>
                            <p></p>
                            <p>此删除并非真正删除账户，可从【账户状态】中选择【删除】查看删除账户，也可恢复删除账户</p>
                        </div>
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
        $("#sidebar-config").addClass("sidebar-nav-active"); // 大分类
        $("#config-users").addClass("active"); // 小分类

        // 账户状态搜索显示
        var sName = '{$Request.param.m}';
        var kName = '{$Request.param.k}';
        if (sName!== '') {
            if (sName == 'status') {
                //状况搜索显示 DropdownStasus
                var id='DropdownStasus';
                listDropdownSearch({i:id,k:kName,s:sName});
                //console.log(sName)
            }
        }

        // 使用prop实现全选和反选
        $("#ckSelectAll").on('click', function () {
            $("input[name='ckbox[]']").prop("checked", $(this).prop("checked"));
        });
        
        // 获取选中元素
        $("#DelAllAttr").on('click', function () {
            if(confirm("是否删除所选？")){
                // 获取所有选中的项并把选中项的文本组成一个字符串
                var valArr = new Array;
                $("input[name='ckbox[]']:checked").each(function(i){
                    valArr[i] = $(this).val();
                });
                if (valArr.length !== 0 && valArr !== null && valArr !== '') {
                    var data={name:'delallattr',uid:valArr.join(',')};
                    $.sycToAjax("{:Url('users/delete')}", data);
                };
            };
            return false;
        });

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('users/index',['m'=>'nick'])}?k="+NameInput;
            }
        });

        // 添加账户Model
        $("#AddUserModal").on('click', function () {
            bDialog.open({
                title : '新增账户',
                url : '{:Url(\'users/add\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        console.log();
                        window.location.href = data.results[0].url;
                    }
                }
            });
        })
    });
    // 单条删除操作
    function deleteUserOne(e) {
        if(confirm("是否删除？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'delone',uid:e};
                $.sycToAjax("{:Url('users/delete')}", data);
            }
        };
        return false;
    }
</script>
</div>
</body>
</html>