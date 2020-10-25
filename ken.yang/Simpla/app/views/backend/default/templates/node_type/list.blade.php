@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">内容类型管理</h3>
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
                    <th>操作</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($node_type as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td><span class="label label-success">{{$item->type}}</span></td>
                    <td>{{$item->description}}</td>
                    <td><a href="/admin/node/type/{{$item->type}}/edit" class="btn btn-link btn-xs">编辑</a></td>
                    <td><a href="/admin/node/type/{{$item->type}}/field" class="btn btn-link btn-xs">管理字段</a></td>
                    <td><a href="/admin/node/type/{{$item->type}}/display" class="btn btn-link btn-xs">管理显示</a></td>
                    <td><a href="/admin/node/type/{{$item->type}}/delete" class="btn btn-link btn-xs">删除</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /.row -->

@stop