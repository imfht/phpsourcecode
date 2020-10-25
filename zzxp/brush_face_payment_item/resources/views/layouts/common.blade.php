<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('lib/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('lib/respond.min.js') }}"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('static/h-ui/css/H-ui.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/Hui-iconfont/1.0.8/iconfont.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('static/h-ui/css/H-ui.admin.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/h-ui/css/H-ui.doc.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/Hui-iconfont/1.0.8/iconfont.min.css') }}" />
    <script type="text/javascript" src="{{ asset('lib/My97DatePicker/4.8/WdatePicker.js') }}" ></script>

    <!--[if lt IE 9]>
    <link href="{{ asset('static/h-ui/css/H-ui.ie.css') }}" rel="stylesheet" type="text/css" />
    <![endif]-->
    <!--[if IE 6]>wdatepicker.j
    <script type="text/javascript" src="{{ asset('lib/DD_belatedPNG_0.0.8a-min.js') }}" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <style>
        .row{margin-top:15px;}
    </style>
    <title>刷个脸系统管理后台</title>
    <meta name="keywords" content="关键词,5个左右,单个8汉字以内">
    <meta name="description" content="网站描述，字数尽量空制在80个汉字，160个字符以内！">
</head>
<body>
@yield('content')

<div id="confirm-modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="height:100%;">
        <div class="modal-content radius">
            <div class="modal-header">
                <h3 class="modal-title">消息确认</h3>
                <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" >确定</button>
                <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('lib/jquery/1.9.1/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/uploadfile.js') . '?time=' . filemtime('js/uploadfile.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/h-ui/js/H-ui.admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/admin.js') . '?time=' . filemtime('js/admin.js') }}"></script>
<script type="text/javascript" src="{{ asset('lib/jquery.SuperSlide/2.1.1/jquery.SuperSlide.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('lib/jquery.validation/1.14.0/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script type="text/javascript" src="{{ asset('lib/jquery.validation/1.14.0/messages_zh.min.js') }}"></script>

<script>
    $(function(){
        //幻灯片
        jQuery("#slider-3 .slider").slide({mainCell:".bd ul",titCell:".hd li",trigger:"click",effect:"leftLoop",autoPlay:true,delayTime:700,interTime:3000,pnLoop:false,titOnClassName:"active"});
        //邮箱提示
        $("#email").emailsuggest({});
        //checkbox 美化
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

        //日期插件
        $("#datetimepicker").datetimepicker({
            format: 'yyyy-mm-dd',
            minView: "month",
            todayBtn:  1,
            autoclose: 1,
            endDate : new Date()
        }).on('hide',function(e) {
            //此处可以触发日期校验。
        });

        /*+1 -1效果*/
        $("#spinner-demo").Spinner({value:1, min:1, len:2, max:99});



        //返回顶部
        $(window).on("scroll",backToTopFun);
        backToTopFun();

        //hover效果
        $.Huihover('.maskWraper');

    });

    //弹窗
    function set_func(){
        $("#modal-demo").modal("show");
    }
    //消息框
    function modalalertdemo(){
        $.Huimodalalert('我是消息框，2秒后我自动滚蛋！',2000);
    }
</script>
@yield('scripts')



<style>
    .page li a{border:1px solid #aaa;}
    .page .selected{cursor:not-allowed;}
</style>

</body>
</html>