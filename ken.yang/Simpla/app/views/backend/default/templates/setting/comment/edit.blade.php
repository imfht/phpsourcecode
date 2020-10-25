@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">{{$comment['title']}}设置</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post')) }}
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>选择生效代码</label>
                    <?php echo Form::select('comment_choose', array('1' => '代码一', '2' => '代码二'), $comment['choose'], array('class' => 'form-control')); ?>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">评论代码</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>代码一：</label>
                    <textarea name="comment_code_one" class="form-control" rows="7">{{$comment['code_one']}}</textarea>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>代码二：</label>
                    <textarea name="comment_code_two" class="form-control" rows="7">{{$comment['code_two']}}</textarea>
                </div>
            </div>
            <div class="panel-body">
                不知道如何填写？
                <a href="http://changyan.sohu.com/" target="_blank">搜狐畅言</a>
                <a href="http://duoshuo.com/" target="_blank">多说</a>
                <a href="https://disqus.com/" target="_blank">Disqus</a>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->
@stop