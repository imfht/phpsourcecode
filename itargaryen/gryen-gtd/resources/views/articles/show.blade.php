@extends('layouts._default', [
'module' => 'article-show',
'vue' => true
])
@section('content')
<article class="t-rtcl-box">
    <h1 class="text-center t-rtcl-ttl" id="tArticleTitle">{{ $article->title }}</h1>
    <hr>
    <section class="t-rtcl-content">
        {!! $article->content !!}
    </section>
    <section class="t-rtcl-tags">
        @foreach($article->tagArray as $tag)
        <a class="badge badge-dark" href="{{ action('ArticlesController@tag', ['tag' => $tag]) }}">{{ $tag }}</a>
        @endforeach
    </section>
    <footer class="clearfix small border border-secondary p-3">
        <p class="mb-1">
            <svg class="icon" aria-hidden="true">
                <use xlink:href="#icon-shijian"></use>
            </svg>
            <span>最后更新：</span><span><time pubdate>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $article->updated_at)->toDateString() }}</time></span>
        </p>
        <p class="mb-1">
            <svg class="icon" aria-hidden="true">
                <use xlink:href="#icon-copyright"></use>
            </svg>
            <span>版权声明：</span><span>自由转载-非商用-非衍生-保持署名（<a target="_blank" href="https://creativecommons.org/licenses/by-nc-nd/3.0/deed.zh">创意共享3.0许可证</a>）</span>
        </p>
        <p class="mb-0"><a href="mailto:targaryen@gryen.com" class="text-dark">
                <svg class="icon" aria-hidden="true">
                    <use xlink:href="#icon-email"></use>
                </svg>
                <span>与我联系：</span><span><u>targaryen@gryen.com</u></span>
            </a></p>
    </footer>
</article>
<more-articles></more-articles>
@if (Auth::check())
<div class="form-group tar-artl-ssbtn">
    <div class="btn-group-vertical">
        <a href="{{ action('ArticlesController@edit', ['id' => $article->id]) }}" class="btn btn-primary">编辑</a>
        <button class="btn btn-outline-primary" disabled>{{ $article->views }}</button>
    </div>
</div>
@endif
@include('articles._full-screen-img')
@stop
