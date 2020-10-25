@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <script type="text/javascript" src="{{ asset($theme.'/js/admin.js') }}"></script>
    <input type="hidden" name="_token" id="TOKEN" value="{{ csrf_token() }}"/>
    <div class="container-fluid" id="main">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    @include($theme.'.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            用户管理
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="glyphicon glyphicon-eye-open"></i> 用户名</th>
                                    <th>邮箱</th>
                                    <th>用户组</th>
                                    <th class="operation">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{ $user->is_admin ? '管理员' : '普通用户' }}
                                        </td>
                                        <td>
                                            @if(!$user->is_admin)
                                                <a href="javascript:void(0);" data-id="{{ $user->id }}" class="set-admin"><i class="glyphicon glyphicon-king" data-toggle="tooltip" data-placement="top" title="设为管理员"></i></a>
                                            @else
                                                <a href="javascript:void(0);" data-id="{{ $user->id }}" class="set-no-admin"><i class="glyphicon glyphicon-king text-danger" data-toggle="tooltip" data-placement="top" title="取消管理员"></i></a>
                                            @endif
                                            <a href="javascript:void(0);" data-id="{{ $user->id }}" data-class="users" class="option-del"><i class="glyphicon glyphicon-trash pull-right" data-toggle="tooltip" data-placement="top" title="删除账户"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <div class="pagination"
                                             style="text-align:center;">{!! $users->render() !!}</div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection