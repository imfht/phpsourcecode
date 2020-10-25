@extends('admin.layouts.layout')

@section('css')
    <style>
        .animated{-webkit-animation-fill-mode: none;}
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>编辑权限</h5>
        </div>
        <div class="ibox-content">
            <a href="{{route('rules.index')}}">
                <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 权限管理
                </button>
            </a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="{{route('rules.update',$rule->id)}}" method="POST">
                <div class="form-group">
                    <label class="col-sm-2 control-label">上级权限：</label>
                    <div class="col-sm-2">
                        <select name="parent_id" class="form-control">
                            <option value="0">顶级权限</option>
                            @foreach($rules as $k=>$item)
                                <option value="{{$item['id']}}" @if($rule->parent_id == $item['id']) selected="selected"@endif >{{$item['_name']}}</option>
                            @endforeach
                            @if ($errors->has('parent_id'))
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('parent_id')}}</span>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">权限名称：</label>
                    <div class="col-sm-3">
                        <input type="text" name="name" value="{{$rule->name}}" class="form-control" required data-msg-required="请输入权限名称">
                        @if ($errors->has('name'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('name')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">菜单图标：</label>
                    <div class="col-sm-3">
                        <input type="text" name="fonts" id="fonts" onclick="showicon()" value="{{$rule->fonts}}"  placeholder="菜单图标" class="form-control">
                        @if ($errors->has('fonts'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('fonts')}}</span>
                        @else
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 采用Font Awesome字体图标</span> </div>
                        @endif
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">权限路径：</label>
                    <div class="col-sm-3">
                        <input type="text" name="route" value="{{$rule->route}}" class="form-control">
                        @if ($errors->has('route'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('route')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">排序：</label>
                    <div class="col-sm-1">
                        <input type="text" name="sort" value="{{$rule->sort}}" required class="form-control">
                        @if ($errors->has('sort'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('sort')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否隐藏：</label>
                    <div class="col-sm-2">
                        <select name="is_hidden" class="form-control">
                            <option value="0" @if($rule->is_hidden == 0)selected="selected" @endif >显示</option>
                            <option value="1" @if($rule->is_hidden == 1)selected="selected" @endif>隐藏</option>
                        </select>
                        @if ($errors->has('is_hidden'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('is_hidden')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="col-sm-2">
                        <select name="status" class="form-control">
                            <option value="1" @if($rule->status == 1)selected="selected" @endif>启用</option>
                            <option value="0" @if($rule->status == 0)selected="selected" @endif>禁用</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('status')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
                {{method_field('PATCH')}}
                {{csrf_field()}}
            </form>
        </div>
    </div>
</div>
<div id="functions" style="display: none;">
    @include('admin.rules.fonticon')
</div>
@section('footer-js')
    <script>

        function showicon(){
            layer.open({
                type: 1,
                title:'点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }
        $('.fontawesome-icon-list .fa-hover').find('a').click(function(){
            var str=$(this).text();
            $('#fonts').val( $.trim(str));
            layer.closeAll();
        })
    </script>
@endsection
@endsection
