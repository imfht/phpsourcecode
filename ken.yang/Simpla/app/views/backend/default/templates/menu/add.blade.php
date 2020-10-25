@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加菜单</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post')) }}
        @if($errors->all())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </div>
        @endif
        <div class="form-group">
            <label>标题<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="title" class="form-control" maxlength="64" required="" value='{{Input::old('title')}}'>
        </div>
        <div class="form-group">
            <label>描述</label>
            <textarea name="description" class="form-control" maxlength="256">{{Input::old('description')}}</textarea>
        </div>
        <div class="form-group">
            <label>URL地址<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="url" class="form-control" maxlength="256" required="" value='{{Input::old('url')}}'>
            <small class="help-block">如果是站内链接，不需要写域名，如果是站外地址请填写域名</small>
        </div>
        <div class="form-group">
            <label>菜单级别</label>
            <select name="menu_class" class="form-control">
                <option value="0">无</option>
                {{Base::outputOptionTree($menus)}}
            </select>
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

