@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">内容类型字段管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{Form::open(array('url'=>'/admin/node/type/'.$type.'/field/edit','method'=>'get'))}}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>标签</th>
                    <th>字段名字</th>
                    <th>字段类型</th>
                    <th>排序位置</th>
                    <th>操作</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($fields as $item)
                <tr>
                    <td>{{$item['label']}}</td>
                    <td><span class="label label-success">{{$item['field_name']}}</span></td>
                    <td>{{$item['name']}}</td>
                    <td><input name="weight[{{$item['field_name']}}]" value="{{$item['weight']}}"/></td>
                    <td><a href="/admin/node/type/{{$item['node_type']}}/field/{{$item['field_name']}}/edit" class="btn btn-link btn-xs">编辑</a></td>
                    <td><a href="/admin/node/type/{{$item['node_type']}}/field/{{$item['field_name']}}/config" class="btn btn-link btn-xs">配置</a></td>
                    <td><a href="javascript:void(0)" data-toggle="modal" data-target="#modal_pop" name="{{$item['field_name']}}" class="delete_field_sure btn btn-link btn-xs">删除</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <input type="submit" href="/admin/node/type/{{$type}}/field/edit" class="btn btn-default" value="保存"/>
        <a href="/admin/node/type/{{$type}}/field/add" class="btn btn-default">添加字段</a>
        {{Form::close()}}
    </div>
</div>
<!-- /.row -->
<!-- Modal -->
<div class="modal fade" id="modal_pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">删除字段</h4>
            </div>
            <div class="modal-body">
                你确定要删除该字段吗？一旦删除，与内容关联的字段将不再关联，请谨慎操作！
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary delete_field" value="">确认</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@stop