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

    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">点击触发上传</li>
            <li>Base64数据上传</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show" id="upload_touch_id">
                <div class="layui-form-item">
                    <label class="layui-form-label">点击触发上传</label>
                    <button type="button" class="layui-btn" id="upload_touch">
                        <i class="layui-icon">&#xe67c;</i>上传图片
                    </button>
                    <div class="result">

                    </div>
                </div>
            </div>
            <div class="layui-tab-item" id="upload_base64_id">
                <div class="layui-form layui-form-pane" onsubmit="return false;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">上传图片</label>
                        <input class="layui-btn" type="file" id="upload_base64">
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input class="layui-btn" id="preview" value="预览图片信息">
                            <input class="layui-btn" id="success" value="开始上传">
                        </div>
                    </div>
                    <div class="result"></div>
                </div>
            </div>
        </div>
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
            //上传图片
            //执行实例
            var uploadInst = upload.render({
                elem: '#upload_touch' //绑定元素
                , url: '{{route('upload_touch')}}' //上传接口
                , done: function (rev) {
                    //上传完毕回调
                    $("#upload_touch_id .result").empty();
                    if (rev.status === 0) {
                        $("#upload_touch_id .result").append('<p><a target="_blank" href="' + rev.data + '">' + rev.data + '</a></p>');
                        $("#upload_touch_id .result").append('<img src="' + rev.data + '"/>');
                    } else {
                        layer.alert(rev.msg);
                    }
                }
                , error: function () {
                    //请求异常回调
                }
            });
            //上传图片2
            $("#preview").click(function () {
                var load_index = layer.load(1);
                $("#upload_base64_id .result").empty();
                var file = $("#upload_base64")[0].files[0];
                var fileObj = checkFile(file);
                var reader = new FileReader();
                reader.onload = function () {
                    var base64 = reader.result;
                    var img = "<img src='" + base64 + "'/>";
                    $("#upload_base64_id .result").append(img);
                    $("#upload_base64_id .result").append(base64);
                    layer.close(load_index);
                };
                reader.readAsDataURL(fileObj);
            });
            //上传图片2
            $("#success").click(function () {
                var load_index = layer.load(1);
                $("#upload_base64_id .result").empty();
                var file = $("#upload_base64")[0].files[0];
                var fileObj = checkFile(file);
                var reader = new FileReader();
                reader.onload = function () {
                    var base64 = reader.result;
                    $.post('{{route('upload_base64')}}', {base64: base64}, function (rev) {
                        if (rev.status === 0) {
                            var a = "<a target='_blank' href='" + rev.data + "'>点击查看图片</a>";
                            var img = "<img src='" + rev.data + "' alt=''/>";
                            $("#upload_base64_id .result").append('<hr/>');
                            $("#upload_base64_id .result").append(a);
                            $("#upload_base64_id .result").append('<hr/>');
                            $("#upload_base64_id .result").append(img);
                        }
                        layer.close(load_index);
                    }, 'json');
                };
                reader.readAsDataURL(fileObj);
            });

            /**
             * 对图片做判断
             * @param file
             * @returns {*}
             */
            function checkFile(file) {
                var maxSize = 2 * 1024 * 1024;//最大只允许1m大小
                if (typeof file === 'undefined') {
                    layer.alert("请选择图片");
                    return false;
                }
                console.log(file);
                //判断大小
                var size = file.size;
                var sizeM = size / (1024 * 1024);
                var sizeInfo = "图片实际大小: " + sizeM.toFixed(2) + "M<br/>";
                $("#result").append(sizeInfo);
                if (size > maxSize) {
                    layer.alert("您上传的图片太大,请控制在" + maxSize / (1024 * 1024) + 'M');
                    return false;
                }
                return file;
            }

            // 验证
            form.verify({
                username: function (value) {
                    if (value === "") {
                        return "请输入用户名";
                    }
                },
                password: function (value) {
                    if (value === "") {
                        return "请输入密码";
                    }
                },
                captcha: function (value) {
                    if (value === "") {
                        return "请输入验证码";
                    }
                }
            });
            // 提交监听
            form.on('submit(success)', function (data) {
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>
@stop