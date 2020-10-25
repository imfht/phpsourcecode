@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <div class="container-fluid" id="main">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    @include($theme.'.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">系统统计</div>
                        <div class="panel-body">
                            <p class="clearfix"></p>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.users.index') }}" class="list-group-item active">最近五个用户<span class="badge">共{{ $user_num }}个</span></a>
                                @foreach($users as $user)
                                    <a href="#" class="list-group-item">{{ $user->name }}</a>
                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.persons.index') }}" class="list-group-item active">最近五个人物<span class="badge">共{{ $person_num }}个</span></a>
                                @foreach(Theme::person_data(5) as $person)
                                    <a href="{{ url('person',$person->id) }}" class="list-group-item" target="_blank">{{ $person->name }}</a>
                                @endforeach
                            </div>
                            <p class="clearfix"></p>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.articles.index') }}" class="list-group-item active">最近五篇文章<span class="badge">共{{ $article_num }}篇</span></a>
                                @foreach(Theme::article_data(5) as $article)
                                    <a href="{{ url('article',$article->id) }}" class="list-group-item" target="_blank">{{ str_limit($article->title,32,'...') }}<span class="pull-right">{{ $article->created_at }}</span></a>
                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.projects.index') }}" class="list-group-item active">最近五个项目<span class="badge">共{{ $project_num }}篇</span></a>
                                @foreach(Theme::project_data(5) as $project)
                                    <a href="{{ url('project',$project->id) }}" class="list-group-item" target="_blank">{{ str_limit($project->title,32,'...') }}<span class="pull-right">{{ $project->created_at->format('Y-m-d') }}</span></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection