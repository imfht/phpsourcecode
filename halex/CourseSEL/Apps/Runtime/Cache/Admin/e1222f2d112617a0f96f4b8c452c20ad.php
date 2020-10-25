<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo (C("app_name")); echo (C("app_copy")); ?></title>
    <link rel="shortcut icon" href="/web/CourseSEL/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/web/CourseSEL/Public/layui/css/layui.css" media="all">
    <link rel="stylesheet" type="text/css" href="/web/CourseSEL/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/web/CourseSEL/Public/build/css/app.css" media="all">
    <script type="text/javascript" src="/web/CourseSEL/Public/js/xadmin.js"></script>
    <script src="/web/CourseSEL/Public/js/jquery-1.11.1.min.js"></script>
</head>

<body>
    <div class="layui-layout layui-layout-admin kit-layout-admin">
        <div class="layui-header">
            <div class="layui-logo" style="font-size: 30px;"><b><?php echo (C("app_name")); ?></b></div>
            <div class="layui-logo kit-logo-mobile"><?php echo (C("app_name")); ?></div>
            <ul class="layui-nav layui-layout-left kit-nav" kit-navbar>
                <li class="layui-nav-item"><a href="javascript:;" style="font-size: 20px;"><?php echo (C("site_name")); ?></a></li>
                <!-- <li class="layui-nav-item"><a href="javascript:;" class="layui-btn layui-btn-radius layui-btn-small">后台</a></li> -->
                <li class="layui-nav-item"><a href="javascript:;" id="pay"><i class="fa fa-gratipay" aria-hidden="true"></i> 撩我</a></li>
                <!-- <li class="layui-nav-item">
                    <a href="javascript:;">其它系统</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-url="<?php echo U('test');?>" data-icon="fa-bar-chart" data-title="数据分析" kit-target data-id='11'>邮件管理</a></dd>
                        <dd><a href="javascript:;">消息管理</a></dd>
                        <dd><a href="javascript:;">授权管理</a></dd>
                    </dl>
                </li> -->
            </ul>
            <ul class="layui-nav layui-layout-right kit-nav" lay-filter="kitNavbar" kit-navbar>
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <!-- <img src="http://m.zhengjinfan.cn/images/0.jpg" class="layui-nav-img"> --> <i class="fa fa-user-circle-o " style="font-size: 20px; margin: 5px;" aria-hidden="true"></i><?php echo (session('username')); ?>&nbsp;<span style="color: #c2c2c2;">[<?php echo (get_type($_SESSION['type'])); ?>]</span>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" id="info">基本资料</a></dd>
                        <!-- <dd><a href="javascript:;" id="lock">锁定屏幕</a></dd> -->
                        <dd><a href="javascript:;" onclick="x_admin_show('修改密码','/web/CourseSEL/index.php/Admin/Index/tpass')">修改密码</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item"><a href="<?php echo U('Index/Login/out_login');?>" onclick="return out_login();"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a></li>
            </ul>
        </div>

        <div class="layui-side layui-bg-black kit-side">
            <div class="layui-side-scroll">
                <div class="kit-side-fold"><i class="fa fa-navicon" aria-hidden="true"></i></div>
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree" lay-filter="kitNavbar" kit-navbar>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;"><i class="fa fa-list" aria-hidden="true"></i><span> 选课管理</span></a>
                        <dl class="layui-nav-child">
                            <dd>
                                <a href="javascript:;" kit-target data-options="{url:'<?php echo U('Selection/index');?>',icon:'&#xe6c6;',title:'选课项目',id:'1'}">
                                    <i class="layui-icon">&#xe6c6;</i><span> 选课项目</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" data-url="<?php echo U('Data/index');?>" data-icon="fa-bar-chart" data-title="选课数据" kit-target data-id='2'><i class="fa fa-bar-chart" aria-hidden="true"></i><span> 选课数据</span></a>
                            </dd>
                            <!-- <dd>
                                <a href="javascript:;" data-url="<?php echo U('Data/index');?>" data-icon="fa-bar-chart" data-title="数据分析" kit-target data-id='3'><i class="fa fa-bar-chart" aria-hidden="true"></i><span> 数据分析</span></a>
                            </dd> -->
                            <!-- <dd>
                                <a href="javascript:;" data-url="nav.html" data-icon="&#xe628;" data-title="导航栏" kit-target data-id='3'><i class="layui-icon">&#xe628;</i><span> 导航栏</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" data-url="list4.html" data-icon="&#xe614;" data-title="列表四" kit-target data-id='4'><i class="layui-icon">&#xe614;</i><span> 列表四</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" kit-target data-options="{url:'https://www.baidu.com',icon:'&#xe658;',title:'百度一下',id:'5'}"><i class="layui-icon">&#xe658;</i><span> 百度一下</span></a>
                            </dd> -->
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;"><i class="fa fa-list" aria-hidden="true"></i><span> 学生管理</span></a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" kit-target data-options="{url:'<?php echo U('Stu/index');?>',icon:'fa-user',title:'学生列表',id:'6'}"><i class="fa fa-user" aria-hidden="true"></i><span> 学生列表</span></a></dd>
                            <dd><a href="javascript:;" kit-target data-options="{url:'<?php echo U('Stu/import_index');?>',icon:'fa-sign-in',title:'批量导入',id:'7'}"><i class="fa fa-sign-in" aria-hidden="true"></i><span> 批量导入</span></a></dd>
                            <!-- <dd><a href="javascript:;" kit-target data-options="{url:'onelevel.html',icon:'&#xe658;',title:'OneLevel',id:'50'}"><i class="layui-icon">&#xe658;</i><span> OneLevel</span></a></dd>
                            <dd><a href="javascript:;" kit-target data-options="{url:'app.html',icon:'&#xe658;',title:'App',id:'8'}"><i class="layui-icon">&#xe658;</i><span> app.js主入口</span></a></dd> -->
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;" data-url="/components/table/table.html" data-name="table" kit-loader><i class="fa fa-list" aria-hidden="true"></i><span> 教师管理</span></a>
                        <dl class="layui-nav-child">
                            <dd>
                                <a href="javascript:;" kit-target data-options="{url:'<?php echo U('Teacher/index');?>',icon:'fa-list',title:'教师列表',id:'11'}">
                                    <i class="fa fa-list" aria-hidden="true"></i><span> 教师列表</span></a>
                            </dd>
                            <!-- <dd>
                                <a href="javascript:;" data-url="form.html" data-icon="fa-user" data-title="表单" kit-target data-id='2'><i class="fa fa-user" aria-hidden="true"></i><span> 表单</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" data-url="nav.html" data-icon="&#xe628;" data-title="导航栏" kit-target data-id='3'><i class="layui-icon">&#xe628;</i><span> 导航栏</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" data-url="list4.html" data-icon="&#xe614;" data-title="列表四" kit-target data-id='4'><i class="layui-icon">&#xe614;</i><span> 列表四</span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" kit-target data-options="{url:'https://www.baidu.com',icon:'&#xe658;',title:'百度一下',id:'5'}"><i class="layui-icon">&#xe658;</i><span> 百度一下</span></a>
                            </dd> -->
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;" data-url="/views/form.html" data-name="form" kit-loader><i class="fa fa-plug" aria-hidden="true"></i><span> 其它</span></a>
                    </li>
                </ul>
            </div >
        </div>
        <div class="layui-body" id="container">
            <!-- 内容主体区域 -->
            
            <div style="padding: 15px;">主体内容加载中,请稍等...</div>
        </div>


        <div class="layui-footer">
            <!-- 底部固定区域 -->
            &copy; 2017 |&nbsp;<img src="/web/CourseSEL/16.png" alt=""> CourseSEL | Design by <a href="javascript:;" id="me"> hxb0810</a>

        </div>
    </div>
    
    <script type="text/javascript">
        // var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        // document.write(unescape("%3Cspan id='cnzz_stat_icon_1264021086'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/z_stat.php%3Fid%3D1264021086%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script src="/web/CourseSEL/Public/layui/layui.js"></script>
    <script>

        var message;
        layui.config({
            base: '/web/CourseSEL/Public/build/js/'
        }).use(['app', 'message'], function() {
            var app = layui.app,
                $ = layui.jquery,
                layer = layui.layer;
            //将message设置为全局以便子页面调用
            message = layui.message;
            //主入口
            app.set({
                type: 'iframe'
            }).init();
            $('#pay').on('click', function() {
                layer.open({
                    title: false,
                    type: 1,
                    content: '<img src="/web/CourseSEL/Public/img/me.jpg" />',
                    area: ['430px', '430px'],
                    shadeClose: true
                });
             });
            $('#info').on('click',function(){
                  layer.alert("帐号：<?php echo ($info["tname"]); ?><br>姓名：<?php echo ($info["truename"]); ?><br>权限：<?php echo (get_type($info["type"])); ?><br>", {
                    skin: 'layui-layer-lan'
                    ,title:'我的信息'
                    ,closeBtn: 0
                    ,anim: 4 //动画类型
                  });
            });
            $('#lock').on('click',function(){
                   
                  layer.prompt({
                              title:false,
                              formType: 1,
                              closeBtn: 0,
                              //value: '初始值',
                              shade: [0.9, '#393D49'],
                              btn: '解锁',
                              btnAlign: 'c',
                              title: '锁屏',
                              area: ['800px', '350px'] //自定义文本域宽高
                        }, function(value, index, elem){
                              alert(value); //得到value
                              layer.close(index);
                    });
            });
            $('#me').on('click', function() {
                layer.open({
                    title: '朋友，你好！',
                    btn:'朕已知道',
                    type: 0,
                    scrollbar: false,
                    content: '<div style="width:300px; height:105px; background-color:#393D49;color: #fff; margin:-20px;"><div style="padding:20px;text-align:center;">Email:hxb0810@163.com<br>Tel:15534378771</div></div>',
                    area: ['300px', '200px'],
                    shadeClose: true
                });
             });
        });
    function out_login(){
        if(confirm("确定要退出么？")){
        return true;
        }else{
        return false;
        }
    };
    </script>

</body>

</html>