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
                        <div class="panel-heading">人物创建</div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.persons.store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="hash" value="{{ $person->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">　　姓名</div>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $person->name) }}">
                                </div>
                                @if($errors->first('name'))
                                    <p class="bg-danger">{{ $errors->first('name') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　头衔</div>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $person->title) }}">
                                </div>
                                @if($errors->first('title'))
                                    <p class="bg-danger">{{ $errors->first('title') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">　　性别</div>
                                    <input type="checkbox" name="sex" value="1" data-on-text="女" data-off-text="男" @if(old('sex', $person->sex)) checked @endif />
                                </div>
                                @if($errors->first('sex'))
                                    <p class="bg-danger">{{ $errors->first('sex') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　排序</div>
                                    <input type="number" class="form-control" name="sort" value="{{ old('sort', $person->sort) }}">
                                </div>
                                @if($errors->first('sort'))
                                    <p class="bg-danger">{{ $errors->first('sort') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　贡献度</div>
                                    <input type="number" class="form-control" name="point" value="{{ old('point', $person->point) }}">
                                </div>
                                @if($errors->first('point'))
                                    <p class="bg-danger">{{ $errors->first('point') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">从业时间</div>
                                    <input type="number" class="form-control" name="age" value="{{ old('age', $person->age) }}">
                                </div>
                                @if($errors->first('age'))
                                    <p class="bg-danger">{{ $errors->first('age') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　特长</div>
                                    <input type="text" class="form-control" name="tag" value="{{ old('tag', implode(',',json_decode($person->tag))) }}">
                                </div>
                                @if($errors->first('tag'))
                                    <p class="bg-danger">{{ $errors->first('tag') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否推荐</div>
                                    <input type="checkbox" name="is_recommend" value="1"
                                           data-on-text="推荐中" data-off-text="不推荐"
                                           @if(old('is_recommend', $person->is_recommend)) checked @endif />
                                </div>
                                @if($errors->first('is_recommend'))
                                    <p class="bg-danger">{{ $errors->first('is_recommend') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否显示</div>
                                    <input type="checkbox" name="is_show" value="1"
                                           data-on-text="显示" data-off-text="隐藏"
                                           @if(old('is_show', $person->is_show)) checked @endif />
                                </div>
                                @if($errors->first('is_join'))
                                    <p class="bg-danger">{{ $errors->first('is_join') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　头像</div>
                                    <input type="text" class="form-control" name="head" id="image-default" value="{{ old('head', $person->head) }}" readonly>
                                    <input type="hidden" class="form-control" name="head_thumbnail" id="image-thumb" value="{{ old('head_thumbnail', $person->head_thumbnail) }}" readonly>
                                    <div class="input-group-addon btn btn-primary" data-class="person" data-type="cover" id="image-upload">上传头像</div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon"><span data-toggle="tooltip" data-placement="bottom" title="添加外链网址则直接跳转到该网址">外链网址</span></div>
                                    <input type="text" class="form-control" name="url" value="{{ old('url', $person->url) }}">
                                </div>
                                @if($errors->first('url'))
                                    <p class="bg-danger">{{ $errors->first('url') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">seo关键字</div>
                                    <input type="text" class="form-control" name="keywords" value="{{ old('keywords', $person->keywords) }}">
                                </div>
                                @if($errors->first('keywords'))
                                    <p class="bg-danger">{{ $errors->first('keywords') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">seo描述</div>
                                    <input type="text" class="form-control" name="description" value="{{ old('description', $person->description) }}">
                                </div>
                                @if($errors->first('description'))
                                    <p class="bg-danger">{{ $errors->first('description') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　简介</div>
                                    <input type="text" class="form-control" name="info" value="{{ old('info', $person->info) }}">
                                </div>
                                @if($errors->first('info'))
                                    <p class="bg-danger">{{ $errors->first('info') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　　详情</div>
                                    <script type="text/plain" id="content" name="text" style="width:800px;height:240px;">{!! old('text', $person->text) !!}</script>
                                </div>
                                @if($errors->first('text'))
                                    <p class="bg-danger">{{ $errors->first('text') }}</p>
                                @endif
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存人物</button>
                                    <a href="{{ route('admin.persons.index') }}" class="btn btn-warning">返回列表</a>
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
    </script>
@endsection