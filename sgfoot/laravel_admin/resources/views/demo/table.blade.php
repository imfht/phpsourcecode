@extends('layout')
@section('title', $title)
@section('style')

@stop
@section('body')
    <blockquote class="layui-elem-quote">{{$title}}</blockquote>
    <div class="layui-form-item">
        <a class="layui-btn ml5" layTips="点击添加一款新的产品|3|#3595CC" href=""><i
                    class="fa fa-plus-circle fa-fw"></i>添加产品</a>
    </div>
    <div class="layui-collapse">
        <div class="layui-colla-item ">
            <h2 class="layui-colla-title">帮助</h2>
            <div class="layui-colla-content layui-show">
                <ul>
                    <li><span class="layui-badge layui-bg-green mr5">1</span>管理产品,可以编辑,可以添加,可以删除,谨慎操作</li>
                    <li><span class="layui-badge layui-bg-green mr5">2</span>带有<i class="layui-icon">&#xe642;</i>标记,代表可以直接编辑
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <table id="table-list" lay-filter="tableLay"></table>
    @verbatim
        <script type="text/html" id="sexTpl">
            <input type="checkbox" name="" value="{{d.id}}" lay-skin="switch" lay-text="男|女"
                   lay-filter="sexSwitch" {{d.sex== 1 ? 'checked' : '' }}>
        </script>
        <script type="text/html" id="barTable">
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        </script>
    @endverbatim
@stop
@section('script')
    <script type="text/javascript">

        layui.use(['form', 'layer', 'table', 'element'], function () {
            // 操作对象
            var form = layui.form
                , layer = layui.layer
                , $ = layui.jquery
                , table = layui.table
                , element = layui.element;

            $(document).keyup(function (event) {
                if (event.keyCode === 13) {
                    $("button").trigger("click");
                }
            });
            // 验证
            form.verify({
                username: function (value) {
                    if (value === "") {
                        return "请输入用户名";
                    }
                },
                password: function (value) {
                    if (value === "") {
                        return "请输入密码";
                    }
                },
                captcha: function (value) {
                    if (value === "") {
                        return "请输入验证码";
                    }
                }
            });
            // 提交监听
            form.on('submit(login)', function (data) {
                $.post('{{route('login')}}', data.field, function (rev) {
                    if (rev.status === 0) {
                        window.location.href = rev.data;
                    } else {
                        $("input[name='captcha']").select().focus();
                        layer.msg(rev.msg, {shift: 1, icon: 2});
                        $("#captcha").attr("src", '{{captcha_src()}}?' + Math.random());//刷新验证码 的
                    }
                });
                return false;
            });
            load_table();
            function load_table(where) {
                if (typeof where === 'undefined') {
                    where = {};
                }
                //表格加载数据
                table.render({
                    elem: '#table-list'
                    , height: 'auto'
                    , method: 'post'
                    , url: "{{route('list')}}" //数据接口
                    , where: where
                    , page: true //开启分页
                    , size: ''
                    , cellMinWidth: 60
                    , limit: 25
                    , limits: ['25', '50', '100']
                    , cols: [[ //表头
                        {field: 'sid', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: '姓名', width: 90, align: 'center'}
                        , {field: 'company', title: '所属公司', width: 90, align: 'center'}
                        , {
                            field: 'school',
                            title: '<i class="layui-icon">&#xe642;</i>母校',
                            width: 150,
                            sort: true,
                            edit: 'text'
                        }
                        , {field: 'sex', title: '性别', width: 90, height: 200, templet: '#sexTpl'}
                        , {field: 'update_format', title: '修改时间', sort: true, width: 120}
                        , {title: '操作', templet: '#barTable', width: 220}
                    ]]
                });
            }

            //监听切换操作
            form.on('switch(sexSwitch)', function (obj) {
                var pp = {};
                pp.id = this.value;
                pp.value = obj.elem.checked ? 1 : 0;
                pp.field = 'sex';
                layer.msg(JSON.stringify(pp));
            });
            //单元格编辑
            table.on('edit(tableLay)', function (obj) {
                var value = parseInt(obj.value) //得到修改后的值
                    , data = obj.data //得到所在行所有键值
                    , field = obj.field; //得到字段
                layer.msg(JSON.stringify(obj));
            });
            //扩展操作
            table.on('tool(tableLay)', function (obj) {
                var data = obj.data;
                console.log(data);
                if (obj.event === 'del') {
                    var tips = '真的删除行么';
                    layer.confirm(tips, {icon: 2}, function (index) {
                        $.post("{{route('del')}}", {id: data.id}, function (rev) {
                            if (rev.status === 0) {
                                obj.del();
                            }
                            layer.msg(rev.msg);
                        });
                        layer.close(index);
                    });
                }
            });

        });
    </script>
@stop