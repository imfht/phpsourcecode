@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid jumbotron">
        <div class="container">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="item active text-center">
                        <h1>obday {{ config('system.title') }}</h1>
                        <p>这里是timeCMS官方站点</p>
                        <p><a class="btn btn-primary" href="{{ url('article',2) }}">使用说明</a></p>
                    </div>
                    <div class="item text-center">
                        <h1>时光CMS timeCMS</h1>
                        <p>满足你日常的使用需要</p>
                        <p><a class="btn btn-primary" href="https://git.oschina.net/lakche/timeCMS-free.git" target="_blank">开源中国仓库</a> <a class="btn btn-primary" href="https://github.com/lakche/timeCMS-free.git" target="_blank">github仓库</a></p>
                    </div>
                    <div class="item text-center">
                        <h1>时光如水 岁月如歌</h1>
                        <p>工作累了吧，进来休息休息吧</p>
                        <p><a class="btn btn-primary" href="{{ url('page/building') }}">开始静思</a> <a class="btn btn-primary" href="{{ url('page/building') }}">开始缅怀</a></p>
                    </div>
                </div>
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">上一页</span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">下一页</span>
                </a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                @foreach($types as $tp)
                    <div class="col-sm-6 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">{{ $tp->title }}<span class="pull-right"><a href="{{ url('category',$tp->id) }}">更多>></a></span></div>
                            <div class="panel-body">
                                @if($articles = Theme::article_data(5,byId,findCategory,$tp->id))
                                    <div class="list-group">
                                        @foreach($articles as $article)
                                            <a href="{{ url('article',$article->id) }}" class="list-group-item">{{ $article->title }}<span class="pull-right">{{ $article->maketime() }}</span></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                    <div class="col-sm-12 col-md-12" id="persons">
                        <div class="panel panel-primary">
                            <div class="panel-heading">荣誉殿堂
                                <span class="pull-right"><a href="{{ url('person') }}">更多>></a></span>
                            </div>
                            <div class="panel-body">
                                @if($persons = Theme::person_data(4,byPoint))
                                @foreach($persons as $person)
                                    <div class="col-sm-6 col-md-3">
                                        <div class="thumbnail">
                                            <div class="pic">
                                                <p><a href="{{ url('person',$person->id) }}">
                                                    <img src="{{ $person->getHead() }}" alt="{{ $person->name }}">
                                                </a></p>
                                                <span><s></s><div>{{ $person->point }}</div></span>
                                            </div>
                                            <div class="caption text-center">
                                                <a href="{{ url('person',$person->id) }}"><h3>{{ $person->name }}</h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12" id="person">
                        <div class="panel panel-primary">
                            <div class="panel-heading">作品展示
                                <span class="pull-right"><a href="{{ url('project') }}">更多>></a></span>
                            </div>
                            <div class="panel-body" id="project">
                                @if($projects = Theme::project_data(4,bySort))
                                @foreach($projects as $project)
                                    <div class="col-sm-6 col-md-3">
                                        <div class="thumbnail">
                                            <div class="pic">
                                                <p>
                                                    <a href="{{ url('project',$project->id) }}">
                                                        <img src="{{ $project->getCover() }}" alt="{{ $project->title }}">
                                                    </a>
                                                </p>
                                            </div>
                                            <div class="caption text-center">
                                                <a href="{{ url('project',$project->id) }}">
                                                    <h3>{{ str_limit($project->title,14,'...') }}</h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-sm-12 col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">关于荣誉殿堂</div>
                                <div class="panel-body">
                                    <p class="info">凡是帮助我改进本系统或提出意见被采纳者，均可以加入荣誉殿堂，展示您的风采。</p>
                                    <p>
                                        <a href="https://git.oschina.net/lakche/timeCMS-free.git" target="_blank" class="btn btn-default">开源中国仓库</a>
                                        <a href="https://github.com/lakche/timeCMS-free.git" target="_blank" class="btn btn-default pull-right">github仓库</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">关于静思堂</div>
                                <div class="panel-body">
                                    <p class="info">在这里你可以把烦恼记录下来，经过一个星期的沉淀之后，你会发现一切都是浮云。</p>
                                    <p><a href="{{ url('page/building') }}" class="btn btn-default btn-block">开始静思</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">关于通天塔</div>
                                <div class="panel-body">
                                    <p class="info">美丽的灵魂已安息在天堂，我们的思绪却久久不能平静，即使无数的岁月，总有一些人值得缅怀。</p>
                                    <p><a href="{{ url('page/building') }}" class="btn btn-default btn-block">开始缅怀</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection