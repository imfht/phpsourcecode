/**
 * 人员管理
 * @author 牧羊人
 * @since 2020/7/11
 */
layui.use(['function', 'laydate', 'admin', 'zTree'], function () {
    var func = layui.function,
        admin = layui.admin,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', fixed: 'left', unresize: true, sort: true}
            , {field: 'avatar_url', width: 60, title: '头像', align: 'center', templet: function (d) {
                    var avatarStr = "";
                    if (d.avatar_url) {
                        avatarStr = '<a href="' + d.avatar_url + '" target="_blank"><img src="' + d.avatar_url + '" height="26" /></a>';
                    }
                    return avatarStr;
                }
            }
            , {field: 'realname', width: 100, title: '真实姓名', align: 'center'}
            , {field: 'gender_name', width: 60, title: '性别', align: 'center'}
            , {field: 'level_name', width: 120, title: '职级', align: 'center'}
            , {field: 'position_name', width: 120, title: '岗位', align: 'center'}
            , {field: 'mobile', width: 130, title: '手机号码', align: 'center'}
            , {field: 'email', width: 180, title: '邮箱', align: 'center',}
            , {field: 'is_admin', width: 80, title: '管理员', align: 'center', templet: function (d) {
                    var str = "";
                    if (d.is_admin == 1) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
                    } else {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">否</span>';
                    }
                    return str;
                }
            }
            , {field: 'status', width: 80, title: '状态', align: 'center', templet: '#statusTpl'}
            , {field: 'login_num', width: 100, title: '登录次数', align: 'center'}
            , {fixed: 'right', width: 350, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList", function (layEvent, data) {
            if (layEvent === 'permission') {

                admin.open({
                    title: '角色权限分配',
                    btn: ['保存', '取消'],
                    content: '<ul id="roleAuthTree" class="ztree"></ul>',
                    success: function (layero, dIndex) {
                        var loadIndex = layer.load(2);
                        $.get('/adminrom/index', {type: 2, typeId: data.id}, function (res) {
                            layer.close(loadIndex);
                            if (res.success) {
                                $.fn.zTree.init($('#roleAuthTree'), {
                                    check: {enable: true},
                                    data: {simpleData: {enable: true}}
                                }, res.data);
                            } else {
                                layer.msg(res.msg, {icon: 2});
                            }
                        }, 'json');
                        // 超出一定高度滚动
                        $(layero).children('.layui-layer-content').css({'max-height': '300px', 'overflow': 'auto'});
                    },
                    yes: function (dIndex) {
                        var insTree = $.fn.zTree.getZTreeObj('roleAuthTree');
                        var checkedRows = insTree.getCheckedNodes(true);
                        var ids = [];
                        for (var i = 0; i < checkedRows.length; i++) {
                            ids.push(checkedRows[i].id);
                        }
                        func.ajaxPost("/adminrom/setPermission", {
                            type: 2,
                            typeId: data.id,
                            authIds: ids.join(',')
                        }, function (res, success) {
                            // 关闭窗体
                            layer.close(dIndex);
                        });
                    }
                });
            } else if (layEvent === 'resetPwd') {
                //初始化密码
                var url = cUrl + "/resetPwd/?id=" + data.id;
                func.ajaxPost(url, {'id': data.id}, function (data, success) {
                    console.log("重置密码：" + (success ? "成功" : "失败"));
                })
            }
        });

        //【设置弹框】
        func.setWin("人员");

        //【设置人员状态】
        func.formSwitch('status', null, function (data, res) {
            console.log("开关回调成功");
        });
    }
});