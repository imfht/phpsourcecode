@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加权限</legend>
        <div class="table-top-button-box">
            <a href="{{ route('permissions.index') }}" class="layui-btn layui-btn-small">
                <i class="layui-icon">&#xe62d;</i> 返回
            </a>
        </div>
        <div class="layui-field-box">
            <form class="layui-form" action="{{ route('permissions.store') }}" method="POST">
                {!! csrf_field() !!}
                <div class="layui-form-item">
                    <label class="layui-form-label">选择父类</label>
                    <div class="layui-input-inline" style="width:30%">
                        <select name="pid">
                            <option value="0" selected>后台管理</option>
                            @foreach($permissions as $permission)
                            <option value="{{ $permission->id }}">{{ str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $permission->lev).'|  -  -  '.$permission->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-form-mid layui-word-aux">权限父子只有2级</div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">权限名称</label>
                    <div class="layui-input-block" style="width:30%">
                        <input type="text" name="display_name"   placeholder="请输入权限名称" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">权限标识</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="text" name="name"   placeholder="路由" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">请填写路由别名 例: <code>admin.home</code></div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">排序</label>
                    <div class="layui-input-inline">
                        <input type="text" name="sort" autocomplete="off" class="layui-input" value="0">
                    </div>
                    <div class="layui-form-mid layui-word-aux">数字越大,排序越靠前</div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="go">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
@endsection

@section('script')
    <script>
        layui.use('form', function(){
            var form = layui.form();
            //监听提交
            form.on('submit(go)', function(data){
                //layer.msg(JSON.stringify(data.field));
                if (!confirm('确定要添加此内容吗?')) {
                    return false;
                }
            });
        });
    </script>
@endsection