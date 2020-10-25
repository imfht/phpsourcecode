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
                    <div class="panel panel-default" id="adimages">
                        <div class="panel-heading">
                            广告管理 - {{ $adspace->name }}
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>名称</th>
                                    <th>
                                        <div class="dropdown">
                                            <a id="dLabel" data-target="#" href="" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                位置
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                                <li><a href="{{ route('admin.adimages.index') }}">全部</a></li>
                                                @foreach(Theme::adspaces() as $adspace)
                                                    <li><a href="{{ route('admin.adimages.show',$adspace->id) }}">{{ $adspace->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </th>
                                    <th>图片</th>
                                    <th>开放</th>
                                    <th class="operation">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($adimages as $adimage)
                                    <tr>
                                        <td>{{ $adimage->id }}</td>
                                        <td><a href="{{ url('adimages', $adimage->id) }}" target="_blank">{{ $adimage->name }}</a></td>
                                        <td><a href="{{ route('admin.adimages.show',$adimage->adspace_id) }}">{{ $adimage->space()->name }}</a></td>
                                        <td>
                                            @if($adimage->getCover())
                                                <a href="{{ asset($adimage->cover) }}" target="_blank"><img src="{{$adimage->getCover()}}" alt="图片" style="max-height:60px;"></a>
                                            @else
                                                无
                                            @endif
                                        </td>
                                        <td>
                                            @if($adimage->is_open)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.adimages.edit', $adimage->id) }}">
                                                <i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title="编辑广告"></i>
                                            </a>
                                            <a href="javascript:void(0);" data-id="{{ $adimage->id }}" data-class="adimages" class="option-del">
                                                <i class="glyphicon glyphicon-trash pull-right" data-toggle="tooltip" data-placement="top" title="删除广告"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <div class="pagination" style="text-align:center;">{!! $adimages->render() !!}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a href="{{ route('admin.adimages.create') }}" class="btn btn-info pull-right">添加广告</a></td>
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