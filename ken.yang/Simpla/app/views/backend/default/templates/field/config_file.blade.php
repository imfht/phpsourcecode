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
        {{ Form::open(array('method' => 'post',enctype =>'multipart/form-data')) }}
        <div class="checkbox">
            <label>
                <input type="checkbox" name="required" value="1" <?php echo isset($config->required) ? 'checked=""' : ''; ?>>是否必填
            </label>
        </div>
        <div class="form-group">
            <label>允许的文件扩展名</label>
            <input type="text" name="file_ext" value="" class="form-control">
            <p class="help-block">多个名字使用'，'分开,默认支持所有图片类型</p>
        </div>
        <div class="form-group">
            <label>最大文件上传大小</label>
            <input type="number" name="min_px" value="" class="form-control">
            <p class="help-block">单位为KB.</p>
        </div>
        <div class="form-group">
            <label>文件保存地址</label>
            <input type="text" name="file_path" value="" class="form-control">
            <p class="help-block">输入你的文件保存地址，可以是任意可写的文件夹路径.</p>
        </div>
        <div class="form-group">
            <label>允许最大文件上传数量</label>
            <select class="form-control" name="file_max_num">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="0">无限</option>
            </select>
        </div>
        <input type="hidden" name="type" value="file">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>

@stop