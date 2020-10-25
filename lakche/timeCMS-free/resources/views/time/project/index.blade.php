@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                        <div class="panel panel-primary">
                            <div class="panel-heading">@if(isset($type)) {{ $type->title }} @else 全部项目 @endif</div>
                            <div class="panel-body" id="projects">
                                @if(isset($type) && $type->info != '')
                                    <div class="alert alert-success" role="alert">{{ $type->info }}</div>
                                @endif
                                @foreach($projects as $project)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="thumbnail">
                                            <div class="pic">
                                                <p>
                                                    <a href="{{ url('project',$project->id) }}">
                                                        <img src="{{ $project->getCover() }}" alt="{{ $project->title }}">
                                                    </a>
                                                </p>
                                            </div>
                                            <div class="caption text-center">
                                                <a href="{{ url('project',$project->id) }}">
                                                    <h3>{{ str_limit($project->title,14,'...') }}</h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="panel-footer text-center">
                                {!! $projects->render() !!}
                            </div>
                        </div>
                </div>
                <div class="col-sm-3">
                    @include($theme.'.category.right')
                </div>
            </div>
        </div>
    </div>
@endsection