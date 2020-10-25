@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">菜单管理</h3>
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
                    <th>菜单ID</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menu_type as $item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td><span class="label label-success">{{$item->id}}</span></td>
                    <td>{{$item->description}}</td>
                    <td><a href="/admin/menu/{{$item->id}}/list" class="btn btn-link btn-xs">查看</a></td>
                    <td><a href="/admin/menu/add/{{$item->id}}" class="btn btn-link btn-xs">添加</a></td>
                    <td><a href="/admin/menu/type/{{$item->id}}/edit" class="btn btn-link btn-xs">编辑</a></td>
                    <td><a href="/admin/menu/type/{{$item->id}}/delete" class="btn btn-link btn-xs">删除</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /.row -->

@stop