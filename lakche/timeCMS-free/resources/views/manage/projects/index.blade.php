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
                            项目管理
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
                                                分类
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                                <li><a href="{{ route('admin.projects.index') }}">全部</a></li>
                                                @foreach(Theme::categories() as $category)
                                                    <li><a href="{{ route('admin.projects.show',$category->id) }}">{{ $category->title }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </th>
                                    <th class="text-center operation">推荐</th>
                                    <th class="text-center operation">显示</th>
                                    <th class="operation">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->id }}</td>
                                        <td><a href="{{ route('project.show', [$project->id]) }}"
                                               target="_blank">{{ $project->title }}</a></td>
                                        <td><a href="{{ route('admin.projects.show',$project->category_id) }}">{{ $project->category->title }}</a></td>
                                        <td class="text-center">
                                            @if($project->is_recommend)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($project->is_show)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.projects.edit', [$project->id]) }}">
                                                <i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title="编辑项目"></i>
                                            </a>
                                            <a href="javascript:void(0);" data-id="{{ $project->id }}" data-class="projects" class="option-del">
                                                <i class="glyphicon glyphicon-trash pull-right" data-toggle="tooltip" data-placement="top" title="删除项目"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <div class="pagination"
                                             style="text-align:center;">{!! $projects->render() !!}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a href="{{ route('admin.projects.create') }}"
                                                       class="btn btn-info pull-right">添加项目</a></td>
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