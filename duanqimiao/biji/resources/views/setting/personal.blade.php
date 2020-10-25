@extends('auth.layout')
@section('title')
    <strong>更换头像</strong>
@endsection
@section('script')
    <style>
        body{
            background: #fff;
            font-family: gotham,helvetica,arial,sans-serif;
            color: #4a4a4a;
            font-size: 14px;
            font-weight: 400;
        }
        .p{
            margin-bottom: 20px;
        }
        .form-group{
            padding-left: 15px;
        }

    </style>
@endsection
@section('content')
    <form action="{{ url('/uploads') }}" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
        <p class="p">选择图片</p>
        <div class="form-group">
            <input type="file" name="photo" />
        </div>
        <div class="form-group">
            <a href="{{ url('/biji/') }}"><input type="button" value="取消" class="btn btn-default" /></a>
            <input type="submit" value="提交" class="upload btn btn-primary" />
        </div>
        @include('partials.errors')
    </form>
@endsection
