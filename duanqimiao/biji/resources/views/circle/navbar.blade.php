
<ul class="nav navbar-nav">
    <li>
        <input name="search" id="search" type="text" class="form-control" placeholder="搜索笔记 " aria-describedby="basic-addon2" style="height: 40px;margin-top: 5px;margin-left: 30px;"/>
    </li>
    <li><input id="search_btn" type="button" class="btn btn-default" value="搜索" style="height:40px;margin-left: 30px;margin-top: 5px;"></li>
</ul>
<ul class="nav navbar-nav navbar-right">
    <li><a href="{{ url('/collect/') }}">我的收藏</a></li>
    <li><a href="{{ url('/share/') }}">我的分享</a></li>
    <li><a href="{{ url('/biji/') }}">返回个人主页 <span class="glyphicon glyphicon-home"></span></a></li>
    <li></li>
    <li class="dropdown" >

        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
           aria-expanded="false">
            {{ Auth::user()->name }}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="/auth/logout">退出登录</a></li>
        </ul>
    </li>
    <li>
        <a href="{{ url('/setting') }}" target="_Blank" style="padding: 0">
            <img id = "user_img" src="
                @if(empty($thumbObj->thumb))
            {{ url('images/photo.jpg') }}
            @else
            {{ url($thumbObj->thumb) }}
            @endif
                    "
                 style="width: 45px;;" class="img-circle" data-toggle="tooltip" data-placement="right" title="换一张照片">
        </a>
    </li>
</ul>