@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">操作</h3>
                </div>
                <div class="box-body">
                    <a href="{{route('permission.create')}}" class="btn btn-primary">新建模块</a>
                </div>
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">模块列表</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody><tr>
                            <th style="width: 10px">#</th>
                            <th>模块名称</th>
                            <th>显示名称</th>
                            <th>模块描述</th>
                            <th>操作</th>
                        </tr>
                        @foreach($permissions as $permission)
                        <tr>
                            <td>{{$permission->id}}</td>
                            <td>{{$permission->name}}</td>
                            <td>{{$permission->display_name}}</td>
                            <td>{{$permission->description}}</td>
                            <td><a class="btn btn-xs" href="{{url('admin/permission/'.$permission->id.'/edit')}}">修改</a></td>
                        </tr>
                        @endforeach
                        </tbody></table>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{$permissions->render()}}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection