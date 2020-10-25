@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    @include($theme.'.user.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">个人收藏</div>
                        <div class="panel-body">
                            <div class="list-group">
                                @foreach ($articles as $article)
                                    <a href="{{ route('article.show',$article->article_id) }}" class="list-group-item" target="_blank">
                                        {{ $article->article()->title }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="pagination" style="width: 100%; text-align:center;">{!! $articles->render() !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection