@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/bootstrap-switch.min.css') }}"/>
    <script type="text/javascript" src="{{ asset($theme.'/js/bootstrap-switch.min.js') }}"></script>
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
                            留言管理
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="{{ route('admin.comments.update',$comment->id) }}"
                                  enctype="multipart/form-data">
                                {{ csrf_field() }}
                                {{ method_field('put') }}
                                <input type="hidden" name="hash" value="{{ $comment->hash }}">
                                <input type="hidden" name="article_id" value="{{ $comment->article_id }}">
                                <div class="input-group">
                                    <div class="input-group-addon">留言人</div>
                                    <input type="text" class="form-control" name='name' value="{{ $comment->name }}" readonly>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">联系方式</div>
                                    <input type="text" class="form-control" name="phone" value="{{ $comment->phone }}" readonly>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">留言时间</div>
                                    <input type="text" class="form-control" value="{{ $comment->created_at }}" readonly>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">留言文章</div>
                                    <a class="form-control" href="{{ url('article',$comment->article_id) }}" target="_blank">{{ $comment->article()->title }}</a>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">留言内容</div>
                                    <textarea class="form-control" rows="5" name='info' readonly>{!! $comment->info !!}</textarea>
                                </div>

                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否显示</div>
                                    <input type="checkbox" name="is_show" value="1" data-on-text="显示" data-off-text="不显示" @if(old('is_show', $comment->is_show)) checked @endif />
                                </div>
                                @if($errors->first('is_show'))
                                    <p class="bg-danger">{{ $errors->first('is_show') }}</p>
                                @endif
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">是否审核</div>
                                    <input type="checkbox" name="is_open" value="1" data-on-text="通过" data-off-text="未通过" @if(old('is_open', $comment->is_open)) checked @endif />
                                </div>
                                @if($errors->first('is_open'))
                                    <p class="bg-danger">{{ $errors->first('is_open') }}</p>
                                @endif
  
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存修改</button>
                                    <a href="{{ route('admin.comments.index') }}" class="btn btn-warning">返回列表</a>
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