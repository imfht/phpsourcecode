<?php $url_array = explode('/', Request::path()) ?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a href="/admin"><i class="fa fa-dashboard fa-fw"></i>控制面板</a>
            </li>

            @foreach(RolesPermission::getRoutes() as $key=>$access)
            @if($access['list'])
            <li class="{{in_array($key,$url_array)?'active':''}}">
                <a href="#" class="{{in_array($key,$url_array)?'active':''}}"><i class="fa {{$access['class']}} fa-fw"></i>{{$access['title']}}<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    @foreach($access['list'] as $row)
                    <li>
                        <?php try {
                            URL::route($row['as']);
                        } catch (Exception $e) {
                            continue;
                        } //是为了解决开启模块的时候，导致路径不存在，所以这样处理的?>
                        @if(URL::route($row['as']) == Request::url())
                        <a href="{{URL::route($row['as'])}}" class="active">{{$row['title']}} <span class="glyphicon glyphicon-hand-right"></span></a>
                        @else
                        <a href="{{URL::route($row['as'])}}">{{$row['title']}}</a>
                        @endif

                    </li>
                    @endforeach
                </ul>
            </li>
            @endif
            @endforeach

            <!--
            <li>
                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="#">Second Level Item</a>
                    </li>
                    <li>
                        <a href="#">Second Level Item</a>
                    </li>
                    <li>
                        <a href="#">Third Level <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            -->

        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->