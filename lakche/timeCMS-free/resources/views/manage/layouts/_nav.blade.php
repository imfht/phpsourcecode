<header>
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">{{ $system['title'] }}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{ url('/') }}" class="navbar-brand">
                <img src="{{ asset($theme.'/images/logo.png') }}" alt="{{ $system['title'] }}">
                {{ $system['title'] }}
            </a>
        </div>
        <nav id="bs-navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                @if($system_menus = Theme::menu_data(9,bySort,1))
                    @foreach($system_menus as $system_menu)
                    <li><a href="{{ $system_menu->url }}">{{ $system_menu->name }}</a></li>
                    @endforeach
                @endif
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if (auth()->check())
                    <li><a href="{{ url('user') }}">{{ str_limit(auth()->user()->name,10,'...') }}</a></li>
                    <li><a href="{{ url('user') }}">个人管理</a></li>
                @if (Auth::user()->is_admin)
                        <li><a href="{{ url("admin") }}">系统管理</a></li>
                    @endif
                    <li><a href="{{ url("auth/logout") }}">退出登录</a></li>
                @else
                    <li><a href="{{ url('auth/register') }}">注册</a></li>
                    <li><a href="{{ url('auth/login') }}">登录</a></li>
                @endif
            </ul>
        </nav>
    </div>
</header>