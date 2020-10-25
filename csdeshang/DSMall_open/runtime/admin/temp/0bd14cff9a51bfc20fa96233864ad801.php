<?php /*a:3:{s:69:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\upload\upload_type.html";i:1591845923;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\header.html";i:1591845922;s:69:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\admin_items.html";i:1591845922;}*/ ?>
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







<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo htmlentities(lang('ds_upload_set')); ?></h3>
                <h5></h5>
            </div>
            <?php if($admin_item): ?>
<ul class="tab-base ds-row">
    <?php if(is_array($admin_item) || $admin_item instanceof \think\Collection || $admin_item instanceof \think\Paginator): if( count($admin_item)==0 ) : echo "" ;else: foreach($admin_item as $key=>$item): ?>
    <li><a href="<?php echo htmlentities($item['url']); ?>" <?php if($item['name'] == $curitem): ?>class="current"<?php endif; ?>><span><?php echo htmlentities($item['text']); ?></span></a></li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<?php endif; ?>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <div class="ncap-form-default">
            <dl>
                <dt><?php echo htmlentities(lang('upload_type')); ?></dt>
                <dd>
                    <label class="radio-label">
                        <i  class="radio-common <?php if($list_config['upload_type'] =='local'): ?>selected<?php endif; ?>">
                            <input type="radio" value="local" name="upload_type" id="upload_type_local" <?php if($list_config['upload_type'] =='local'): ?>checked="checked"<?php endif; ?>>
                        </i>
                        <span><?php echo htmlentities(lang('upload_type_local')); ?></span>
                    </label>
                    <label class="radio-label">
                        <i class="radio-common <?php if($list_config['upload_type'] =='alioss'): ?>selected<?php endif; ?>">
                            <input type="radio" value="alioss" name="upload_type" id="upload_type_alioss" <?php if($list_config['upload_type'] =='alioss'): ?> checked="checked"<?php endif; ?>>
                        </i>
                        <span><?php echo htmlentities(lang('upload_type_alioss')); ?></span>
                    </label>
                </dd>
            </dl>
            <div class="alioss">
                <dl>
                    <dt><?php echo htmlentities(lang('aliendpoint_type')); ?></dt>
                    <dd>
                        <div class="onoff">
                            <label for="aliendpoint_type_show1" class="cb-enable <?php if($list_config['aliendpoint_type'] == 1): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_yes')); ?></label>
                            <label for="aliendpoint_type_show0" class="cb-disable <?php if($list_config['aliendpoint_type'] == 0): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_no')); ?></label>
                            <input id="aliendpoint_type_show1" name="aliendpoint_type" value="1" type="radio" <?php if($list_config['aliendpoint_type'] == 1): ?> checked="checked"<?php endif; ?>>
                            <input id="aliendpoint_type_show0" name="aliendpoint_type" value="0" type="radio" <?php if($list_config['aliendpoint_type'] == 0): ?> checked="checked"<?php endif; ?>>
                        </div>
                    </dd>
                        </dl>
                        <dl>
                            <dt><?php echo htmlentities(lang('alioss_accessid')); ?></dt>
                            <dd>
                                <input id="alioss_accessid" name="alioss_accessid" value="<?php echo htmlentities((isset($list_config['alioss_accessid']) && ($list_config['alioss_accessid'] !== '')?$list_config['alioss_accessid']:'')); ?>" class="input-txt" type="text">
                                <a href="http://www.csdeshang.com/home/help/article/id/203.html" target="_blank"><?php echo htmlentities(lang('config_document')); ?></a>
                            </dd>
                        </dl>
                        <dl>
                            <dt><?php echo htmlentities(lang('alioss_accesssecret')); ?></dt>
                            <dd>
                                <input id="alioss_accesssecret" name="alioss_accesssecret" value="<?php echo htmlentities((isset($list_config['alioss_accesssecret']) && ($list_config['alioss_accesssecret'] !== '')?$list_config['alioss_accesssecret']:'')); ?>" class="input-txt" type="text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><?php echo htmlentities(lang('alioss_bucket')); ?></dt>
                            <dd>
                                <input id="alioss_bucket" name="alioss_bucket" value="<?php echo htmlentities((isset($list_config['alioss_bucket']) && ($list_config['alioss_bucket'] !== '')?$list_config['alioss_bucket']:'')); ?>" class="input-txt" type="text">
                            </dd>
                        </dl>
                        <dl>
                            <dt><?php echo htmlentities(lang('alioss_endpoint')); ?></dt>
                            <dd>
                                <input id="alioss_endpoint" name="alioss_endpoint" value="<?php echo htmlentities((isset($list_config['alioss_endpoint']) && ($list_config['alioss_endpoint'] !== '')?$list_config['alioss_endpoint']:'')); ?>" class="input-txt" type="text">
                            </dd>
                        </dl>
                    </div>
                    <dl>
                        <dt></dt>
                        <dd><input class="btn" type="submit" value="<?php echo htmlentities(lang('ds_submit')); ?>"/></dd>
                    </dl>
                </div>
            </form>
        </div>
        <script>
            $(function () {
                if ($('#upload_type_local').prop('checked')) {
                    $('.alioss').hide();
                }
                $('#upload_type_local').click(function () {
                    $('.alioss').hide();
                });
                $('#upload_type_alioss').click(function () {
                    $('.alioss').show();
                })
            });
        </script>