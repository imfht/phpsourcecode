@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid" id="project">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 left">
                    <h2>{{ $project->title }}</h2>
                    <div class="info">
                        <div>项目分类：<span><a href="{{ url('project/type',$project->category_id) }}">{{ $project->category->title }}</a></span></div>
                        <div>发布时间：<span>{{ $project->created_at->format('Y-m-d') }}</span></div>
                        <div>浏览量：<span>{{ $project->views }}</span></div>
                        <div>项目周期：<span>{{ $project->period }}天</span></div>
                        <div>项目费用：<span>{{ $project->cost }}元</span></div>
                    </div>
                    <div class="tag">
                        @if($project->tag != '[""]')
                            @foreach( json_decode($project->tag) as $tag )
                                <div><i class="glyphicon glyphicon-heart"></i>{{ $tag }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="panel panel-primary person">
                        <div class="panel-heading">参与人员</div>
                        <div class="panel-body">
                            @if($persons = $project->persons())
                                @foreach($persons as $person)
                                    <div class="col-xs-6">
                                        <div class="thumbnail">
                                            <div class="pic">
                                                <a href="{{ url('person',$person->id) }}">
                                                    <img src="{{ $person->getHead() }}" alt="{{ $person->name }}">
                                                </a>
                                            </div>
                                            <div class="text-center">
                                                <a href="{{ url('person',$person->id) }}">{{ $person->name }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="panel panel-primary speed">
                        <div class="panel-heading">项目进度</div>
                        <div class="panel-body">
                            @if($project->speed)
                                @foreach(json_decode($project->speed) as $speed)
                                    <p><span>{{ $speed->time }}</span> {{ $speed->event }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-9 right">
                        @if($project->cover != '')
                            <div class="cover text-center"><img src="{{ $project->cover }}" alt="{{ $project->title }}"></div>
                        @endif
                        {!! $project->text !!}
                    <div class="page-footer clearfix">
                        <p>相关阅读：</p>
                        @if($projects = $type->projects->sortByDesc('id')->take(6))
                            @foreach($projects as $project)
                            <div class="col-sm-6">
                                <a href="{{ url('project',$project->id) }}">{{ $project->title }}</a>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.right').css('min-height',$('.left').height()+'px');
    </script>
@endsection