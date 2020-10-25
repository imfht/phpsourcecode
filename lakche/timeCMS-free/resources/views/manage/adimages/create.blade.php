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
                            广告管理
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.adimages.store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="hash" value="{{ $adimage->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">广告名称</div>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $adimage->name) }}">
                                </div>
                                @if($errors->first('name'))
                                    <p class="bg-danger">{{ $errors->first('name') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">广告位置</div>
                                    <select name="adspace_id" id="adspace_id" class="form-control">
                                        {!! Theme::adspaceTree() !!}
                                    </select>
                                </div>
                                @if($errors->first('adspace_id'))
                                    <p class="bg-danger">{{ $errors->first('adspace_id') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">广告网址</div>
                                    <input type="text" class="form-control" name="url" value="{{ old('url', $adimage->url) }}">
                                </div>
                                @if($errors->first('url'))
                                    <p class="bg-danger">{{ $errors->first('url') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">浏览量</div>
                                    <input type="number" class="form-control" name="views" value="{{ old('views', $adimage->views) }}">
                                </div>
                                @if($errors->first('views'))
                                    <p class="bg-danger">{{ $errors->first('views') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　排序</div>
                                    <input type="number" class="form-control" name="sort" value="{{ old('sort', $adimage->sort) }}">
                                </div>
                                @if($errors->first('sort'))
                                    <p class="bg-danger">{{ $errors->first('sort') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">　显示</div>
                                    <input type="checkbox" name="is_open" value="1" data-on-text="显示" data-off-text="隐藏" @if(old('is_open', $adimage->is_open)) checked @endif />
                                </div>
                                @if($errors->first('is_open'))
                                    <p class="bg-danger">{{ $errors->first('is_open') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">广告图片</div>
                                    <input type="text" class="form-control" name="cover" id="image-default" value="{{ old('cover', $adimage->cover) }}" readonly>
                                    <input type="hidden" class="form-control" name="thumb" id="image-thumb" value="{{ old('thumb', $adimage->thumb) }}" readonly>

                                    <div class="input-group-addon btn btn-primary" data-class="apimages" data-type="cover" id="image-upload">上传图片</div>
                                </div>
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存广告</button>
                                    <a href="{{ route('admin.adimages.index') }}" class="btn btn-warning">返回列表</a>
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
        $("#adspace_id").val({{ old('adspace_id', $adimage->adspace_id) }});
    </script>
@endsection