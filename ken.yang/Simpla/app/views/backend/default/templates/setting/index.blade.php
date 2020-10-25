@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">设置</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post','enctype'=>'multipart/form-data')) }}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">网站设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="site_name">站点名字</label>
                    <input type="text" name="site_name" class="form-control" value="{{$setting['site_name']}}" maxlength="32">
                </div>
                <div class="form-group">
                    <label for="site_description">站点描述</label>
                    <textarea name="site_description" class="form-control" rows="3">{{$setting['site_description']}}</textarea>
                </div>
                <div class="form-group">
                    <label for="site_url">站点域名网址</label>
                    <input type="text" name="site_url" class="form-control" value="{{$setting['site_url']}}" maxlength="64">
                    <small class="help-block">不需要填写http://</small>
                </div>
                <div class="form-group">
                    <label for="site_logo">站点LOGO</label>
                    <input type="file" name="site_logo">
                    <p><img src="/{{$setting['site_logo']?$setting['site_logo']:'logo.png'}}" height="100" /></p>
                </div>
                <div class="form-group">
                    <label for="site_mail">站点邮箱</label>
                    <input type="text" name="site_mail" class="form-control" value="{{$setting['site_mail']}}" maxlength="256">
                </div>
                <div class="form-group">
                    <label for="site_copyright">站点备案信息</label>
                    <input type="text" name="site_copyright" class="form-control" value="{{$setting['site_copyright']}}" maxlength="256">
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">用户设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>是否开启注册</label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $setting['user_is_allow_register'] ? 'checked=""' : '' ?> value="1" name="user_is_allow_register">开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $setting['user_is_allow_register'] ? '' : 'checked=""'; ?> alue="0" name="user_is_allow_register">关闭
                    </label>
                </div>
                <div class="form-group">
                    <label>是否开启登录</label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $setting['user_is_allow_login'] ? 'checked=""' : '' ?> value="1" name="user_is_allow_login">开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" <?php echo $setting['user_is_allow_login'] ? '' : 'checked=""'; ?> value="0" name="user_is_allow_login">关闭
                    </label>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">主题设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>前端主题</label>
                    <input type="text" name="theme_default" class="form-control" value="{{$setting['theme_default']}}">
                </div>
                <div class="form-group">
                    <label>后端主题</label>
                    <input type="text" name="admin_theme" class="form-control" value="{{$setting['admin_theme']}}">
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">文章数量显示设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>首页显示的文章数量</label>
                    <input type="number" name="home_list_num" class="form-control" value="{{$setting['home_list_num']}}">
                </div>
                <div class="form-group">
                    <label>分类页面显示的文章数量</label>
                    <input type="number" name="category_list_num" class="form-control" value="{{$setting['category_list_num']}}">
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">其他设置</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="site_tongji">统计代码</label>
                    <textarea name="site_tongji" class="form-control" rows="3">{{$setting['site_tongji']}}</textarea>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">站点维护</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    @if($setting['site_maintenance'])
                    <p class="text-danger">当前站点处于离线状态</p>
                    <a class="btn btn-success" href="/admin/setting/maintenance/up" role="button">开启站点</a>
                    @else
                    <p class='text-success'>当前站点处于开启状态</p>
                    <a class="btn btn-danger" href="/admin/setting/maintenance/down" role="button">关闭站点</a>
                    @endif
                </div>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->
@stop