@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加区块</h3>
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
            <label>标题</label>
            <input type="text" name="title" class="form-control" maxlength="32" value='{{Input::old('title')}}'>
            <small class="help-block">如果不填写标题，则在页面将不会显示标题</small>
        </div>
        <div class="form-group">
            <label>机器名字<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="machine_name" class="form-control" maxlength="64" required="" value='{{Input::old('machine_name')}}'>
            <small class="help-block">machine_name：机器名字用于前台数据读取，且机器名字不能重复，仅允许字母、数字、破折号（-）以及底线（_），填写后无法修改。</small>
        </div>
        <div class="form-group">
            <label>描述<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="description" class="form-control" maxlength="256" required="" value='{{Input::old('description')}}'>
        </div>
        <div class="form-group">
            <label>区块内容<span class="text-red" title="此项必填">*</span></label>
            <textarea name="body" id="ueditor">{{Input::old('body')}}</textarea>
        </div>
        <div class="form-group">
            <label>位置<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="weight" class="form-control" value="0" maxlength="4" required="" value='{{Input::old('weight')}}'>
        </div>
        <div class="form-group">
            <label>所属区域<span class="text-red" title="此项必填">*</span></label>
            <?php echo Form::select('baid', $areas, '', array('class' => 'form-control')); ?>
        </div>
        <div class="form-group">
            <label>是否开启缓存<span class="text-red" title="此项必填">*</span></label>
            <label class="radio-inline">
                <input type="radio" value="1" name="cache">开启
            </label>
            <label class="radio-inline">
                <input type="radio" value="0" name="cache" checked="">关闭
            </label>
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

