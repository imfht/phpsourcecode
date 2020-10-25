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
                <div class="col-sm-2">
                    @include($theme.'.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">系统设置</div>
                        <div class="panel-body">
                            @if(isset($message))
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('admin.system.store') }}" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="TOKEN" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="hash" value="system">
                                <div class="input-group">
                                    <div class="input-group-addon">网站标题</div>
                                    <input type="text" class="form-control" name="title"
                                           value="{{ $system['title'] or ''  }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">标题附加字</div>
                                    <input type="text" class="form-control" name="subtitle"
                                           value="{{ $system['subtitle'] or ''  }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">seo关键字</div>
                                    <input type="text" class="form-control" name="keywords"
                                           value="{{ $system['keywords'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">seo描述</div>
                                    <input type="text" class="form-control" name="description"
                                           value="{{ $system['description'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">版权说明</div>
                                    <input type="text" class="form-control" name="copyright"
                                           value="{{ $system['copyright'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">备案号</div>
                                    <input type="text" class="form-control" name="record"
                                           value="{{ $system['record'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">联系QQ</div>
                                    <input type="text" class="form-control" name="qq"
                                           value="{{ $system['qq'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">微信号</div>
                                    <input type="text" class="form-control" name="wechat"
                                           value="{{ $system['wechat'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">微信二维码</div>
                                    <input type="text" class="form-control" name="wechatcode" id="image-default" value="{{ $system['wechatcode'] or '' }}" readonly>
                                    <input type="hidden" class="form-control" name="thumb" id="image-thumb" value="" readonly>

                                    <div class="input-group-addon btn btn-primary" data-class="system" data-type="image" id="image-upload">上传二维码</div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">微博号</div>
                                    <input type="text" class="form-control" name="weibo"
                                           value="{{ $system['weibo'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">网站模板</div>
                                    <input type="text" class="form-control" name="theme"
                                           value="{{ $system['theme'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">工信部备案</div>
                                    <input type="text" class="form-control" name="miitbeian"
                                           value="{{ $system['miitbeian'] or '' }}">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">网安备案</div>
                                    <input type="text" class="form-control" name="beian"
                                           value="{{ $system['beian'] or '' }}">
                                </div>
                                <div class="input-group checkbox">
                                    <div class="input-group-addon">网站是否开放</div>
                                    <input type="checkbox" name="is_open" value="1"
                                           data-on-text="开启" data-off-text="关闭"
                                           @if(isset($system['is_open']) && intval($system['is_open']) > 0 ) checked @endif />
                                </div>
                                <div class="input-group col-sm-12">
                                    <button type="submit" class="btn btn-primary pull-right">保存设置</button>
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