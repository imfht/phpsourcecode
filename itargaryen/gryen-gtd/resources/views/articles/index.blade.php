@extends('layouts._default', [
    'siteTitle' => '笔记',
    'module' => 'article-list',
    'noJsLoad' => true
])
@section('content')
   @include('articles._list')
@stop