@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/bootstrap-switch.min.css') }}"/>
    <script type="text/javascript" src="{{ asset($theme.'/js/bootstrap-switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset($theme.'/ueditor/ueditor.config.js') }}?{{ rand(1000, 9999) }}"></script>
    <script type="text/javascript" src="{{ asset($theme.'/ueditor/ueditor.all.min.js') }}?{{ rand(1000, 9999) }}"></script>
    <script type="text/javascript" src="{{ asset($theme.'/js/plupload/plupload.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset($theme.'/js/plupload/i18n/zh_CN.js') }}"></script>
    <script type="text/javascript" src="{{ asset($theme.'/js/admin.js') }}"></script>
    <div class="container-fluid" id="main">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <div class="list-group">
                        @include($theme.'.left')
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            添加项目
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="hash" value="{{ $project->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">项目名称</div>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $project->title) }}">
                                </div>
                                @if($errors->first('title'))
                                    <p class="bg-danger">{{ $errors->first('title') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目分类</div>
                                    <select name="category_id" id="category_id" class="form-control">
                                        {!! Theme::categoryTree() !!}
                                    </select>
                                </div>
                                @if($errors->first('category_id'))
                                    <p class="bg-danger">{{ $errors->first('category_id') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目排序</div>
                                    <input type="number" class="form-control" name="sort" value="{{ old('sort', $project->sort) }}">
                                </div>
                                @if($errors->first('sort'))
                                    <p class="bg-danger">{{ $errors->first('sort') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　浏览量</div>
                                    <input type="number" class="form-control" name="views" value="{{ old('views', $project->views) }}">
                                </div>
                                @if($errors->first('views'))
                                    <p class="bg-danger">{{ $errors->first('views') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目标签</div>
                                    <input type="text" class="form-control" name="tag" value="{{ old('tag', implode(',',json_decode($project->tag))) }}">
                                </div>
                                @if($errors->first('tag'))
                                    <p class="bg-danger">{{ $errors->first('tag') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否推荐</div>
                                    <input type="checkbox" name="is_recommend" value="1" data-on-text="推荐中" data-off-text="不推荐" @if(old('is_recommend', $project->is_recommend)) checked @endif />
                                </div>
                                @if($errors->first('is_recommend'))
                                    <p class="bg-danger">{{ $errors->first('is_recommend') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否显示</div>
                                    <input type="checkbox" name="is_show" value="1" data-on-text="显示" data-off-text="隐藏" @if(old('is_show', $project->is_show)) checked @endif />
                                </div>
                                @if($errors->first('is_show'))
                                    <p class="bg-danger">{{ $errors->first('is_show') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目封面</div>
                                    <input type="text" class="form-control" name="cover" id="image-default" value="{{ old('cover', $project->cover) }}" readonly>
                                    <input type="hidden" class="form-control" name="thumb" id="image-thumb" value="{{ old('thumb', $project->thumb) }}" readonly>
                                    <div class="input-group-addon btn btn-primary" data-class="project" data-type="cover" id="image-upload">上传封面</div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">项目费用</div>
                                    <input type="number" class="form-control" name="cost" value="{{ old('cost', $project->cost) }}">
                                    <span class="input-group-addon">元</span>
                                </div>
                                @if($errors->first('cost'))
                                    <p class="bg-danger">{{ $errors->first('cost') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目周期</div>
                                    <input type="number" class="form-control" name="period" value="{{ old('period', $project->period) }}">
                                    <span class="input-group-addon">天</span>
                                </div>
                                @if($errors->first('period'))
                                    <p class="bg-danger">{{ $errors->first('period') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">参与人员</div>
                                    <input type="hidden" class="form-control" name="person_id" value="{{ old('person_id', implode(',',json_decode($project->person_id))) }}">
                                    <input type="text" class="form-control" name="person_name" value="{{ old('person_name', $project->getPersonName()) }}" readonly>
                                    <div class="input-group-addon" id="person_clear">清空</div>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">选择人物<span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" id="choose_person">
                                            @foreach(Theme::person_data(999) as $person)
                                                <li data-id="{{ $person->id }}"><a href="#" onclick="javascript:void(0);">{{ $person->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @if($errors->first('person_id'))
                                    <p class="bg-danger">{{ $errors->first('person_id') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目简介</div>
                                    <input type="text" class="form-control" name="info" value="{{ old('info', $project->info) }}">
                                </div>
                                @if($errors->first('info'))
                                    <p class="bg-danger">{{ $errors->first('info') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon"><span data-toggle="tooltip" data-placement="bottom" title="添加外链网址则直接跳转到该网址">外链网址</span></div>
                                    <input type="text" class="form-control" name="url" value="{{ old('url', $project->url) }}">
                                </div>
                                @if($errors->first('url'))
                                    <p class="bg-danger">{{ $errors->first('url') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">seo关键字</div>
                                    <input type="text" class="form-control" name="keywords" value="{{ old('keywords', $project->keywords) }}">
                                </div>
                                @if($errors->first('keywords'))
                                    <p class="bg-danger">{{ $errors->first('keywords') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">seo描述</div>
                                    <input type="text" class="form-control" name="description" value="{{ old('description', $project->description) }}">
                                </div>
                                @if($errors->first('description'))
                                    <p class="bg-danger">{{ $errors->first('description') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">项目详情</div>
                                    <script type="text/plain" id="content" name="text" style="width:800px;height:240px;">{!! old('text', $project->text) !!}</script>
                                </div>
                                @if($errors->first('text'))
                                    <p class="bg-danger">{{ $errors->first('text') }}</p>
                                @endif
                                <div class="input-group">
                                    项目进度
                                </div>
                                @if($project->speed)
                                    @foreach(json_decode($project->speed) as $speed)
                                        <div class="input-group">
                                            <div class="input-group-addon">时间</div>
                                            <input type="text" class="form-control" name="time[]" value="{{ $speed->time }}">
                                            <div class="input-group-addon">事件</div>
                                            <input type="text" class="form-control" name="event[]" value="{{ $speed->event }}">
                                            <div class="input-group-addon btn btn-danger del-speed"><i class="glyphicon glyphicon-minus"></i>删除进度</div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="input-group speed-add">
                                    <div class="input-group-addon">时间</div>
                                    <input type="text" class="form-control" name="time[]" value="">
                                    <div class="input-group-addon">事件</div>
                                    <input type="text" class="form-control map" name="event[]" value="">
                                    <div class="input-group-addon btn btn-primary add-speed"><i class="glyphicon glyphicon-plus"></i>增加进度</div>
                                </div>
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存项目</button>
                                    <a href="{{ route('admin.projects.index') }}" class="btn btn-warning">返回列表</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var ue = UE.getEditor('content');
        $.fn.bootstrapSwitch.defaults.onColor = 'primary';
        $.fn.bootstrapSwitch.defaults.offColor = 'danger';
        $("[type='checkbox']").bootstrapSwitch();
        $("#category_id").val({{ old('category_id', $project->category_id) }});
    </script>
@endsection