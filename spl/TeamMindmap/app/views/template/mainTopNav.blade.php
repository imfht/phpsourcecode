

<!-- navigation -->
  <nav id="navigation" class="navbar navbar-fixed-top">
    <div class="container-fluid">
		  
      <!-- logo -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar second-icon-bar"></span>
          <span class="icon-bar"></span>

        </button>
	    <a class="navbar-brand" href="{{ URL::to('/') }}">logo</a>
      </div>

      <!-- navigation content -->
      <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
        <!-- nav-function  -->
        <ul id="nav-function" class="nav navbar-nav">
          @if( Auth::check() )
            <li><a href="{{ URL::to('/ng#/project') }}">我的项目</a></li>
            <li><a href="{{ URL::to('/ng#/personal/information/setting') }}">个人主页</a></li>
          @endif
          <li><a href="{{ URL::to('/guide') }}">功能介绍</a></li>
        </ul>

        <!-- nav-user -->

        <ul id="nav-user" class="nav navbar-nav navbar-right">


        @if( Auth::check() )
            {{------------------创建项目--------------------}}
            <li class="divider-vertical"></li>
            <li id="project-create-li" class="common-li">
                <a href="{{ URL::to('/ng#/project/creating') }}">
                    <span class="glyphicon glyphicon-plus common-image"></span>
                    <span class="common-label">创建项目</span>
                </a>
            </li>
            <li class="divider-vertical"></li>

            {{--------------------消息提示-----------------------}}
            <li id="project-message-li" class="common-li">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bell common-image"></i>
                    <span class="common-label">
                        <span class="label label-default">{{ $unread['notification'] + $unread['message'] }}</span>
                    </span>
                </a>
                <ul class="dropdown-menu common-dropdown">
                    <li><a href="{{ URL::to('/ng#/personal/notification') }}"><i class="fa fa-bullhorn"></i> 通知 <span class="badge">{{ $unread['notification'] }}</span></a></li>
                    <li class="divider"></li>
                    <li><a href="{{ URL::to('/ng#/personal/message/list') }}"><i class="fa fa-envelope"></i> 私信 <span class="badge">{{ $unread['message'] }}</span></a></li>
                </ul>
            </li>
            <li class="divider-vertical"></li>

             {{-------------------头像部分----------------------}}
             <li id="user-head-li" class="dropdown">
               {{----------------头像-------------}}
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                   {{HTML::image('img/userHeadImage/'. Auth::user()->head_image,'head image')}}
               </a>
               {{------------头像下拉菜单--------------}}
               <ul class="dropdown-menu navbar-inverse common-dropdown" role="menu">
                   <li><a href="{{ URL::to('/ng#/personal/setting') }}"><i class="fa fa-gear"></i>   用户设置</a></li>
                   <li class="divider"></li>
                   <li><a href="{{ URL::to('/authority/logout') }}"><i class="fa fa-sign-out"></i>   注销</a></li>
               </ul>
             </li>
        @else
          {{----------------登入注册------------}}
          <li id="login-li" class="authority-li"><a class="btn btn-primary" href="{{ URL::to('authority/login') }}">登入</a></li>
          <li id="signin-li" class="authority-li"><a class="btn btn-success" href="{{ URL::to('authority/signin') }}">注册</a></li>
        @endif
        </ul>
      </div><!-- /.navbar-collapse -->
		  
    </div><!-- /.container-fluid -->
  </nav>
