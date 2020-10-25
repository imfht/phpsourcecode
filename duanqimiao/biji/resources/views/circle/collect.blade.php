@extends('circle.layout')
@section('script')
    <script type="text/javascript" src="{{ URL::asset('/') }}js/circle.js"></script>
@endsection
@section('content')
    <h3><a style=";color: #666666" href="/circle/">笔友圈</a> <small>» 我的收藏 </small></h3>
    @include('partials.success')

    <table id="bijis-table" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="display: none">id</th>
            <th>标 题</th>
            <th data-sortable="false">操 作</th>
        </tr>
        </thead>
        <tbody class="tbody">

        @foreach($collects as $collect)
            <div style="display: none">{{ $collect_bijis = \App\Biji::where('id',$collect->biji_id)->where('share',1)->get() }}</div>
            @foreach($collect_bijis as $bijis)
                <tr>
                    <td style="display: none;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    </td>
                    <td>
                        {{ $bijis->title }}
                    </td>
                    <td>
                        <input type="hidden" name="bijis_id" value="{{ $bijis->id }}"/>
                        <a href="{{ URL::to('circle/'. $bijis->id . '/edit') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i>查 看
                        </a>
                        <a><button id="collect-delete-btn" type="button" class="btn btn-danger btn-sm">删除</button></a>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endsection