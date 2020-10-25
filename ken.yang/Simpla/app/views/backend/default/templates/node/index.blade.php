@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">内容管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <!--筛选-->
        {{ Form::open(array('method' => 'post')) }}
        <div class="form-group input-group form-filter">
            <span class="input-group-addon">状态</span>
            <?php echo Form::select('status', array('2' => '任意', '1' => '已发布', '0' => '未发布'), $choose['status'], array('class' => 'form-control')); ?>
        </div>
        <div class="form-group input-group form-filter">
            <span class="input-group-addon">类型</span>
            <?php echo Form::select('type', $types, $choose['type'], array('class' => 'form-control')); ?>
        </div>
        <button class="btn btn-default" type="submit">筛选</button>
        <a class="btn btn-default" href="{{Request::url()}}">重置</a>
        {{Form::close()}}

        <!--内容列表-->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>类型</th>
                    <th>状态</th>
                    <th>发布者</th>
                    <th>发布时间</th>
                    <th>编辑</th>
                    <th>删除</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nodes as $node)
                <tr>
                    <td>{{$node['id']}}</td>
                    <td><a href="/node/{{$node['id']}}" target="_blank">{{$node['title']}}</a></td>
                    <td>{{$node->nodeType['name']}}</td>
                    <td>
                        @if($node['status'])
                        <span class="label label-success">发布</span>
                        @else
                        <span class="label label-danger">未发布</span>
                        @endif
                        @if($node['promote'])
                        <span class="label label-default">首页</span>
                        @endif
                        @if($node['sticky'])
                        <span class="label label-default">置顶</span>
                        @endif
                        @if($node['plusfine'])
                        <span class="label label-default">加精</span>
                        @endif
                    </td>
                    <td>{{$node->user['username']}}</td>
                    <td>{{$node['created_at']}}</td>
                    <td><a href="/admin/node/{{$node['id']}}/edit" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span></td>
                    <td><a href="/admin/node/{{$node['id']}}/delete" name="{{$node['id']}}" class="btn btn-danger btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该内容吗？"><span class="glyphicon glyphicon-trash"></span></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$nodes->links()}}
    </div>
</div>
<!-- /.row -->
<script>
    $(function () {
        $('[data-toggle="confirmation"]').confirmation({
            onConfirm: function (event, element) {
                var url = element.context.pathname;
                window.location.href = url;
            }
        });
    });
</script>
@stop