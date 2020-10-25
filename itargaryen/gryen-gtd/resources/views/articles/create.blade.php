@extends('layouts._default', [
    'module' => 'article-edit',
    'siteTitle' => '新建笔记',
])
@section('content')
    <div class="tar-article-form">
        {!! Form::open(['action' => 'ArticlesController@store', 'enctype' => 'multipart/form-data']) !!}
        @include('articles._form')
        {!! Form::close() !!}
    </div>
@stop