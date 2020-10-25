@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加{{$node_type->name}}</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post','enctype'=>'multipart/form-data')) }}<?php //print_r($messages);                   ?>
        <!--错误输出start-->
        @if ($errors->has('title'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('title') }}</div>
        @endif
        <!--错误输出end-->
        <div class="form-group">
            <label>标题<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="title" class="form-control" required="" maxlength="256" value='{{Input::old('title')}}'>
        </div>
        <div class="form-group">
            <label>区块内容</label>
            <textarea name="body" id="ueditor">{{Input::old('body')}}</textarea>
        </div>
        @foreach($node_type_fields as $field)
            @include("BackTheme::templates.field.".$field['field_type'])
        @endforeach

        <!--其他设置-->
        <div id="accordion" class="panel-group">
            <!--发布选项-->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" class="collapsed">发布选项</a>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="collapseOne" style="height: 0px;">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="status" value="1" checked="">
                                    已发布
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="promote" value="1">
                                    推荐到首页
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sticky" value="1">
                                    置顶
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="plusfine" value="1">
                                    加精
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="view" value="1">
                                    随机浏览数
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--作者信息-->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#collapseTwo" data-parent="#accordion" data-toggle="collapse" class="collapsed">作者信息</a>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="collapseTwo" style="height: 0px;">
                    <div class="panel-body">
                        <div class="form-group">
                            <label>作者ID</label>
                            <input type="text" name="uid" class="form-control" maxlength="11" value='{{Input::old('uid')}}'>
                            <label>发布时间</label>
                            <input type="text" name="created_at" class="form-control" maxlength="11" value='{{Input::old('created_at')}}'>
                        </div>
                    </div>
                </div>
            </div>
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
                            <input type="text" name="seo_title" class="form-control" maxlength="256" value="{{Input::old('seo_title')}}">
                            <label>描述</label>
                            <input type="text" name="seo_description" class="form-control" maxlength="256" value="{{Input::old('seo_description')}}">
                            <label>关键字</label>
                            <input type="text" name="seo_keywords" class="form-control" maxlength="256" value="{{Input::old('seo_keywords')}}">
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

