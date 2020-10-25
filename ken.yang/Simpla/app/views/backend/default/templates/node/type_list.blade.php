@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加内容</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>名字</th>
                    <th>机器名字</th>
                    <th>描述</th>
                </tr>
            </thead>
            <tbody>
                @foreach($node_type as $item)
                <tr>
                    <td><a href="/admin/node/add/{{$item->type}}">{{$item->name}}</a></td>
                    <td><span class="label label-success">{{$item->type}}</span></td>
                    <td>{{$item->description}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@stop

