@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">缓存管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post')) }}

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">缓存设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>是否开启缓存</label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_cache['status'] ? 'checked=""' : ''; ?> value="1" name="site_cache_status">开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_cache['status'] ? '' : 'checked=""'; ?> value="0" name="site_cache_status">关闭
                    </label>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>是否缓存首页</label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_cache['extend'] ? 'checked=""' : ''; ?> value="1" name="site_cache_extend">开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_cache['extend'] ? '' : 'checked=""'; ?> value="0" name="site_cache_extend">关闭
                    </label>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>选择缓存类型</label>
                    <?php echo Form::select('site_cache_value', array('1' => '只缓存内容(强烈推荐)', '2' => '只缓存分类', '0' => '全局缓存'), $site_cache['value'], array('class' => 'form-control')); ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <a class="btn btn-primary" href="/admin/setting/cache/clear">清除所有缓存</a>
                </div>
            </div>
        </div>

        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->
@stop