@extends('InstallTheme::layout.page')

@section('content')
<div class="listing listing-success">
    <div class="shape">
        <div class="shape-text">{{$version}}</div>
    </div>
    <div class="listing-content">
        <h3 class="lead">Simpla安装向导<small>第一步</small></h3>
        <hr>
        <p>
            Simpla 软件使用协议</p>

        <p>版权所有(c)2014-2015，simplahub.com保留所有权力。</p>

        <p>感谢您选择 simpla 内容管理系统, 希望他能够帮您把网站发展的更快、更好、更强！</p>

        <p>Simpla 遵循GUN协议。你可以遵守协议进行你想要的操作。</p>

        <p>Simpla是一款免费开源软件，但是希望你在你的网站底部保留我们的连接，给予我们支持。</p>

        <p>通过Simpla的官方网站，你可以免费的获得主题和模块，或者贡献你自己的主题或者模块。</p>

        <p>再次感谢你使用Simpla，我们将会做的更好！</p>

        <p>如果你有任何问题或者疑问，请到<a href="http://simpla.simplahub.com" target="_blank">Simpla官方网站</a>或者<a href="http://www.simplahub.com" target="_blank">Simplahub社区</a>进行咨询。</p>
        <hr>
        <p class="text-align-center">
            <a href="/install/step2" type="button" class="btn btn-primary">接受</a>
        </p>
    </div>
</div>
@stop