@if ($logged_in)
<li class="dropdown">
    <a data-toggle="dropdown" class="dropdown-toggle" href="#">欢迎,{{$users->username}}<span class="caret"></span></a>
    <ul role="menu" class="dropdown-menu">
        <li><a href="/user/{{$users->id}}">个人中心</a></li>
        @if ($users->roles['rid'] == '3')
        <li><a href="/admin">后台控制面板</a></li>
        @endif
        <li class="divider"></li>
        <li><a href="/logout">退出登录</a></li>
    </ul>
</li>
@else
@if($is_allow_login)
<li><a href="/login?back_url={{Request::url()}}">登录</a></li>
@endif
@if($is_allow_register)
<li><a href="/register?back_url={{Request::url()}}">注册</a></li>
@endif
@endif