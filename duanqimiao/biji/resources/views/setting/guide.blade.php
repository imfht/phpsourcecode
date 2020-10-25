<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>帮助中心</title>
    {{--<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">--}}
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <link rel="stylesheet" media="screen" href="{{ asset('/css/style.css') }}">
</head>
<body>

<!-- particles.js container -->
<div id="particles-js">

</div>
<div class="style">
    <div class="header">
        <div class="title"><a href="{{ url('/biji') }}">笔友 | Be yourself</a></div>
        <div class="sub">使用指南</div>
    </div>
    <div class="steps">
        <div class="marks">
           {{-- <div class="mark"><i class="icon guide-img"></i>

                <div style="margin-top: 1em">入门指南</div>
            </div>
            <div class="mark"><i class="icon skill-img"></i>

                <div style="margin-top: 0.5em">技巧&教程</div>
            </div>
            <div class="mark"><i class="icon qa-img"></i>

                <div style="margin-top: 1em">问题解答</div>
            </div>
            <div style="clear: both"></div>--}}
        </div>
        <div class="hot">
            <div class="hot-title">热门文章</div>
            <div class="partials">
                <div class="left">
                    <div><a href="{{ url('/guide/1') }}" class="how" target="_blank">如何删除笔记和管理“废纸篓”笔记本</a></div>
                    <div><a href="{{ url('/guide/3') }}" class="how" target="_blank">如何分享笔记</a></div>
                    <div><a href="{{ url('/guide/5') }}" class="how" target="_blank">如何找回丢失的笔记</a></div>
                </div>
                <div class="right">
                    <div><a href="{{ url('/guide/2') }}" class="how" target="_blank">如何获得笔记链接</a></div>
                    <div><a href="{{ url('/guide/4') }}" class="how" target="_blank">如何修改密码</a></div>
                    <div><a href="{{ url('/guide/6') }}" class="how" target="_blank">关于设备设置的常见问题</a></div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div class="footer">
            Copyright 2016 dqm Corporation. 保留所有权利。
        </div>
    </div>
</div>

<!-- scripts -->
<script src="{{ URL::asset('/') }}js/particles.js"></script>
<script src="{{ URL::asset('/') }}js/app.js"></script>

<!-- stats.js -->
<script src="{{ URL::asset('/') }}js/stats.js"></script>
<script>
    var count_particles, stats, update;
    stats = new Stats;
    stats.setMode(0);
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.left = '0px';
    stats.domElement.style.top = '0px';
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector('.js-count-particles');
    update = function () {
        stats.begin();
        stats.end();
        if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
            count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>

</body>
</html>
