<div class="scrollspy">
    <div id="pop" data-spy="affix" class="text-center" data-toggle="modal" data-target="#pop-box">导航</div>
</div>
<div class="modal fade bs-example-modal-lg" id="pop-box" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">网站导航</h4>
            </div>
            <div class="modal-body clearfix">
                <div class="col-sm-6">
                    <div class="list-group">
                        <a class="list-group-item" href="{{ url('category',1) }}">技术漫谈</a>
                        <a class="list-group-item" href="{{ url('category',2) }}">说天道地</a>
                        <a class="list-group-item" href="{{ url('person') }}">荣誉殿堂</a>
                        <a class="list-group-item" href="{{ url('project') }}">作品展示</a>
                        <a class="list-group-item" href="{{ url('page/building') }}">静思堂</a>
                        <a class="list-group-item" href="{{ url('page/building') }}">通天塔</a>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="list-group">
                        @if (auth()->check())
                            <a class="list-group-item" href="{{ url('user') }}">个人管理</a>
                            @if (Auth::user()->is_admin)
                                <a class="list-group-item" href="{{ url("admin") }}">系统管理</a>
                            @endif
                            <a class="list-group-item" href="{{ url("auth/logout") }}">退出登录</a>
                        @else
                            <a class="list-group-item" href="{{ url('auth/register') }}">注册</a>
                            <a class="list-group-item" href="{{ url('auth/login') }}">登录</a>
                        @endif
                    </div>
                    <div class="list-group">
                        <a class="list-group-item" href="tencent://message/?uin={{ $system['qq'] }}&amp;Site=www.obday.com&amp;Menu=yes" target="_blank">官方QQ：{{ $system['qq'] }}</a>
                        <a class="list-group-item" href="http://weibo.com/{{ $system['weibo'] }}" target="_blank">官方微博：[点击打开]</a>
                        <a class="list-group-item" href="#" data-toggle="modal" data-target="#wechatcode-box">微信号：{{ $system['wechat'] }}[点击扫码]</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="wechatcode-box" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">请扫描微信二维码</h4>
            </div>
            <div class="modal-body clearfix text-center">
                @if($system['wechatcode'] != '')
                    <img src="{{ $system['wechatcode'] }}" alt="请扫描微信二维码">
                @endif
            </div>
        </div>
    </div>
</div>
<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"slide":{"type":"slide","bdImg":"6","bdPos":"right","bdTop":"100"}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
<style>
    .affix {
        right: 30px;
        bottom: 50px;
        width: 50px;
        height: 50px;
        line-height: 50px;
        background: #fff;
        -moz-border-radius: 50px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
        -moz-box-shadow: 0 0 12px 6px rgba(0,0,0,.175);
        -webkit-box-shadow: 0 0 12px 6px rgba(0,0,0,.175);
        box-shadow: 0 0 12px 6px rgba(0,0,0,.175);
        cursor: pointer;
        font-weight: bold;
    }
    .affix:hover {
        animation: blinking 3s linear 1s infinite alternate;
        -moz-animation: blinking 3s linear 1s infinite alternate;
        -webkit-animation: blinking 3s linear 1s infinite alternate;
        -o-animation: blinking 3s linear 1s infinite alternate;
    }
    @keyframes blinking
    {
        0%   {background: #ffffff;}
        25%  {background: #FFCC99;}
        50%  {background: #FFFF99;}
        75%  {background: #99CC99;}
        100% {background: #ffffff;}
    }
</style>
<script>
    var _winHeight = $(document).height();
    var _showHeight = $('nav').outerHeight(true);
    $('#pop').affix({
        offset: {
            top :  _showHeight
        }
    });
</script>