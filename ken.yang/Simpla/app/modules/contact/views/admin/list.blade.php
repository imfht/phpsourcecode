@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">联系列表</h3>
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
                        <th>联系人</th>
                        <th>联系方式</th>
                        <th>内容</th>
                        <th>提交时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $row)
                    <tr>
                        <td>{{$link->title}}</td>
                        <td>{{$link->people}}</td>
                        <td>{{$link->contact}}</td>
                        <td>{{$link->body}}</td>
                        <td>{{$link->created_at}}</td>
                        <td>
                            <a href="/admin/link/{{$link->id}}/delete" name="{{$link->id}}"  class="btn btn-danger btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该友情连接吗？"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$paginate}}
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