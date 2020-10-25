@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>添加管理员</h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('admins.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 管理员管理</button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="{{ route('admins.update',$admin->id) }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                {!! csrf_field() !!}
                {{method_field('PATCH')}}
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户名：</label>
                    <div class="input-group col-sm-2">
                        <input type="text" class="form-control" name="name" value="{{$admin->name}}" required data-msg-required="请输入用户名">
                        @if ($errors->has('name'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('name')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="password" class="form-control" name="password">
                        @if ($errors->has('password'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('password')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">头像：</label>
                    <div class="input-group col-sm-2">
                        <input type="file" class="form-control" name="avatr">
                        @if ($errors->has('avatr'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('avatr')}}</span>
                        @endif
                        <span class="view picview ">
                           <img id="thumbnail-avatar" class="thumbnail img-responsive" src="{{$admin->avatr}}" width="100" height="100">
                        </span>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">所属角色：</label>
                    <div class="input-group col-sm-2">
                        @php
                            $ruleids = $admin->roles->pluck('id')->toArray();
                        @endphp
                        @foreach($roles as $k=>$item)
                            <label><input type="checkbox" name="role_id[]" value="{{$item->id}}" @if(in_array($item->id,$ruleids)) checked="checked" @endif> {{$item->name}}</label><br/>
                        @endforeach
                        @if ($errors->has('role_id'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('role_id')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="input-group col-sm-1">
                        <select class="form-control" name="status">
                            <option value="1" @if($admin->status == 1) selected="selected" @endif>正常</option>
                            <option value="2" @if($admin->status == 2) selected="selected" @endif>锁定</option>
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
            </form>
        </div>
    </div>
</div>
@endsection