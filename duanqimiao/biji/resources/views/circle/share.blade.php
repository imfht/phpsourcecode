@extends('circle.layout')
@section('script')
    <script type="text/javascript" src="{{ URL::asset('/') }}js/circle.js"></script>
    <link rel="stylesheet" href="{{ asset('/css/circle.css') }}">
@endsection
@section('content')
    <h3><a style=";color: #666666" href="/circle/">笔友圈</a> <small>» 我的分享 </small></h3>
    @include('partials.success')
    <table id="bijis-table" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>标 题</th>
            <th data-sortable="false">操 作</th>
        </tr>
        </thead>
        <tbody class="tbody">
        @foreach($shares as $share)
            <tr>
                <td>
                    {{ $share->title }}
                </td>
                <td>
                    <input type="hidden" name="share_id" value="{{ $share->id }}"/>
                    <a href="{{ URL::to('circle/'. $share->id . '/edit') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-eye"></i>查 看
                    </a>
                    <a><button id="share-delete-btn" type="button" class="btn btn-danger btn-sm">删除</button></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection