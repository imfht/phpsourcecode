<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="javascript:;" class="logo"><b>DF</b>CMS</a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{\Auth::user()->avatar}}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{\Auth::user()->name}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{\Auth::user()->avatar}}" class="img-circle" alt="User Image" />
                            <p>
                                {{\Auth::user()->name}} - @foreach(\Auth::user()->roles as $role) [{{$role->name}}]  @endforeach
                                <small>{{\Auth::user()->created_at}}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{route('user.change_my')}}" class="btn btn-primary btn-flat">个人信息</a>
                            </div>
                            <div class="pull-right">
                                <a onclick="event.preventDefault();document.getElementById('logout-form').submit();" href="{{route('logout')}}" class="btn btn-primary btn-flat">退出</a>

                                {!! Form::open(['route' => 'logout','id'=>'logout-form','style'=>'display: none;','onclick'=>'event.preventDefault();document.getElementById("logout-form").submit();']) !!}
                                {!! Form::close() !!}
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>