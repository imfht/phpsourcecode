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
    <form class="layui-form layui-form-pane" onsubmit="return false;">
        <div class="layui-form-item">
            <label class="layui-form-label">商品名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verType="tips" required lay-verify="required"
                       placeholder="请输入商品名称"
                       autocomplete="new-password" class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">副标题</label>
            <div class="layui-input-inline">
                <input type="text" name="subhead" placeholder="请输入副标题"
                       autocomplete="new-password" class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item" id="value_id">
            <label class="layui-form-label">厨窗图</label>
            <div class="layui-input-inline">
                <div class="layui-upload">
                    <input type="button" class="layui-btn" id="upload_img" value="上传图片"/>
                    <div class="layui-upload-list">
                        <img class="layui-upload-img" style="width:300px;height: auto;" id="preId" src="">
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择分类</label>
            <div class="layui-input-inline">
                <select name="category_id" lay-verType="tips" required lay-verify="required">
                    <option value="">选择分类</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <a href="#" target="_blank" class="layui-btn">添加分类</a>
        </div>
        <div class="layui-form-item" id="value_id">
            <label class="layui-form-label">售价</label>
            <div class="layui-input-inline">
                <input type="text" name="price" placeholder="请输入售价" lay-verType="tips" required lay-verify="required"
                       autocomplete="new-password" class="layui-input" value="">
            </div>
            <div class="layui-form-mid layui-word-aux">元</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">上架状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="is_shop" lay-skin="switch"
                       value="1" lay-text="上架|下架" lay-verType="tips" required lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否送停车码</label>
            <div class="layui-input-block">
                <input type="checkbox" name="is_code" lay-skin="switch" value="1"
                 lay-text="送|不送" lay-verType="tips" required
                       lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item" id="value_id">
            <label class="layui-form-label">库存</label>
            <div class="layui-input-inline">
                <input type="number" name="number" placeholder="请输入库存" lay-verType="tips" required lay-verify="required"
                       autocomplete="new-password" class="layui-input" value="">
            </div>
        </div>
        <div class="layui-form-item" id="value_id">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-inline">
                <textarea name="desc" placeholder="请输入备注内容" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input style="width:105px;" class="layui-btn" lay-submit lay-filter="success" value="完成">
                <a href="javascript:window.history.back()" class="layui-btn layui-btn-primary">返回</a>
            </div>
        </div>
    </form>
@stop
@section('script')
    <script type="text/javascript">

        layui.use(['form', 'layer', 'table', 'element'], function () {
            // 操作对象
            var form = layui.form
                , layer = layui.layer
                , $ = layui.jquery;

            $(document).keyup(function (event) {
                if (event.keyCode === 13) {
                    $("button").trigger("click");
                }
            });
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