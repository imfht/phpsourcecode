@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">区块管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <p>
            <a href="/admin/block/add" class="btn btn-outline btn-primary btn-sm">添加区块</a>
            <a href="/admin/block/refresh" class="btn btn-outline btn-primary btn-sm">刷新区块</a>
        </p>
        @foreach($block_area as $item)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{$item->title}}
            </div>
            <div class="panel-body">

                @if(count($item->block))
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>区块ID</th>
                            <th>机器名字</th>
                            <th>区块名</th>
                            <th>描述</th>
                            <th>位置</th>
                            <th>操作</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->block as $block)
                        <tr>
                            <td><span class="label label-success">{{$block->id}}</span></td>
                            <td><span class="label label-success">{{$block->machine_name}}</span></td>
                            <td>{{$block->title}}</td>
                            <td>{{$block->description}}</td>
                            <td>{{$block->weight}}</td>
                            <td><a href="/admin/block/{{$block->id}}/edit" class="btn btn-link btn-xs">编辑</a></td>
                            <!--系统区块不允许删除-->
                            @if($block->type != 'system')
                            <td><a href="/admin/block/{{$block->id}}/delete" class="btn btn-link btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该区块吗？">删除</a></td>
                            @else
                            <td></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

            </div>
        </div>
        @endforeach


    </div>
</div>
<!-- /.row -->
<script>
    $(function () {
        $('[data-toggle="confirmation"]').confirmation({
            onConfirm: function (event, element) {
                var url = element.context.href;
                window.location.href = url;
            }
        });
    });
</script>
@stop