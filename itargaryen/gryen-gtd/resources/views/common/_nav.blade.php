<nav class="navbar navbar-expand-lg navbar-light border-bottom bg-white @if(isset($extraClass) && !empty($extraClass)){{ ' ' . $extraClass }}@endif">
    <div class="container">
        <a class="navbar-brand t-navbar-brand" href="{{ action('HomeController@index') }}"><img
                    src="https://statics.gryen.com/logo.png" alt="{{ isset($CONFIG->SITE_TITLE) ? $CONFIG->SITE_TITLE : env('APP_NAME') }}"></a>
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#tNavbarCollapse"
                aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="tNavbarCollapse">
            <ul class="nav navbar-nav">
                <li class="nav-item @if (isset($module) && $module === 'article-list') active @endif">
                    <a class="nav-link" href="{{ action('ArticlesController@index') }}">笔记</a>
                </li>
                @if (Auth::check())
                    <li class="nav-item "><a class="nav-link" href="{{url('/dashboard')}}">仪表盘</a></li>
                    <li class="nav-item "><a class="nav-link" href="{{url('/articles/create')}}">添加文章</a></li>
                @endif
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav>
