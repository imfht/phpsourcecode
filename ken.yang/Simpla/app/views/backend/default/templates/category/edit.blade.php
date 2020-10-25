@extends('BackTheme::layout.master')
@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">编辑分类</h3>
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
            <input type="text" name="title" class="form-control" value="{{Input::old('title')?Input::old('title'):$current_category['title']}}" maxlength="64" required="">
        </div>
        <div class="form-group">
            <label>描述</label>
            <textarea name="description" class="form-control" maxlength="256">{{Input::old('description')?Input::old('description'):$current_category['description']}}</textarea>
        </div>
        <div class="form-group">
            <label>分类级别</label>
            <select name="category_class" class="form-control">
                <option value="0">无</option>
                {{Base::outputOptionTree($categories,Input::old('category_class')?Input::old('category_class'):$current_category['pid'])}}
            </select>
        </div>
        <!--其他设置-->
        <div id="accordion" class="panel-group">
            <!--SEO优化设置-->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#collapseThree" data-parent="#accordion" data-toggle="collapse" class="collapsed">SEO优化设置</a>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="collapseThree" style="height: 0px;">
                    <div class="panel-body">
                        <div class="form-group">
                            <label>标题</label>
                            <input type="text" name="seo_title" class="form-control" maxlength="256" value="{{Input::old('seo_title')?Input::old('title'):$current_category['seo']['title']}}">
                            <label>描述</label>
                            <input type="text" name="seo_description" class="form-control" maxlength="256" value="{{Input::old('seo_description')?Input::old('title'):$current_category['seo']['description']}}">
                            <label>关键字</label>
                            <input type="text" name="seo_keywords" class="form-control" maxlength="256" value="{{Input::old('seo_keywords')?Input::old('title'):$current_category['seo']['keywords']}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>

@stop