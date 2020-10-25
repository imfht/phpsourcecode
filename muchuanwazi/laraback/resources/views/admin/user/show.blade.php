@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">用户信息</h3>
                </div>

                <div class="box-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$user->id}}</td>
                        </tr>
                        <tr>
                            <th>用户名</th>
                            <td>{{$user->name}}</td>
                        </tr>
                        <tr>
                            <th>电子邮件</th>
                            <td>{{$user->email}}</td>
                        </tr>
                        <tr>
                            <th>创建时间</th>
                            <td>{{$user->created_at}}</td>
                        </tr>
                        <tr>
                            <th>更新时间</th>
                            <td>{{$user->updated_at}}</td>
                        </tr>


                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                   <a href="{{route('user.index')}}" class="btn btn-default pull-right">返回</a>
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection