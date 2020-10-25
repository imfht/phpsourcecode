<footer>
    <div class="container text-center">
        <p><a href="{{ url('/') }}">{{ $system['title'] }}</a> 是在timeCMS(时光CMS)基础上搭建起来的站点，也是timeCMS的官方站点</p>
        <p>
            {{ $system['copyright'] or '' }} {{ $system['record'] or '' }}
            @if(isset($system['miitbeian']))
            <a href="http://www.miitbeian.gov.cn/" target="_blank">{{ $system['miitbeian'] or '' }}</a>
            @endif
            @if(isset($system['beian']))
            @if (preg_match('|(\d+)|',$system['beian'],$beian))
            <div style="width:300px;margin:0 auto; padding:20px 0;">
                <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode={{ $beian[0] }}" style="display:inline-block;text-decoration:none;height:20px;line-height:20px;">
                    <img src="{{ asset('time/images/beian.png') }}" style="float:left;"/>
                    <p style="float:left;height:20px;line-height:20px;margin: 0px 0px 0px 5px; color:#939393;">{{ $system['beian'] or '' }}</p>
                </a>
            </div>
            @endif
            @endif
        </p>
        <ul class="bs-docs-footer-links text-muted">
            <li>本项目源码受 <a rel="license" href="https://github.com/twbs/bootstrap/blob/master/LICENSE" target="_blank">MIT</a>开源协议保护</li>
            <li>文档受 <a rel="license" href="https://creativecommons.org/licenses/by/3.0/" target="_blank">CC BY 3.0</a> 开源协议保护</li>
        </ul>
        <ul class="bs-docs-footer-links text-muted">
            <li>当前版本： v1.0.0</li>
            <li>·</li>
            <li><a href="https://git.oschina.net/lakche/timeCMS-free.git" target="_blank">开源中国仓库</a></li>
            <li>·</li>
            <li><a href="https://github.com/lakche/timeCMS-free.git" target="_blank">github仓库</a></li>
        </ul>
    </div>
</footer>