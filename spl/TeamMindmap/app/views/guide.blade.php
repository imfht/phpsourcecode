@extends('layout.master')
{{--add css file here--}}

{{--add script file here--}}
@section('scripts')
{{HTML::script('packages/bower/requirejs/require.js',array('data-main'=>'../js/guide-main'))}}
@stop

@section("title")
功能介绍
@stop

@section("content")
    <br/><br/><br/><br/><br/><br/>
    <p>this is guide content</p>
@stop

