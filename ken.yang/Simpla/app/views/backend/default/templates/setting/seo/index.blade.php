@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">SEO管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post')) }}

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">首页SEO</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>标题</label>
                    <input type="text" name="seo_title" class="form-control" maxlength="256" value="{{Input::old('seo_title')?Input::old('seo_title'):$home_seo['title']}}">
                    <label>描述</label>
                    <input type="text" name="seo_description" class="form-control" maxlength="256" value="{{Input::old('seo_description')?Input::old('seo_description'):$home_seo['description']}}">
                    <label>关键字</label>
                    <input type="text" name="seo_keywords" class="form-control" maxlength="256" value="{{Input::old('seo_keywords')?Input::old('seo_keywords'):$home_seo['keywords']}}">
                </div>
                <div class="form-group">
                    <small class="help-block">注：其他SEO设置请在添加/编辑内容或者分类下面设置</small>
                </div>
            </div>
        </div>

        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->
@stop