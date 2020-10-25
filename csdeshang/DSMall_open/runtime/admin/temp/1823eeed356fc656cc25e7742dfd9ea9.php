<?php /*a:3:{s:62:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\config\base.html";i:1591845922;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\header.html";i:1591845922;s:69:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\admin_items.html";i:1591845922;}*/ ?>
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
                <h3><?php echo htmlentities(lang('web_set')); ?></h3>
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
    
    <form method="post" enctype="multipart/form-data" name="form1" action="">
        <div class="ncap-form-default">
            <dl>
                <dt><?php echo htmlentities(lang('site_name')); ?></dt>
                <dd>
                    <input id="site_name" name="site_name" value="<?php echo htmlentities($list_config['site_name']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"><?php echo htmlentities(lang('web_name_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('site_logo')); ?></dt>
                <dd>
                    <?php if(!(empty($list_config['site_logo']) || (($list_config['site_logo'] instanceof \think\Collection || $list_config['site_logo'] instanceof \think\Paginator ) && $list_config['site_logo']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['site_logo']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box"><input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="site_logo" type="file" class="type-file-file" id="site_logo" size="30" hidefocus="true" ds_type="change_site_logo">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('site_logo_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('member_logo')); ?></dt>
                <dd>
                    <?php if(!(empty($list_config['member_logo']) || (($list_config['member_logo'] instanceof \think\Collection || $list_config['member_logo'] instanceof \think\Paginator ) && $list_config['member_logo']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['member_logo']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box"><input type='text' name='textfield' id='textfield2' class='type-file-text' /><input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="member_logo" type="file" class="type-file-file" id="member_logo" size="30" hidefocus="true" ds_type="change_member_logo">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('member_logo_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('seller_center_logo')); ?></dt>
                <dd>
                    <?php if(!(empty($list_config['seller_center_logo']) || (($list_config['seller_center_logo'] instanceof \think\Collection || $list_config['seller_center_logo'] instanceof \think\Paginator ) && $list_config['seller_center_logo']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['seller_center_logo']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box"><input type='text' name='textfield' id='textfield3' class='type-file-text' /><input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="seller_center_logo" type="file" class="type-file-file" id="seller_center_logo" size="30" hidefocus="true" ds_type="change_seller_center_logo">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('seller_center_logo_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('site_mobile_logo')); ?></dt>
                <dd>
                    <?php if(!(empty($list_config['site_mobile_logo']) || (($list_config['site_mobile_logo'] instanceof \think\Collection || $list_config['site_mobile_logo'] instanceof \think\Paginator ) && $list_config['site_mobile_logo']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['site_mobile_logo']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box"><input type='text' name='textfield' id='textfield4' class='type-file-text' /><input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="site_mobile_logo" type="file" class="type-file-file" id="site_mobile_logo" size="30" hidefocus="true" ds_type="change_site_mobile_logo">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('site_mobile_logo_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('site_logowx')); ?></dt>
                <dd>
                    <?php if(!(empty($list_config['site_logowx']) || (($list_config['site_logowx'] instanceof \think\Collection || $list_config['site_logowx'] instanceof \think\Paginator ) && $list_config['site_logowx']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['site_logowx']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box"><input type='text' name='textfield' id='textfield5' class='type-file-text' /><input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="site_logowx" type="file" class="type-file-file" id="site_logowx" size="30" hidefocus="true" ds_type="change_site_logowx">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('site_logowx_notice')); ?></p>
                </dd>
            </dl>
            
            <dl id="business_licence" class="noborder">
                    <dt class="required"><label for="file_business_licence"><?php echo htmlentities(lang('config_business_licence')); ?>:</label></dt>
                    <dd class="vatop rowform">
                        <?php if(!(empty($list_config['business_licence']) || (($list_config['business_licence'] instanceof \think\Collection || $list_config['business_licence'] instanceof \think\Paginator ) && $list_config['business_licence']->isEmpty()))): ?>
                        <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                            <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['business_licence']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                        </span>
                        <?php endif; ?>
                        <span class="type-file-box">
                            <input type='text' name='textfield' id='textfield6' class='type-file-text' />
                            <input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                            <input name="business_licence" id="file_business_licence" type="file" class="type-file-file" id="site_logo" size="30" hidefocus="true">
                        </span>
                        <p class="notic"></p>
                    </dd>
                  
                </dl>
            
            <dl>
                <dt><?php echo htmlentities(lang('fixed_suspension_state')); ?></dt>
                <dd>
                    <div class="onoff">
                        <label for="fixed_suspension_state1" class="cb-enable <?php if($list_config['fixed_suspension_state'] == 1): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_open')); ?></label>
                        <label for="fixed_suspension_state0" class="cb-disable <?php if($list_config['fixed_suspension_state'] == 0): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_close')); ?></label>
                        <input id="fixed_suspension_state1" name="fixed_suspension_state" value="1" type="radio" <?php if($list_config['fixed_suspension_state'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="fixed_suspension_state0" name="fixed_suspension_state" value="0" type="radio" <?php if($list_config['fixed_suspension_state'] == 0): ?> checked="checked"<?php endif; ?>>
                    </div>
                </dd>
            </dl>
            <dl id="fixed_suspension_img" class="noborder">
                <dt class="required"><label for="file_fixed_suspension_img"><?php echo htmlentities(lang('fixed_suspension_img')); ?></label></dt>
                <dd class="vatop rowform">
                    <?php if(!(empty($list_config['fixed_suspension_img']) || (($list_config['fixed_suspension_img'] instanceof \think\Collection || $list_config['fixed_suspension_img'] instanceof \think\Paginator ) && $list_config['fixed_suspension_img']->isEmpty()))): ?>
                    <span class="type-file-show"><img class="show_image" src="<?php echo htmlentities(ADMIN_SITE_ROOT); ?>/images/preview.png">
                        <div class="type-file-preview"><img src="<?php echo htmlentities(UPLOAD_SITE_URL); ?>/<?php echo htmlentities(ATTACH_COMMON); ?>/<?php echo htmlentities($list_config['fixed_suspension_img']); ?>?<?php echo htmlentities(TIMESTAMP); ?>"></div>
                    </span>
                    <?php endif; ?>
                    <span class="type-file-box">
                        <input type='text' name='textfield' id='textfield6' class='type-file-text' />
                        <input type='button' name='button' id='button1' value='上传' class='type-file-button' />
                        <input name="fixed_suspension_img" id="file_fixed_suspension_img" type="file" class="type-file-file" id="site_logo" size="30" hidefocus="true">
                    </span>
                    <p class="notic"><?php echo htmlentities(lang('fixed_suspension_img_notice')); ?></p>
                </dd>
            </dl>
            
              <dl>
                <dt><?php echo htmlentities(lang('fixed_suspension_url')); ?></dt>
                <dd>
                    <input id="fixed_suspension_url" name="fixed_suspension_url" value="<?php echo htmlentities($list_config['fixed_suspension_url']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"><?php echo htmlentities(lang('fixed_suspension_url_notice')); ?></p>
                </dd>
			</dl>  
              <dl>
                <dt><?php echo htmlentities(lang('hot_search')); ?></dt>
                <dd>
                    <textarea id="hot_search" name="hot_search"><?php echo htmlentities($list_config['hot_search']); ?></textarea>
                    <span class="err"></span>
                    <p class="notic"><?php echo htmlentities(lang('field_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('h5_site_url')); ?></dt>
                <dd>
                    <input id="h5_site_url" name="h5_site_url" value="<?php echo htmlentities($list_config['h5_site_url']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('h5_force_redirect')); ?></dt>
                <dd>
                    <div class="onoff">
                        <label for="h5_force_redirect1" class="cb-enable <?php if($list_config['h5_force_redirect'] == 1): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_open')); ?></label>
                        <label for="h5_force_redirect0" class="cb-disable <?php if($list_config['h5_force_redirect'] == 0): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_close')); ?></label>
                        <input id="h5_force_redirect1" name="h5_force_redirect" value="1" type="radio" <?php if($list_config['h5_force_redirect'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="h5_force_redirect0" name="h5_force_redirect" value="0" type="radio" <?php if($list_config['h5_force_redirect'] == 0): ?> checked="checked"<?php endif; ?>>
                    </div>
                    <p class="notic"><?php echo htmlentities(lang('h5_force_redirect_tips')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('baidu_map_ak_key')); ?></dt>
                <dd>
                    <input id="baidu_ak" name="baidu_ak" value="<?php echo htmlentities($list_config['baidu_ak']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('icp_number')); ?></dt>
                <dd>
                    <input id="icp_number" name="icp_number" value="<?php echo htmlentities($list_config['icp_number']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            
            <dl>
                <dt><?php echo htmlentities(lang('site_phone')); ?></dt>
                <dd>
                    <input id="site_phone" name="site_phone" value="<?php echo htmlentities($list_config['site_phone']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            
            <dl>
                <dt><?php echo htmlentities(lang('site_tel400')); ?></dt>
                <dd>
                    <input id="site_tel400" name="site_tel400" value="<?php echo htmlentities($list_config['site_tel400']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('site_email')); ?></dt>
                <dd>
                    <input id="site_email" name="site_email" value="<?php echo htmlentities($list_config['site_email']); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('flow_static_code')); ?></dt>
                <dd>
                    <textarea id="flow_static_code" name="flow_static_code"><?php echo htmlentities($list_config['flow_static_code']); ?></textarea>
                    <span class="err"></span>
                    <p class="notic"><?php echo htmlentities(lang('flow_static_code_notice')); ?></p>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('cache_open')); ?></dt>
                <dd>
                    <div class="onoff">
                        <label for="cache_open1" class="cb-enable <?php if($list_config['cache_open'] == 1): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_open')); ?></label>
                        <label for="cache_open0" class="cb-disable <?php if($list_config['cache_open'] == 0): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_close')); ?></label>
                        <input id="cache_open1" name="cache_open" value="1" type="radio" <?php if($list_config['cache_open'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="cache_open0" name="cache_open" value="0" type="radio" <?php if($list_config['cache_open'] == 0): ?> checked="checked"<?php endif; ?>>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('site_state')); ?></dt>
                <dd>
                    <div class="onoff">
                        <label for="site_state1" class="cb-enable <?php if($list_config['site_state'] == 1): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_open')); ?></label>
                        <label for="site_state0" class="cb-disable <?php if($list_config['site_state'] == 0): ?>selected<?php endif; ?>"><?php echo htmlentities(lang('ds_close')); ?></label>
                        <input id="site_state1" name="site_state" value="1" type="radio" <?php if($list_config['site_state'] == 1): ?> checked="checked"<?php endif; ?>>
                        <input id="site_state0" name="site_state" value="0" type="radio" <?php if($list_config['site_state'] == 0): ?> checked="checked"<?php endif; ?>>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt><?php echo htmlentities(lang('closed_reason')); ?></dt>
                <dd>
                    <textarea id="closed_reason" name="closed_reason"><?php echo htmlentities($list_config['closed_reason']); ?></textarea>
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl>
                <dt></dt>
                <dd><a href="JavaScript:void(0);" class="btn" onclick="document.form1.submit()"><?php echo htmlentities(lang('ds_confirm_submit')); ?></a></dd>
            </dl>
        </div>
    </form>
    
</div>





<script>
    $(function(){
        $("#site_logo").change(function () {
            $("#textfield1").val($("#site_logo").val());
        });
        $("#member_logo").change(function () {
            $("#textfield2").val($("#member_logo").val());
        });
        $("#seller_center_logo").change(function () {
            $("#textfield3").val($("#seller_center_logo").val());
        });

        $("#site_mobile_logo").change(function () {
            $("#textfield4").val($("#site_mobile_logo").val());
        });
        $("#site_logowx").change(function () {
            $("#textfield5").val($("#site_logowx").val());
        });
        $("#file_business_licence").change(function () {
            $("#textfield6").val($("#file_business_licence").val());
        });
    })
</script>


