@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                    <div class="panel panel-primary">
                        <div class="panel-heading">{{ $type->title }}</div>
                        <div class="panel-body">
                            @if($type->info != '')
                                <div class="alert alert-success" role="alert">{{ $type->info }}</div>
                            @endif
                            @if($articles = $type->articles->sortByDesc('id')->take(9))
                                <div class="list-group">
                                    @foreach($articles as $article)
                                        <a href="{{ url('article',$article->id) }}" class="list-group-item">{{ $article->title }}<span class="pull-right">{{ $article->maketime() }}</span></a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @foreach($subs as $sub)
                        <div class="panel panel-primary">
                            <div class="panel-heading">{{ $sub->title }}<span class="pull-right"><a href="{{ url('category',$sub->id) }}">更多>></a></span></div>
                            <div class="panel-body">
                                @if($sub->info != '')
                                    <div class="alert alert-success" role="alert">{{ $sub->info }}</div>
                                @endif
                                @if($articles = $sub->articles->sortByDesc('id')->take(9))
                                    <div class="list-group">
                                        @foreach($articles as $article)
                                            <a href="{{ url('article',$article->id) }}" class="list-group-item">{{ $article->title }}<span class="pull-right">{{ $article->maketime() }}</span></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-sm-3">
                    @include($theme.'.category.right')
                </div>
            </div>
        </div>
    </div>
@endsection