@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">友情连接管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <!--用户列表-->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>描述</th>
                        <th>图片</th>
                        <th>位置</th>
                        <th>添加时间</th>
                        <th>编辑</th>
                        <th>删除</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($links as $link)
                    <tr>
                        <td><a href="{{$link->url}}" target="_blank">{{$link->title}}</a></td>
                        <td>{{$link->description}}</td>
                        <td>
                            @if($link->image)
                            <img src="/{{$link->image}}" width="45px" height="25px"/>
                            @endif
                        </td>
                        <td>{{$link->weight}}</td>
                        <td>{{$link->created_at}}</td>
                        <td>
                            <a href="/admin/link/{{$link->id}}/edit" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                        <td>
                            <a href="/admin/link/{{$link->id}}/delete" name="{{$link->id}}"  class="btn btn-danger btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该友情连接吗？"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$links->links()}}
        </div>

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