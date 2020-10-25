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
        {{ Form::open(array('method' => 'post')) }}
        <div class="checkbox">
            <label>
                <input type="checkbox" name="required" value="1" <?php echo isset($config->required) ? 'checked=""' : ''; ?>>是否必填
            </label>
        </div>
        <div class="form-group">
            <label>选择分类类型</label>
            <select name="category" class="form-control" required>
                <option value="0">无</option>
                @foreach($category_type as $item)
                <option value="{{$item['machine_name']}}" <?php if(isset($config->category)){if ($item['machine_name'] == $config->category) {echo 'selected=""';}} ?>>{{$item['title']}}</option>
                @endforeach
            </select>
            <p class="help-block">从你的分类类型里面选择一个分类类型,该分类类型在任何时候都可以更改</p>
        </div>
        <input type="hidden" name="type" value="category">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

