<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('new_skin/lib/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new_skin/lib/respond.min.js') }}"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('new_skin/static/h-ui/css/H-ui.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_skin/static/h-ui.admin/css/H-ui.admin.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_skin/lib/Hui-iconfont/1.0.8/iconfont.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_skin/static/h-ui.admin/skin/default/skin.css') }}" id="skin" />
    <link rel="stylesheet" type="text/css" href="{{ asset('new_skin/static/h-ui.admin/css/style.css') }}" />
    <!--[if IE 6]>
    <script type="text/javascript" src="{{ asset('new_skin/lib/DD_belatedPNG_0.0.8a-min.js') }}" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <title>刷个脸系统管理后台</title>
    <meta name="keywords" content="关键词,5个左右,单个8汉字以内">
    <meta name="description" content="网站描述，字数尽量空制在80个汉字，160个字符以内！">
</head>
<body>
<script type="text/javascript" src="{{ asset('lib/jquery/1.9.1/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/admin.js') . '?time=' . filemtime('js/admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script>
    function edit_func_resetPass(id){
        $(function(){
            global_submit_bool = false;
            sendData('{{url('admin/system_user/editmyselfpassword')}}','system_user_id='+id,function(obj){
                if(typeof obj == 'object'){

                    $('#modal-demo-resetPass .edit_pro').show();
                    setDefaultValue(obj,'modal-demo-resetPass');
                    $("#modal-demo-resetPass").modal("show");

                    //获取数据库加密后的密码
                    var password=$("input[name='password']").val();
                    //将数据库内的加密后密码赋值给隐藏表单
                    $("input[name='yuan_md5_password']").val(password);
                    //清空密码框的密码
                    $("input[name='password']").val('');
                }
            },'GET');
        });
    }

    function sure_add_resetPass(id){
        if(!global_submit_bool){
            return sure_edit_resetPass(id);
        }
        send_request('{{url('admin/system_user/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit_resetPass(id){
        //如果原密码、密码只要一个为空，则点击确定无效
        var passWd=$('.passWd').val();
        var oriPass=$('.oriPass').val();
        if(passWd=="" || passWd==undefined || passWd==null || oriPass=="" || oriPass==undefined || oriPass==null){
            alert('原密码或密码不能为空');exit();
        }
        send_request('{{url('admin/system_user/updatemyselfpassword')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    //更新缓存
    function updateCache(){
        $.ajax({
            url:"{{url('admin/system_role/updateCache')}}",
            type:'get'
        });
    }
</script>

<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="/">刷个脸系统管理后台</a>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
            <nav class="nav navbar-nav">
                {{--<ul class="cl">
                    <li class="dropDown dropDown_hover"><a href="javascript:;" class="dropDown_A"><i class="Hui-iconfont">&#xe600;</i> 新增 <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" onClick="article_add('添加资讯','article-add.html')"><i class="Hui-iconfont">&#xe616;</i> 资讯</a></li>
                            <li><a href="javascript:;" onClick="picture_add('添加资讯','picture-add.html')"><i class="Hui-iconfont">&#xe613;</i> 图片</a></li>
                            <li><a href="javascript:;" onClick="product_add('添加资讯','product-add.html')"><i class="Hui-iconfont">&#xe620;</i> 产品</a></li>
                            <li><a href="javascript:;" onClick="member_add('添加用户','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 用户</a></li>
                        </ul>
                    </li>
                </ul>--}}
            </nav>
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li>{{session('role_name')}}</li>
                    <li class="dropDown dropDown_hover">
                        <a href="#" class="dropDown_A">{{session('nick_name')}} <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a onClick="edit_func_resetPass({{session('sys_id')}});">修改密码</a></li>
                            <li><a onclick="updateCache()">更新缓存</a></li>
                            <li><a href="{{url('login/out')}}">安全退出</a></li>
                        </ul>
                    </li>
                    {{--<li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>--}}
                    <li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                            <li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
                            <li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
                            <li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
                            <li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
                            <li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>



<aside class="Hui-aside">
    <input runat="server" id="divScrollValue" type="hidden" value="" />
    <div class="menu_dropdown bk_2">
        {{--<ul>
            <li>
                <a data-href="{{url('/welcome')}}" data-title="首页" href="javascript:void(0)">&nbsp;&nbsp;&nbsp;&nbsp;首页</a>
            </li>
        </ul>--}}
        @if(is_array($menu))
            @foreach($menu as $pmenu)
                @if($pmenu['status']==0)
                    @if($pmenu['parent_id'] == 0)
                        <dl id="menu_notes">
                            <dt>&nbsp;&nbsp;&nbsp;&nbsp;{{$pmenu['title']}}<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
                            <dd>
                                <ul>
                                    @foreach($menu as $cmenu)
                                        @if($cmenu['status']==0)
                                            @if($cmenu['parent_id'] == $pmenu['system_menu_id'])
                                                <li><a data-href="{{url($cmenu['action_url'])}}" data-title="{{$cmenu['title']}}" href="javascript:void(0)">{{$cmenu['title']}}</a></li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </dd>
                        </dl>
                    @endif
                @endif
            @endforeach
        @endif

    </div>
</aside>

<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active">
                    <span title="运营概况" data-href="">运营概况</span>
                    <em></em></li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div style="display:none" class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="{{url('welcome')}}"></iframe>
        </div>
    </div>
</section>

<div class="contextMenu" id="Huiadminmenu">
    <ul>
        <li id="closethis">关闭当前 </li>
        <li id="closeall">关闭全部 </li>
    </ul>
</div>

<div id="modal-demo-resetPass" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="height:100%;">
        <div class="modal-content radius">
            <div class="modal-header">
                <h3 class="modal-title">密码修改</h3>
                <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
            </div>
            <div class="modal-body">

                <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
                    <input type="hidden" value="" name="system_user_id">


                    <div class="row cl edit_pro">
                        <label class="form-label col-xs-3">原密码<span style="color:red">(输入原密码才能进行修改)</span>:</label>
                        <div class="formControls col-xs-8">
                            <input type="password" class="input-text oriPass" name="originalPassword" placeholder="原密码" >
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">密码：</label>
                        <div class="formControls col-xs-8">
                            <input type="password" class="input-text passWd" name="password" placeholder="密码" >
                        </div>
                    </div>
                    <input type="hidden" name="yuan_md5_password">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onClick="sure_add_resetPass('modal-demo-resetPass')">确定</button>
                <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
            </div>
        </div>
    </div>
</div>
<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="{{ asset('new_skin/lib/jquery/1.9.1/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_skin/lib/layer/2.4/layer.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_skin/static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_skin/static/h-ui.admin/js/H-ui.admin.js') }}"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{ asset('new_skin/lib/jquery.contextmenu/jquery.contextmenu.r2.js') }}"></script>
<script type="text/javascript">



</script>


</body>
</html>