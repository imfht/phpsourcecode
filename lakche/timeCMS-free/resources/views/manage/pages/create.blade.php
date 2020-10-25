@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/bootstrap-switch.min.css') }}"/>
    <script type="text/javascript" src="{{ asset($theme.'/js/bootstrap-switch.min.js') }}"></script>
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
                            单页管理
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="hash" value="{{ $page->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">访问路径</div>
                                    <input type="text" class="form-control" name="url" value="{{ old('url', $page->url) }}">
                                </div>
                                @if($errors->first('url'))
                                    <p class="bg-danger">{{ $errors->first('url') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">对应模板</div>
                                    <input type="text" class="form-control" name="view" value="{{ old('view', $page->view) }}">
                                </div>
                                @if($errors->first('view'))
                                    <p class="bg-danger">{{ $errors->first('view') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">外链网址</div>
                                    <input type="text" class="form-control" name="openurl" value="{{ old('openurl', $page->openurl) }}">
                                </div>
                                @if($errors->first('openurl'))
                                    <p class="bg-danger">{{ $errors->first('openurl') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　浏览量</div>
                                    <input type="number" class="form-control" name="views" value="{{ old('views', $page->views) }}">
                                </div>
                                @if($errors->first('views'))
                                    <p class="bg-danger">{{ $errors->first('views') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">开放浏览</div>
                                    <input type="checkbox" name="is_open" value="1" data-on-text="开放" data-off-text="关闭" @if(old('is_open', $page->is_open)) checked @endif />
                                </div>
                                @if($errors->first('is_open'))
                                    <p class="bg-danger">{{ $errors->first('is_open') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">单页封面</div>
                                    <input type="text" class="form-control" name="cover" id="image-default" value="{{ old('cover', $page->cover) }}" readonly>
                                    <input type="hidden" class="form-control" name="thumb" id="image-thumb" value="{{ old('thumb', $page->thumb) }}" readonly>

                                    <div class="input-group-addon btn btn-primary" data-class="page" data-type="cover" id="image-upload">上传封面</div>
                                </div>
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存单页</button>
                                    <a href="{{ route('admin.pages.index') }}" class="btn btn-warning">返回列表</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $.fn.bootstrapSwitch.defaults.onColor = 'primary';
        $.fn.bootstrapSwitch.defaults.offColor = 'danger';
        $("[type='checkbox']").bootstrapSwitch();
    </script>
@endsection