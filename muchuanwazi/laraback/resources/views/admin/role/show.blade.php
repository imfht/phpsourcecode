@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">角色信息</h3>
                </div>

                <div class="box-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$role->id}}</td>
                        </tr>
                        <tr>
                            <th>模块名称</th>
                            <td>{{$role->name}}</td>
                        </tr>
                        <tr>
                            <th>显示名称</th>
                            <td>{{$role->display_name}}</td>
                        </tr>
                        <tr>
                            <th>模块描述</th>
                            <td>{{$role->description}}</td>
                        </tr>


                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                   <a href="{{route('role.index')}}" class="btn btn-default pull-right">返回</a>
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection