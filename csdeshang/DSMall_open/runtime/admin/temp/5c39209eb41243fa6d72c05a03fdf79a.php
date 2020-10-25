<?php /*a:5:{s:62:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\index\index.html";i:1591845922;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\header.html";i:1591845922;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\topnav.html";i:1591845922;s:62:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\left.html";i:1591845922;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\footer.html";i:1591845922;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>DSMall<?php echo htmlentities(lang('system_backend')); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/css/admin.css">
        <link rel="stylesheet" href="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/js/jquery-ui/jquery-ui.min.css">
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/jquery-2.1.4.min.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/jquery.validate.min.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/jquery.cookie.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/common.js"></script>
        <script src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/js/admin.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/js/jquery-ui/jquery-ui.min.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/js/jquery-ui/jquery.ui.datepicker-zh-CN.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/perfect-scrollbar.min.js"></script>
        <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/layer/layer.js"></script>
        <script type="text/javascript">
            var BASESITEROOT = "<?php echo htmlentities(BASE_SITE_ROOT); ?>";
            var ADMINSITEROOT = "<?php echo htmlentities(ADMIN_SITE_ROOT); ?>";
            var BASESITEURL = "<?php echo htmlentities(BASE_SITE_URL); ?>";
            var HOMESITEURL = "<?php echo htmlentities(HOME_SITE_URL); ?>";
            var ADMINSITEURL = "<?php echo htmlentities(ADMIN_SITE_URL); ?>";
        </script>
    </head>
    <body>
        <div id="append_parent"></div>
        <div id="ajaxwaitid"></div>





<div class="admincp-header">
    <div class="logo">
        <img src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/backlogo.png"/>
    </div>
    <div class="navbar">
        <ul class="fl" style="float:left;">
            <?php if(is_array($menu_list) || $menu_list instanceof \think\Collection || $menu_list instanceof \think\Paginator): if( count($menu_list)==0 ) : echo "" ;else: foreach($menu_list as $key=>$menu): ?>
            <li id="nav_<?php echo htmlentities($menu['name']); ?>" <?php if(!$menu['show']): ?>style="display:none"<?php endif; ?>>
                <a href="javascript:void(0)" onclick="openItem('<?php echo htmlentities($menu['name']); ?>')"><?php echo htmlentities($menu['text']); ?></a>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <ul class="fr" style="float:right">
            <li>
                <span><?php echo htmlentities(lang('topnav_hello')); ?>,<?php echo session('admin_name'); ?></span>
                <div class="sub-meun">
                    <dl>
                        <dd><a href="<?php echo url('Index/modifypw'); ?>" target="main-frame"><i class="iconfont">&#xe67b;</i><?php echo htmlentities(lang('topnav_edit_password')); ?></a></dd>
                        <dd><a href="javascript:dsLayerConfirm('<?php echo url('Login/logout'); ?>','<?php echo htmlentities(lang('topnav_logout_confirm')); ?>')"><i class="iconfont">&#xe70c;</i><?php echo htmlentities(lang('topnav_logout')); ?></a></dd>
                    </dl>
                </div>
            </li>
            <li><a href="javascript:dsLayerConfirm('<?php echo url('Index/clear'); ?>','<?php echo htmlentities(lang('topnav_clear_confirm')); ?>')" target="main-frame"><?php echo htmlentities(lang('topnav_cache')); ?></a></li>
            <li><a href="<?php echo url('/home/Index/index'); ?>" target="_blank"><?php echo htmlentities(lang('topnav_visit_home')); ?></a></li>
        </ul>
    </div>
</div>

<div class="admincp-container">
    <div class="admincp-container-left">
        <div id="mainMenu">
<?php if(is_array($menu_list) || $menu_list instanceof \think\Collection || $menu_list instanceof \think\Paginator): if( count($menu_list)==0 ) : echo "" ;else: foreach($menu_list as $menu_k=>$menu): ?>
<ul id="sort_<?php echo htmlentities($menu['name']); ?>" <?php if($menu_k != 'dashboard'): ?>style="display:none"<?php endif; ?>>
    <?php if(is_array($menu['children']) || $menu['children'] instanceof \think\Collection || $menu['children'] instanceof \think\Paginator): if( count($menu['children'])==0 ) : echo "" ;else: foreach($menu['children'] as $submenu_key=>$submenu): $args_array = explode(",",$submenu['args']); ?>
    <li id="left_<?php echo $args_array[2].$args_array[1].$args_array[0] ?>"><a href="javascript:void(0)"  onclick="openItem('<?php echo htmlentities($submenu['args']); ?>')" ><i class="iconfont"><?php echo isset($submenu['ico'])?$submenu['ico']:''; ?></i><?php echo htmlentities($submenu['text']); ?></a></li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<?php endforeach; endif; else: echo "" ;endif; ?>
</div>


    </div>
    <div class="admincp-container-right">
        <div class="top-border"></div>
        <iframe src="<?php echo url('Dashboard/index'); ?>" id="main-frame" name="main-frame" style="overflow: visible;" frameborder="0" width="100%" height="94%" scrolling="yes" onload="window.parent"></iframe>
    </div>
</div>
<script>
            $(function() {
                $('#welcome,dashboard,dashboard').addClass('active');
                if ($.cookie('now_location_controller') != null) {
                    openItem($.cookie('now_location_action') + ',' + $.cookie('now_location_controller') + ',' + $.cookie('now_location_module'));
                } else {
                    $('#mainMenu>ul').first().css('display', 'block');
                    //第一次进入后台时，默认定到欢迎界面
                    $('#item_welcome').addClass('selected');
                    $('#workspace').attr('src', ADMINSITEURL+'/Dashboard/welcome.html');
                }
                $('#iframe_refresh').click(function() {
                    var fr = document.frames ? document.frames("workspace") : document.getElementById("workspace").contentWindow;
                    fr.location.reload();
                });
            });


            function openItem(args) {
                spl = args.split(',');
                action = spl[0];
                try {
                    controller = spl[1];
                    module = spl[2];
                }
                catch (ex) {
                }
                if (typeof(controller) == 'undefined') {
                    var module = args;
                }
                //顶部导航样式处理
                $('.actived').removeClass('actived');
                $('#nav_' + module).addClass('actived');
                //清除左侧样式
                $('.selected').removeClass('selected');

                //show
                $('#mainMenu ul').css('display', 'none');
                $('#sort_' + module).css('display', 'block');
                if (typeof(controller) == 'undefined') {
                    //顶部菜单事件
                    html = $('#sort_' + module + '>li').first().html();
                    str = html.match(/openItem\('(.*)'\)/ig);
                    arg = str[0].split("'");
                    spl = arg[1].split(',');
                    action = spl[0];
                    controller = spl[1];
                    module = spl[2];
                    first_obj = $('#sort_' + module + '>li').first();
                    $(first_obj).addClass('selected');
                } else {
                    //左侧菜单事件
                    //location
                    $.cookie('now_location_module', module);
                    $.cookie('now_location_controller', controller);
                    $.cookie('now_location_action', action);
                    $("#left_"+ module + controller + action).addClass('selected');
                    
                }
                src = ADMINSITEURL + '/' + controller + '/' + action + '/';
                $('#main-frame').attr('src', src);
            }
</script>

 


