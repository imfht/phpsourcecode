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
                            人物管理
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>姓名</th>
                                    <th>性别</th>
                                    <th>头衔</th>
                                    <th>贡献度</th>
                                    <th class="text-center operation">推荐</th>
                                    <th class="text-center operation">显示</th>
                                    <th class="operation">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($persons as $person)
                                    <tr>
                                        <td>{{ $person->id }}</td>
                                        <td><a href="{{ route('person.show', $person->id) }}" target="_blank">{{ $person->name }}</a></td>
                                        <td>@if($person->sex == Male) 男 @else 女 @endif</td>
                                        <td>{{ $person->title }}</td>
                                        <td>{{ $person->point }}</td>
                                        <td class="text-center">
                                            @if($person->is_recommend)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($person->is_show)
                                                <i class="glyphicon glyphicon-ok text-primary"></i>
                                            @else
                                                <i class="glyphicon glyphicon-remove text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.persons.edit', $person->id) }}">
                                                <i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title="编辑人物"></i>
                                            </a>
                                            <a href="javascript:void(0);" data-id="{{ $person->id }}" data-class="persons" class="option-del">
                                                <i class="glyphicon glyphicon-trash pull-right" data-toggle="tooltip" data-placement="top" title="删除人物"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <div class="pagination"
                                             style="text-align:center;">{!! $persons->render() !!}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a href="{{ route('admin.persons.create') }}" class="btn btn-info pull-right">添加人物</a></td>
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