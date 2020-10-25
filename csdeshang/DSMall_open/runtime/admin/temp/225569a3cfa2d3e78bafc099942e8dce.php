<?php /*a:3:{s:68:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\dashboard\welcome.html";i:1591845922;s:64:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\header.html";i:1591845922;s:69:"D:\phpstudy_pro\WWW\git\DSMall\app\admin\view\public\admin_items.html";i:1591845922;}*/ ?>
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







<div class="page welcome">
    <!--
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo htmlentities(lang('ds_welcome')); ?></h3>
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
    -->

    <ul class="info-message">
        <?php if($version_message): ?>
        <li><?php echo htmlentities($version_message); ?></li>
        <?php endif; ?>
    </ul>
    <div class="title"><?php echo htmlentities(lang('dashboard_base_info')); ?></div>
    <div class="info-total">
        <ul>
            <li>
                <a href="<?php echo url('Member/member'); ?>">
                    <div class="p_header bg-79BAD0">
                        <i class="iconfont">&#xe667;</i>
                    </div>
                    <div class="p_content">
                        <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_member_des')); ?></div>
                        <div class="p_num" id="statistics_member">0</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo url('Store/store'); ?>">
                    <div class="p_header bg-EC7E7F">
                        <i class="iconfont">&#xe6ec;</i>
                    </div>
                    <div class="p_content">
                        <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_store_des')); ?></div>
                        <div class="p_num" id="statistics_store">0</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo url('Goods/index'); ?>">
                    <div class="p_header bg-86CE86">
                        <i class="iconfont">&#xe732;</i>
                    </div>
                    <div class="p_content">
                        <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_total_goods')); ?></div>
                        <div class="p_num" id="statistics_goods">0</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo url('Order/index'); ?>">
                    <div class="p_header bg-E9BB5F">
                        <i class="iconfont">&#xe69c;</i>
                    </div>
                    <div class="p_content">
                        <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_trade_des')); ?></div>
                        <div class="p_num" id="statistics_order">0</div>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo url('Operation/setting'); ?>">
                    <div class="p_header bg-6CCDA5">
                        <i class="iconfont">&#xe704;</i>
                    </div>
                    <div class="p_content">
                        <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_stat_des')); ?></div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="title"><?php echo htmlentities(lang('dashboard_weekly_overview')); ?></div>
    <div class="info-chart">
        <div class="week">
            <ul class="tab">
                <li class="active"><?php echo htmlentities(lang('dashboard_member_growth')); ?></li>
                <li><?php echo htmlentities(lang('dashboard_goods_growth')); ?></li>
                <li><?php echo htmlentities(lang('dashboard_store_growth')); ?></li>
                <li><?php echo htmlentities(lang('dashboard_order_growth')); ?></li>
            </ul>
            <div class="tab-content">
                <div class="content show">
                    <div id="stat_json_week_member" class="w100pre close_float" style="height:300px"></div>
                </div>
                <div class="content">
                    <div id="stat_json_week_goods" class="w100pre close_float" style="height:300px"></div>
                </div>
                <div class="content">
                    <div id="stat_json_week_store" class="w100pre close_float" style="height:300px"></div>
                </div>
                <div class="content">
                    <div id="stat_json_week_order" class="w100pre close_float" style="height:300px"></div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $(".info-chart .week li").each(function(index) {
                $(this).click(function() {
                    $("li.active").removeClass("active"); //注意这里
                    $(this).addClass("active"); //注意这里
                    $(".tab-content>div.show").removeClass("show");
                    $(".tab-content>div").eq(index).addClass("show");
                });
            })
        });
    </script>
    
    
        
    <div class="title"><?php echo htmlentities(lang('dashboard_dealt')); ?></div>
    <div class="info-statistical clearfix">
        <!--会员-->
        <div class="info-panel">
            <div class="mt"><?php echo htmlentities(lang('ds_member')); ?></div>
            <div class="mc">
                <ul>
                    <li class="none">
                        <a href="<?php echo url('Member/member'); ?>">
                            <div class="p_header bg-79BAD0">
                                <i class="iconfont">&#xe667;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_new_add')); ?></div>
                                <div class="p_num" id="statistics_week_add_member">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Predeposit/pdcash_list'); ?>">
                            <div class="p_header bg-EC7E7F">
                                <i class="iconfont">&#xe6f3;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_predeposit_get')); ?></div>
                                <div class="p_num" id="statistics_cashlist">0</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!--店铺-->
        <div class="info-panel">
            <div class="mt"><?php echo htmlentities(lang('ds_store')); ?></div>
            <div class="mc">
                <ul>
                    <li class="none">
                        <a href="<?php echo url('Store/store_joinin'); ?>">
                            <div class="p_header bg-86CE86">
                                <i class="iconfont">&#xe6ec;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_store_new')); ?></div>
                                <div class="p_num" id="statistics_store_joinin">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Store/store_bind_class_applay_list',['state'=>0]); ?>">
                            <div class="p_header bg-E9BB5F">
                                <i class="iconfont">&#xe652;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_category_apply')); ?></div>
                                <div class="p_num" id="statistics_store_bind_class_applay">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Store/reopen_list',['storereopen_state'=>1]); ?>">
                            <div class="p_header bg-79BAD0">
                                <i class="iconfont">&#xe731;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_reopen_apply')); ?></div>
                                <div class="p_num" id="statistics_store_reopen_applay">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Store/store',['store_state'=>'expired']); ?>">
                            <div class="p_header bg-EC7E7F">
                                <i class="iconfont">&#xe70c;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_expired')); ?></div>
                                <div class="p_num" id="statistics_store_expired">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Store/store',['store_state'=>'expire']); ?>">
                            <div class="p_header bg-9C6CCD">
                                <i class="iconfont">&#xe70c;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_expire')); ?></div>
                                <div class="p_num" id="statistics_store_expire">0</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!--商品-->
        <div class="info-panel">
            <div class="mt"><?php echo htmlentities(lang('ds_goods')); ?></div>
            <div class="mc">
                <ul>
                    <li class="none">
                        <a href="<?php echo url('Goods/index'); ?>">
                            <div class="p_header bg-E9BB5F">
                                <i class="iconfont">&#xe732;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_new_add')); ?></div>
                                <div class="p_num" id="statistics_week_add_product">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Goods/index',['type'=>'waitverify','search_verify'=>10]); ?>">
                            <div class="p_header bg-79BAD0">
                                <i class="iconfont">&#xe732;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_goods_waitverify')); ?></div>
                                <div class="p_num" id="statistics_product_verify">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Inform/inform_list'); ?>">
                            <div class="p_header bg-EC7E7F">
                                <i class="iconfont">&#xe747;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_inform')); ?></div>
                                <div class="p_num" id="statistics_inform_list">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Brand/brand_apply'); ?>">
                            <div class="p_header bg-9C6CCD">
                                <i class="iconfont">&#xe6b0;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_brnad_applay')); ?></div>
                                <div class="p_num" id="statistics_brand_apply">0</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!--交易-->
        <div class="info-panel">
            <div class="mt"><?php echo htmlentities(lang('ds_trade')); ?></div>
            <div class="mc">
                <ul>
                    <li class="none">
                        <a href="<?php echo url('Refund/refund_manage'); ?>">
                            <div class="p_header bg-86CE86">
                                <i class="iconfont">&#xe6f3;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('order_refund')); ?></div>
                                <div class="p_num" id="statistics_refund">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Returnmanage/return_manage'); ?>">
                            <div class="p_header bg-EC7E7F">
                                <i class="iconfont">&#xe6f3;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('order_return')); ?></div>
                                <div class="p_num" id="statistics_return">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Vrrefund/refund_manage'); ?>">
                            <div class="p_header bg-86CE86">
                                <i class="iconfont">&#xe654;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('ds_vrrefund')); ?></div>
                                <div class="p_num" id="statistics_vr_refund">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Complain/complain_new_list'); ?>">
                            <div class="p_header bg-79BAD0">
                                <i class="iconfont">&#xe6b4;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_complain')); ?></div>
                                <div class="p_num" id="statistics_complain_new_list">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Complain/complain_handle_list'); ?>">
                            <div class="p_header bg-6C93CD">
                                <i class="iconfont">&#xe6b4;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_complain_handle')); ?></div>
                                <div class="p_num" id="statistics_complain_handle_list">0</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!--营销-->
        <div class="info-panel">
            <div class="mt"><?php echo htmlentities(lang('ds_operation')); ?></div>
            <div class="mc">
                <ul>
                    <li class="none">
                        <a href="<?php echo url('Groupbuy/index'); ?>">
                            <div class="p_header bg-E9BB5F">
                                <i class="iconfont">&#xe732;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_groupbuy')); ?></div>
                                <div class="p_num" id="dashboard_wel_groupbuy">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Pointorder/pointorder_list',['porderstate'=>'waitship']); ?>">
                            <div class="p_header bg-6CCDA5">
                                <i class="iconfont">&#xe6b7;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_point_order')); ?></div>
                                <div class="p_num" id="dashboard_wel_point_order">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Bill/show_statis',['bill_state'=>'2']); ?>">
                            <div class="p_header bg-6C93CD">
                                <i class="iconfont">&#xe69c;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_check_billno')); ?></div>
                                <div class="p_num" id="dashboard_wel_check_billno">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Bill/show_statis',['bill_state'=>'3']); ?>">
                            <div class="p_header bg-6CCDA5">
                                <i class="iconfont">&#xe74d;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_pay_billno')); ?></div>
                                <div class="p_num" id="dashboard_wel_pay_billno">0</div>
                            </div>
                        </a>
                    </li>
                    <li class="none">
                        <a href="<?php echo url('Mallconsult/index'); ?>">
                            <div class="p_header bg-86CE86">
                                <i class="iconfont">&#xe750;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('ds_mall_consult')); ?></div>
                                <div class="p_num" id="statistics_mall_consult">0</div>
                            </div>
                        </a>
                    </li>
                    <!--
                    <li class="none">
                        <a href="<?php echo url('Delivery/index',['sign'=>'verify']); ?>">
                            <div class="p_header bg-9C6CCD">
                                <i class="iconfont">&#xe676;</i>
                            </div>
                            <div class="p_content">
                                <div class="p_text"><?php echo htmlentities(lang('dashboard_wel_delivery')); ?></div>
                                <div class="p_num" id="dashboard_wel_delivery">0</div>
                            </div>
                        </a>
                    </li>
                    -->
                </ul>
            </div>
        </div>
    </div>
    
    <div class="title"><?php echo htmlentities(lang('dashboard_wel_version_info')); ?></div>
    <div class="info-system">
        <table cellpadding="0" cellspacing="0" class="system_table">
            <tbody>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_version')); ?></td>
                    <td><?php echo htmlentities($statistics['version']); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_install_date')); ?></td>
                    <td><?php echo htmlentities($statistics['setup_date']); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_program_development')); ?></td>
                    <td><?php echo htmlentities(lang('dashboard_wel_deshangwangluo')); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_all_right_reserved')); ?></td>
                    <td><?php echo htmlentities(lang('dashboard_wel_piracy_must_be_studied')); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_aboutus_website')); ?>:</td>
                    <td><a href="http://www.csdeshang.com" target="_blank"><?php echo htmlentities(lang('dashboard_aboutus_website')); ?></a></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_aboutus_bbs')); ?>:</td>
                    <td><a href="http://bbs.csdeshang.com" target="_blank"><?php echo htmlentities(lang('dashboard_wel_communication_bbs')); ?></a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="title"><?php echo htmlentities(lang('dashboard_wel_sys_info')); ?></div>
    <div class="info-system">
        <table cellpadding="0" cellspacing="0" class="system_table">
            <tbody>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_thinkphp_version')); ?></td>
                    <td><?php echo htmlentities($statistics['tp_version']); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_class_library_file_suffix')); ?></td>
                    <td>.php</td>
                </tr>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_server_os')); ?></td>
                    <td><?php echo htmlentities($statistics['os']); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_server_domain_ip')); ?>:</td>
                    <td><?php echo htmlentities($statistics['domain']); ?> [ <?php echo htmlentities($statistics['ip']); ?> ]</td>
                </tr>
                <tr>
                    <td class="gray_bg">WEB <?php echo htmlentities(lang('dashboard_wel_server')); ?></td>
                    <td><?php echo htmlentities($statistics['web_server']); ?></td>
                    <td class="gray_bg">PHP <?php echo htmlentities(lang('dashboard_wel_version')); ?></td>
                    <td><?php echo htmlentities($statistics['php_version']); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg">MYSQL <?php echo htmlentities(lang('dashboard_wel_version')); ?></td>
                    <td><?php echo htmlentities($statistics['sql_version']); ?></td>
                    <td class="gray_bg">GD <?php echo htmlentities(lang('dashboard_wel_version')); ?>:</td>
                    <td><?php echo htmlentities($statistics['gdinfo']); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_file_uplode_limit')); ?>:</td>
                    <td><?php echo htmlentities($statistics['fileupload']); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_max_occupied_memory')); ?>:</td>
                    <td><?php echo htmlentities($statistics['memory_limit']); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_max_ex_time')); ?>:</td>
                    <td><?php echo htmlentities($statistics['max_ex_time']); ?></td>
                    <td class="gray_bg"><?php echo htmlentities(lang('dashboard_wel_safe_mode')); ?>:</td>
                    <td><?php echo htmlentities($statistics['safe_mode']); ?></td>
                </tr>
                <tr>
                    <td class="gray_bg">Zlib<?php echo htmlentities(lang('dashboard_wel_support')); ?>:</td>
                    <td><?php echo htmlentities($statistics['zlib']); ?></td>
                    <td class="gray_bg">Curl<?php echo htmlentities(lang('dashboard_wel_support')); ?>:</td>
                    <td><?php echo htmlentities($statistics['curl']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="<?php echo htmlentities(PLUGINS_SITE_ROOT); ?>/highcharts/highcharts.js"></script>
    <script type="text/javascript">
        var normal = [];
        var work = ['week_add_member', 'week_add_product','store_joinin', 'store_bind_class_applay', 'store_reopen_applay', 'store_expired', 'store_expire', 'brand_apply', 'cashlist', 'groupbuy_verify_list', 'points_order', 'complain_new_list', 'complain_handle_list', 'product_verify', 'inform_list', 'refund', 'return', 'vr_refund', 'cms_article_verify', 'cms_picture_verify', 'circle_verify', 'check_billno', 'pay_billno', 'mall_consult', 'delivery_point', 'offline'];
        $(document).ready(function () {
            $.getJSON("<?php echo url('Dashboard/statistics'); ?>", function (data) {
                $.each(data, function (k, v) {
                    $("#statistics_" + k).html(v);
                    if (v != 0 && $.inArray(k, work) !== -1) {
                        $("#statistics_" + k).parent().parent().parent().removeClass('none').addClass('high');
                    } else if (v == 0 && $.inArray(k, normal) !== -1) {
                        $("#statistics_" + k).parent().parent().parent().removeClass('normal').addClass('none');
                    }
                });
            });
        });
        $('#stat_json_week_member').highcharts(<?php echo $stat_json_week_member; ?>);
        $('#stat_json_week_goods').highcharts(<?php echo $stat_json_week_goods; ?>);
        $('#stat_json_week_store').highcharts(<?php echo $stat_json_week_store; ?>);
        $('#stat_json_week_order').highcharts(<?php echo $stat_json_week_order; ?>);
    </script>
</div>