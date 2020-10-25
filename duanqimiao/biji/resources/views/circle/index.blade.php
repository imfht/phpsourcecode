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
    <style type="text/css">
        .b_10_3 {
            overflow: hidden; text-align:center;
            background:#999;
        }
        .b_10_3 span {
            float: left; font-size:16px; line-height:2em;
        }
        .b_10_3 span.bold {
            font-weight:bold;
        }
        .b_10_3 span:nth-child(1) {
            width: 35%;
            text-align: center;
        }
        .b_10_3 span:nth-child(2) {
            width: 30%;
            text-align: left;
        }
        .b_10_3 span:nth-child(3) {
            width: 35%;
            text-align: left;
        }
        .b_10_3 span:nth-child(4) {
            width: 35%;
            text-align: right;
        }
        .b_10_3 span:nth-child(5) {
            width: 25%;
            text-align: right;
        }
        .b_10_3 span:nth-child(6) {
            width: 25%;
            text-align: right;
        }
        .b_10_3 span:nth-child(7) {
            width: 50%;
            text-align: center;
        }
        .b_10_3 span:nth-child(8) {
            width: 30%;
            text-align: center;
        }
        .b_10_3 span:nth-child(9) {
            width: 50%;
            text-align: right;
        }
        .b_10_3 span:nth-child(10) {
            width: 40%;
            text-align: right;
        }
    </style>

    <script type="text/javascript" src="{{ URL::asset('/') }}js/tagcanvas.min.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            try {
                var i, et = document.getElementById('tags').childNodes;
                TagCanvas.Start('myCanvas', 'tags', {
                    textColour: '#222',
                    outlineColour: '#fff',
                    reverse: true,
                    depth: 0.8,
                    dragControl: true,
                    decel:0.95,
                    maxSpeed: 0.05,
                    initial: [-0.2, 0]
                });
            } catch (e) {
                // something went wrong, hide the canvas container
                //document.getElementById('myCanvasContainer').style.display = 'none';
            }
        };
    </script>

@endsection
@section('content')
    <div class="b_10_3">

        <canvas width="200" height="200" id="myCanvas"></canvas>

        <div id="tags">
            @foreach($tags as $tag)
                <a href="{{ URL::to('/tagsCloud/'.$tag->id) }}"> {{ $tag->tag }} </a>
            @endforeach
        </div>

    </div>
    <br/>
        <table id="bijis-table" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>标 题</th>
                <th data-sortable="false">操 作</th>
            </tr>
            </thead>
            <tbody class="tbody">
            @foreach ($share_biji as $biji)
                <tr>
                    <td class="title">{{ $biji->title }}</td>
                    <td>
                        <a href="{{ URL::to('circle/'. $biji->id . '/edit') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i>查 看
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $share_biji->render() !!}

@stop