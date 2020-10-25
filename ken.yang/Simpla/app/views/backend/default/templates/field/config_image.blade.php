@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">{{$field_info['label']}}字段配置</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        @if ($errors->has('category'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('category') }}</div>
        @endif
        {{ Form::open(array('method' => 'post','enctype'=>'multipart/form-data')) }}
        <div class="checkbox">
            <label>
                <input type="checkbox" name="required" value="1" <?php echo isset($config->required) ? 'checked=""' : ''; ?>>是否必填
            </label>
        </div>
        <div class="form-group">
            <label>允许的文件扩展名</label>
            <input type="text" name="file_ext" value="{{isset($config->file_ext)?$config->file_ext:''}}" class="form-control">
            <p class="help-block">多个名字使用'，'分开,默认支持所有图片类型</p>
        </div>
        <div class="form-group">
            <label>最大图像分辨率</label>
            <input type="number" name="max_len" value="{{isset($config->max_len)?$config->max_len:''}}" class="form-control max-width-100"> X <input type="number" name="min_len" value="{{isset($config->min_len)?$config->min_len:''}}" class="form-control max-width-100">
            <p class="help-block">要么两个值都填，要么都不填写.</p>
        </div>
        <div class="form-group">
            <label>最大文件上传大小</label>
            <input type="number" name="max_size" value="{{isset($config->max_size)?$config->max_size:''}}" class="form-control">
            <p class="help-block">单位为KB.</p>
        </div>
        <div class="form-group">
            <label>文件保存地址</label>
            <input type="text" name="file_path" value="{{isset($config->file_path)?$config->file_path:''}}" class="form-control">
            <p class="help-block">输入你的文件保存地址，可以是任意可写的文件夹路径.</p>
        </div>
        <div class="form-group">
            <label>默认图像</label>
            <input type="file" name="file_default">
            @if(isset($config->file_default))
            <p>已设置默认图像<img src="/{{isset($config->file_path)?$config->file_path:'/upload/image/site/default/'}}{{isset($config->file_default)?$config->file_default:''}}" width="50" height="50"/></p>
            @endif
            <p class="help-block">当没有上传图像的时候使用默认.</p>
        </div>
        <div class="form-group">
            <label>允许最大图片上传数量</label>
            <?php echo Form::select('file_max_num', array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '0' => '无限'), isset($config->file_max_num) ? $config->file_max_num : '', array('class' => 'form-control')); ?>
        </div>
        <input type="hidden" name="type" value="image">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>

@stop