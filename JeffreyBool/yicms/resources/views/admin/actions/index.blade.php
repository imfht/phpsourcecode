@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>管理员操作日志</h5>
        </div>
        <div class="ibox-content">
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th class="text-center" width="150">用户名</th>
                        <th class="text-center" width="150">拥有权限</th>
                        <th class="text-center" >操作内容</th>
                        <th class="text-center" width="200">操作地址</th>
                        <th class="text-center" width="150">登录时间</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($actions as $key => $item)
                        @if($item->type == 1)
                        <tr>
                            <td class="text-center">{{$item->id}}</td>
                            <td class="text-center">{{$item->admin->name}}</td>
                            <td class="text-center">
                                @foreach($item->admin->roles as $role)
                                    {{$role->name}}
                                @endforeach
                            </td>
                            <td>{{$item->data['action']}}</td>
                            <td class="text-center">{{$item->data['ip']}}<br>来自：{{$item->data['address']}}</td>
                            <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
                            <td class="text-center">
                                <form class="form-common" action="{{route('actions.destroy',$item->id)}}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                                </form>
                            </td>
                        </tr>
                        @else
                            <tr>
                                <td class="text-center">{{$item->id}}</td>
                                <td class="text-center">{{isset($item->admin_id) ? $item->admin->name : '暂无'}}</td>
                                <td class="text-center">
                                    @if($item->admin_id)
                                        @foreach($item->admin->roles as $role)
                                            {{$role->name}}
                                        @endforeach
                                    @else
                                        暂无
                                    @endif
                                </td>
                                <td>{{$item->data['action']}}</td>
                                <td class="text-center">{{$item->data['ip']}}<br>来自：{{$item->data['address']}}</td>
                                <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
                                <td class="text-center">
                                    <form class="form-common" action="{{route('actions.destroy',$item->id)}}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            <div class="pull-right pagination m-t-no">
                <div class="text-center">
                    {{$actions->links()}}
                </div>
                <div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection