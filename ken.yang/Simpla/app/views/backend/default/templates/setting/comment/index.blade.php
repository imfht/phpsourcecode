@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">评论管理</h3>
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
                    <label>是否开启评论<span class="text-red" title="此项必填">*</span></label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_comment['status'] ? 'checked=""' : ''; ?> value="1" name="site_comment_status">开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $site_comment['status'] ? '' : 'checked=""'; ?> value="0" name="site_comment_status">关闭
                    </label>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>选择评论系统</label>
                    <?php echo Form::select('site_comment_value', array('0' => '未选择', 'changyan' => '搜狐畅言评论', 'duoshuo' => '多说评论', 'disqus' => '国外disqus评论'), $site_comment['value'], array('class' => 'form-control')); ?>
                </div>
            </div>
            <div class="panel-body">
                <p>
                    <a href="/admin/setting/comment/changyan" class="btn btn-outline btn-primary">搜狐畅言配置</a>
                    <a href="/admin/setting/comment/duoshuo" class="btn btn-outline btn-primary">多说配置</a>
                    <a href="/admin/setting/comment/disqus" class="btn btn-outline btn-primary">disqus配置</a>
                </p>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->
@stop