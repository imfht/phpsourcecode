@extends('circle.layout')
@section('script')
    <script language="JavaScript" src="{{ URL::asset('/') }}js/circle.js"></script>

    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.all.min.js"></script>
    <!-- 选择语言 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}lang/zh-cn/zh-cn.js"></script>
    <!-- 实例化编辑器代码 -->
    <script type="text/javascript">
        $(function(){
            var ue = UE.getEditor('container');
        });
    </script>
@endsection
@section('content')
    @foreach($tagsCloud as $tag)
    <h3><a style=";color: #666666" href="/circle/">笔友圈</a> <small>» 标签--{{ $tag->tag }} </small></h3>
    @endforeach
    <table id="bijis-table" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>标 题</th>
            <th data-sortable="false">操 作</th>
        </tr>
        </thead>
        <tbody class="tbody">
        @foreach($bijis_id as $biji_id)
            <div style="display: none">
                {{ $share_biji = \App\Biji::where('id',$biji_id->biji_id)->where('share',1)->get() }}
            </div>
            @foreach ($share_biji as $biji)
                <tr>
                    <td>{{ $biji->title }}</td>
                    <td>
                        <a href="{{ URL::to('circle/'. $biji->id . '/edit') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i>查 看
                        </a>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@stop