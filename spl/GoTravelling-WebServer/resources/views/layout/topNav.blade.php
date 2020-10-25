@if( Auth::check() )
    <nav class="main-container-fluid nav-bg nav-fixed">
        <div class="main-container clearfix nav-content">
            <div class="nav-header">
                <a href="{{ URL::to('/') }}">
                    <img src="{{asset('image/logo.png')}}" alt="GoTravelling Logo"  />
                </a>
            </div>

            <ul class="nav-list nav-left clearfix">
                <li><a href="{{url('personal/info')}}">个人信息</a></li>
                <li><a href="{{url('route')}}">我的路线</a></li>
            </ul>

            <ul class="nav-list nav-right clearfix">
                <li><a href="{{url('route')}}#/create"><i class="fa fa-plus"></i> 新建路线</a></li>
                <li class="nav-second-list-label">
                    <a href="{{url('personal/info')}}" alt="个人空间"><img class="head_image" src="{{asset('image/header/'. Auth::user()['head_image'])}}" alt="用户头像"/></a>
                    <ul class="nav-second-list">
                        <li><a href="{{url('personal/info')}}"><i class="fa fa-cog"></i> 用户设置</a></li>
                        <li><a href="{{url('auth/logout')}}"><i class="fa fa-sign-out"></i> 退出登录</a></li>
                    </ul>
                </li>
            </ul>

        </div>
    </nav>
@endif