@extends('layouts.admin')
@section('content')
    <div class="table-top-button-box">
        <a href="{{ route('roles.index') }}" class="layui-btn layui-btn-small">
            <i class="layui-icon">&#xe62d;</i> 返回
        </a>
    </div>
    <div class="layui-field-box">
        <form class="layui-form" action="{{ route('roles.perm.store', ['id'=>$id]) }}" method="POST">
            {!! csrf_field() !!}

            <div style="float: left; width: 46%; padding: 0 10px;">
                <fieldset class="layui-elem-field layui-field-title">
                    <legend style="margin:0 auto;">选择控制权限</legend>
                </fieldset>

                <div class="layui-form-item" id="permissions-box">
                    <div class="layui-input-block" style="margin-left: 50px;">
                        {!! getCheckboxTree($permissions, 'permission', $perm) !!}
                    </div>
                </div>
            </div>
            <div id="middlebox" style="float: left; height: 300px; margin-top: 50px;; border-left: 1px solid #e2e2e2;"></div>
            <div style="float: left; width: 46%; padding: 0 10px;">
                <fieldset class="layui-elem-field layui-field-title">
                    <legend style="margin:0 auto;">选择菜单权限</legend>
                </fieldset>

                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 50px;">
                        {!! getCheckboxTree($menus, 'menu', $menu) !!}
                    </div>
                </div>
            </div>
            <div class="layui-form-item" style="text-align: center;">
                <div class="layui-input-block" style="margin-left:0;">
                    <button class="layui-btn" lay-submit lay-filter="go">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form', 'jquery'], function(){
            var form = layui.form(), $ = layui.jquery
            $(function($){
                var pbh = $('#permissions-box').height()
                $('#middlebox').height(pbh/1.5).css({'margin-top':30+(pbh-pbh/1.5)/2});


            })
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