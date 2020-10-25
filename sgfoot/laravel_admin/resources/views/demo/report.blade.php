@extends('layout')
@section('title', $title)
@section('style')

@stop
@section('body')
    <blockquote class="layui-elem-quote">{{$title}}</blockquote>
    <div class="layui-form-item">
        <a class="layui-btn ml5" layTips="点击返回列表|3|#3595CC" href="{{route('list')}}"><i
                    class="fa fa-plus-circle fa-fw"></i>返回列表</a>
    </div>
    <div class="layui-collapse">
        <div class="layui-colla-item ">
            <h2 class="layui-colla-title">帮助</h2>
            <div class="layui-colla-content layui-show">
                <ul>
                    <li><span class="layui-badge layui-bg-green mr5">1</span>带*号的必填</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="layui-form layui-form-pane" style="padding-top: 10px; ">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" id="upload_file">
                    <i class="layui-icon">&#xe67c;</i>上传图片
                </button>
                <a class="layui-btn" id="import" href="{{route('export')}}">导出</a>
            </div>
        </div>
        <div class="result"></div>
    </div>
@stop
@section('script')
    <script type="text/javascript">

        layui.use(['form', 'layer', 'element', 'upload'], function () {
            // 操作对象
            var form = layui.form
                , layer = layui.layer
                , upload = layui.upload
                , $ = layui.jquery;

            $(document).keyup(function (event) {
                if (event.keyCode === 13) {
                    $("button").trigger("click");
                }
            });
            //上传文件
            var upload_file = upload.render({
                elem: '#upload_file' //绑定元素
                , accept: 'file'
                , url: '{{route('import')}}' //上传接口
                , done: function (rev) {
                    //上传完毕回调
                    layer.alert(JSON.stringify(rev.data), {shift: 1});
                    return false;
                }
                , error: function (error) {
                    //请求异常回调
                    console.log(error);
                }
            });

        });
    </script>
@stop