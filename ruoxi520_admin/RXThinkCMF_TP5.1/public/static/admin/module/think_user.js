/**
 * 用户管理
 * @author 牧羊人
 * @since 2020/7/11
 */
layui.use(['function', 'form'], function () {
    var form = layui.form,
        func = layui.function,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'realname', width: 100, title: '真实姓名', align: 'center'}
            , {field: 'nickname', width: 120, title: '用户昵称', align: 'center'}
            , {field: 'gender', width: 80, title: '性别', align: 'center', templet(d) {
                    var cls = "";
                    if (d.gender == 1) {
                        // 男
                        cls = "layui-btn-normal";
                    } else if (d.gender == 2) {
                        // 女
                        cls = "layui-btn-danger";
                    } else if (d.gender == 3) {
                        // 未知
                        cls = "layui-btn-warm";
                    }
                    return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.gender_name+'</span>';
                }}
            , {field: 'avatar', width: 90, title: '用户头像', align: 'center', templet: function (d) {
                    var avatarStr = "";
                    if (d.avatar_url) {
                        avatarStr = '<a href="' + d.avatar_url + '" target="_blank"><img src="' + d.avatar_url + '" height="26" /></a>';
                    }
                    return avatarStr;
                }
            }
            , {field: 'status', width: 100, title: '状态', align: 'center', templet: '#statusTpl'}
            , {field: 'mobile', width: 130, title: '手机号码', align: 'center'}
            , {field: 'city_area', width: 200, title: '所在地区', align: 'center'}
            , {field: 'device', width: 100, title: '设备类型', align: 'center', templet(d) {
                    var cls = "";
                    if (d.device == 1) {
                        // 苹果
                        cls = "layui-btn-normal";
                    } else if (d.device == 2) {
                        // 安卓
                        cls = "layui-btn-danger";
                    } else if (d.device == 3) {
                        // WAP站
                        cls = "layui-btn-warm";
                    } else if (d.device == 4) {
                        // PC站
                        cls = "layui-btn-primary";
                    } else if (d.device == 5) {
                        // 微信小程序
                        cls = "layui-btn-disabled";
                    }

                    return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.device_name+'</span>';
                }}
            , {field: 'source', width: 100, title: '用户来源', align: 'center', templet(d) {
                    var cls = "";
                    if (d.source == 1) {
                        // 注册会员
                        cls = "layui-btn-normal";
                    } else if (d.source == 2) {
                        // 马甲会员
                        cls = "layui-btn-danger";
                    }
                    return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.source_name+'</span>';
                }}
            , {field: 'create_time', width: 180, title: '注册时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.create_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'login_time', width: 180, title: '最近登录时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.login_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'login_ip', width: 100, title: '最近登录IP', align: 'center'}
            , {field: 'login_region', width: 130, title: '上次登录地点', align: 'center'}
            , {field: 'login_count', width: 100, title: '登录总次数', align: 'center'}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'left', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("会员用户");

        //【设置状态】
        func.formSwitch('status', null, function (data, res) {
            console.log("开关回调成功");
        });
    }
});