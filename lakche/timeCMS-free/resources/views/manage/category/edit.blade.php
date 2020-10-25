@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/bootstrap-switch.min.css') }}"/>
    <script src="{{ asset($theme.'/js/bootstrap-switch.min.js') }}"></script>
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
                            文章分类修改
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.category.update',$category->id) }}">
                                {{ csrf_field() }}
                                {{ method_field('put') }}
                                <input type="hidden" name="hash" value="{{ $category->hash }}">
                                <div class="input-group">
                                    <div class="input-group-addon">栏目标题</div>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $category->title) }}">
                                </div>
                                @if($errors->first('title'))
                                <p class="bg-danger">{{ $errors->first('title') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">上级栏目</div>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="0">--请选择--</option>
                                        {!! Theme::categoryTree() !!}
                                    </select>
                                </div>
                                @if($errors->first('parent_id'))
                                    <p class="bg-danger">{{ $errors->first('parent_id') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">栏目简介</div>
                                    <input type="text" class="form-control" name="info" value="{{ old('info', $category->info) }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">栏目排序</div>
                                    <input type="number" class="form-control" name="sort" value="{{ old('sort', $category->sort) }}">
                                </div>
                                @if($errors->first('sort'))
                                    <p class="bg-danger">{{ $errors->first('sort') }}</p>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-addon">栏目封面</div>
                                    <input type="text" class="form-control" name="cover" id="image-default" value="{{ old('cover', $category->cover) }}" readonly>
                                    <input type="hidden" class="form-control" name="thumb" id="image-thumb" value="{{ old('thumb', $category->thumb) }}" readonly>

                                    <div class="input-group-addon btn btn-primary" data-class="category" data-type="cover" id="image-upload">上传封面</div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">seo关键字</div>
                                    <input type="text" class="form-control" name="keywords" value="{{ old('keywords', $category->keywords) }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">seo描述</div>
                                    <input type="text" class="form-control" name="description" value="{{ old('description', $category->description) }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">带子分类模板</div>
                                    <input type="text" class="form-control" name="templet_all" value="{{ old('templet_all', $category->templet_all) }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">不带子分类模板</div>
                                    <input type="text" class="form-control" name="templet_nosub" value="{{ old('templet_nosub', $category->templet_nosub) }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">文章模板</div>
                                    <input type="text" class="form-control" name="templet_article" value="{{ old('templet_article', $category->templet_article) }}">
                                </div>
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">导航显示</div>
                                    <input type="checkbox" name="is_nav_show" value="1"
                                           data-on-text="显示" data-off-text="隐藏"
                                           @if(old('is_nav_show', $category->is_nav_show)) checked @endif />
                                </div>
                                @if($errors->first('is_nav_show'))
                                    <p class="bg-danger">{{ $errors->first('is_nav_show') }}</p>
                                @endif
                                <a href="{{ route('admin.category.index') }}" class="btn btn-warning">返回根分类</a>
                                <button type="submit" class="btn btn-primary pull-right">保存分类</button>
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
        $("#parent_id").val({{ old('parent_id', $category->parent_id) }});
    </script>
@endsection