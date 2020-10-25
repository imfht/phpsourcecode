<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <title>笔友 | Be yourself</title>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/admin.js"></script>
    <link type="text/css" rel="stylesheet" href="{{ asset('/css/themes/bootstrap/easyui.css') }}"/>
    <link type="text/css" rel="stylesheet" href="{{ asset('/css/themes/icon.css') }}"/>
    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}

    <script type="text/javascript">
       /* 添加用户*/
        var toolbar = [{
            text:'添加',
            iconCls:'icon-add',
            handler:function(){
                var html="<div>用户名：<input type='text' name='name' style='margin-right: 1em;'>" +
                        "密码：<input type='password' name='password' style='margin-right: 1em;'>" +
                        "电子邮箱：<input type='email' name='email' style='margin-right: 1em;'/>" +
                        "<input type='hidden' name='_token' value='{{ csrf_token() }}'/></div>";
                var d = dialog({
                    title: '添加用户',
                    content: html,
                    okValue: '保存',
                    ok: function () {
                        this.title('提交中…');
                        $.ajax({
                            type: "get",
                            url: "/admin/userManage/create" ,
                            data: {
                                'name': $('input[name=name]').val(),
                                'password':$('input[name=password]').val(),
                                'email':$('input[name=email]').val()
                            },
                            success: function (data) {
                                var d = dialog({
                                    title: '提示',
                                    content: data.info,
                                    width: 220
                                });
                                d.show();
                                setTimeout(function () {
                                    d.close().remove();
                                }, 3000);
                                window.location.reload();
                                return true;
                            }
                        });
                    },
                    cancelValue: '取消',
                    cancel: function () {}
                });
                d.show();
            }
        }];
    </script>
</head>
<body>
<table id="dg" class="easyui-datagrid" title="用户列表" style="width:100%;height:250px"
       data-options="rownumbers:true,singleSelect:false,toolbar:toolbar">
    <thead>
    <tr>
        <th data-options="field:'userName',width:100">用户名</th>
        <th data-options="field:'email',width:150">电子邮箱</th>
        <th data-options="field:'created_at',width:150">创建时间</th>
        <th data-options="field:'updated_at',width:150">更新时间</th>
        <th data-options="field:'edit',width:150">操作</th>
    </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at }}</td>
                <td>{{ $user->updated_at }}</td>
                <td>
                    <input type="hidden" name="userId" value="{{ $user->id }}"/>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <a id="edit" href="#">编辑修改</a>
                    <a style="margin-left: 1em;" id="delete" href="#">删除</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
<div style="display: none">
    <span>Selection Mode: </span>
    <select onchange="$('#dg').datagrid({singleSelect:(this.value==0)})">
        <option value="0">Single Row</option>
        <option value="1">Multiple Rows</option>
    </select><br/>
    SelectOnCheck: <input type="checkbox" checked onchange="$('#dg').datagrid({selectOnCheck:$(this).is(':checked')})"><br/>
    CheckOnSelect: <input type="checkbox" checked onchange="$('#dg').datagrid({checkOnSelect:$(this).is(':checked')})">
</div>

</body>
</html>