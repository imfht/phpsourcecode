@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">用户权限管理 - <small>{{$role->title}}</small></h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">

            {{ Form::open(array('method' => 'post')) }}
            @foreach($roles_access as $access)
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">{{$access['title']}}</div>

                <!-- Table -->
                <table class="table">
                    <tbody>
                        @foreach($access['list'] as $row)
                        <tr>
                            <td>{{$row['title']}}</td>
                            <td>{{$row['description']}}</td>
                            <td><input name="permission['{{$row['as']}}']" type="checkbox" value="{{$row['as']}}" <?php
                                if ($row['permisssion']) {
                                    echo 'checked';
                                }
                                ?>>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
            <button class="btn btn-default" type="submit">保存</button>
            {{Form::close()}}

        </div>
    </div>
</div>
<!-- /.row -->
@stop