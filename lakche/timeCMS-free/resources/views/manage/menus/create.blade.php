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
                            菜单管理
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="hash" value="{{ $menu->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">　名称</div>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $menu->name) }}">
                                </div>
                                @if($errors->first('name'))
                                    <p class="bg-danger">{{ $errors->first('name') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　网址</div>
                                    <input type="text" class="form-control" name="url" value="{{ old('url', $menu->url) }}">
                                </div>
                                @if($errors->first('url'))
                                    <p class="bg-danger">{{ $errors->first('url') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　位置</div>
                                    <select name="position" id="position" class="form-control">
                                        <option value="0">页头</option>
                                        <option value="1" selected>导航</option>
                                        <option value="2">页脚</option>
                                    </select>
                                </div>
                                @if($errors->first('position'))
                                    <p class="bg-danger">{{ $errors->first('position') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">　排序</div>
                                    <input type="number" class="form-control" name="sort" value="{{ old('sort', $menu->sort) }}">
                                </div>
                                @if($errors->first('sort'))
                                    <p class="bg-danger">{{ $errors->first('sort') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">　开放</div>
                                    <input type="checkbox" name="is_open" value="1" data-on-text="开放" data-off-text="关闭" @if(old('is_open', $menu->is_open)) checked @endif />
                                </div>
                                @if($errors->first('is_open'))
                                    <p class="bg-danger">{{ $errors->first('is_open') }}</p>
                                @endif
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存菜单</button>
                                    <a href="{{ route('admin.menus.index') }}" class="btn btn-warning">返回列表</a>
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
        $("#position").val({{ old('position', $menu->position) }});
    </script>
@endsection