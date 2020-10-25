@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">编辑菜单</h3>
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
            <input type="text" name="title" class="form-control" value="{{Input::old('title')?Input::old('title'):$current_menu['title']}}" maxlength="64" required="">
        </div>
        <div class="form-group">
            <label>描述</label>
            <textarea name="description" class="form-control" maxlength="256">{{Input::old('description')?Input::old('description'):$current_menu['description']}}</textarea>
        </div>
        <div class="form-group">
            <label>URL地址<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="url" class="form-control" value="{{Input::old('url')?Input::old('url'):$current_menu['url']}}" maxlength="256" required="">
            <small class="help-block">如果是站内链接，不需要写域名，如果是站外地址请填写域名。</small>
        </div>
        <div class="form-group">
            <label>菜单级别</label>
            <select name="menu_class" class="form-control">
                <option value="0">无</option>
                {{Base::outputOptionTree($menus,Input::old('menu_class')?Input::old('menu_class'):$current_menu['pid'])}}
            </select>
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>
@stop