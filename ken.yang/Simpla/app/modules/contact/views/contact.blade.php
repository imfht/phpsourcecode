@extends('Theme::layout.page')

@section('content')


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
            <label>标题<span class="text-danger" title="此项必填">*</span></label>
            <input name="title" class="form-control" maxlength="32" required="">
        </div>
        <div class="form-group">
            <label>联系人</label>
            <input name="people" class="form-control" maxlength="32">
        </div>
        <div class="form-group">
            <label>联系方式</label>
            <input name="contact" class="form-control" maxlength="32">
        </div>
        <div class="form-group">
            <label>内容<span class="text-danger" title="此项必填">*</span></label>
            <textarea name="body" class="form-control" maxlength="1000" rows="5" required=""></textarea>
        </div>
        <button class="btn btn-default" type="submit">提交</button>
        {{Form::close()}}
    </div>
</div>

@stop