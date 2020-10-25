<!--左侧导航开始-->
@php
    $admin = Auth::guard('admin')->user();
@endphp
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i>
    </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header text-center">
                <div class="dropdown profile-element">
                                <span>
                                    <img alt="image" class="img-circle" src="{{$admin->avatr}}" width="64"/>
                                </span>
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold">{{$admin->name}}</strong>
                                </span>
                                <span class="text-muted text-xs block">
                                    @foreach($admin->roles as $role)
                                      {{$role->name}}
                                    @endforeach
                                    <b class="caret"></b>
                                </span>
                                </span>
                                </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="J_menuItem" href="form_avatar.html">修改头像</a></li>
                        <li><a class="J_menuItem" href="form_avatar.html">修改密码</a></li>
                        <li class="divider"></li>
                        <li><a href="{{route('admin.logout')}}">安全退出</a></li>
                    </ul>
                </div>
                <div class="logo-element">YS+</div>
            </li>

            @foreach(Auth::guard('admin')->user()->getMenus() as $key => $rule)
                @if($rule['route'] == 'index.index')
                    <li>
                        <a title="{{$rule['name']}}" href="{{route($rule['route'])}}" target="_blank">
                            <i class="fa fa-{{$rule['fonts']}}"></i>
                            <span class="nav-label">{{$rule['name']}}</span>
                        </a>
                        @if(isset($rule['children']))
                            <ul class="nav nav-second-level collapse">
                                @foreach($rule['children'] as $k=>$item)
                                    <li><a class="J_menuItem" href="{{ route($item['route']) }}" data-index="{{$item['id']}}">{{$item['name']}}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    <li>
                @else
                    <li>
                        <a title="{{$rule['name']}}"> <i class="fa fa-{{$rule['fonts']}}"></i>
                            <span class="nav-label">{{$rule['name']}}</span>
                            <span class="fa arrow"></span>
                        </a>
                        @if(isset($rule['children']))
                            <ul class="nav nav-second-level collapse">
                                @foreach($rule['children'] as $k=>$item)
                                    <li><a class="J_menuItem" href="{{ route($item['route']) }}" data-index="index_v1.html">{{$item['name']}}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
                <li>
            @endforeach
        </ul>
    </div>
</nav>
<!--左侧导航结束-->