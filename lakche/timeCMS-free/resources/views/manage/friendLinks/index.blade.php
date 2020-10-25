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
                    <div class="panel panel-default" id="friendLink">
                        <div class="panel-heading">
                            友情链接管理
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>名称</th>
                                    <th>LOGO</th>
                                    <th>开放</th>
                                    <th class="operation">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($friendLinks as $friendLink)
                                    <tr>
                                        <td>{{ $friendLink->id }}</td>
                                        <td><a href="{{ url('friendLink', $friendLink->id) }}" target="_blank">{{ $friendLink->name }}</a></td>
                                        <td>
                                            @if($friendLink->getCover())
                                                <a href="{{ asset($friendLink->cover) }}" target="_blank"><img src="{{$friendLink->getCover()}}" alt="LOGO"></a>
                                            @else
                                                无
                                            @endif
                                        </td>
                                        <td>
                                            @if($friendLink->is_open)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.friendLinks.edit', $friendLink->id) }}">
                                                <i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title="编辑友情链接"></i>
                                            </a>
                                            <a href="javascript:void(0);" data-id="{{ $friendLink->id }}" data-class="friendLinks" class="option-del">
                                                <i class="glyphicon glyphicon-trash pull-right" data-toggle="tooltip" data-placement="top" title="删除友情链接"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <div class="pagination" style="text-align:center;">{!! $friendLinks->render() !!}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a href="{{ route('admin.friendLinks.create') }}" class="btn btn-info pull-right">添加友情链接</a></td>
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