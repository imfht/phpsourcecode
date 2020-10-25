<?php
/**
 * wch_consignee.php UTF8
 * User: djks
 * Date: 15/4/17 17:10
 * Copyright: http://www.weicaihong.com
 */

if($_GET['shop_user_id'] == $_SESSION['user_id']) {

    $wch_address = array(
        'province' => $_GET['province'],
        'city' => $_GET['city'],
        'district' => $_GET['district'],
        'address' => $_GET['address'],
        'consignee' => $_GET['username'],
        'tel' => $_GET['phone'],
    );
    if ($_GET['address'] == 'post') {
        if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger")) {
            $wch_back = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            wch_header($wch_back);
        }
    }

    $wch_address['address'] = $wch_address['province'] . $wch_address['city'] . $wch_address['district'] . $wch_address['address'];
    if ($wch_address['address'])
    {
        $smarty->assign('wch_address',       $wch_address);
        $smarty->assign('consignee',       $wch_address);
    }
}

if($_POST)
{


    if ($post_data['shop_user_id'] > 0)
    {
        include_once(ROOT_PATH . 'includes/lib_transaction.php');

        // 根据微信地图获取region_id

        // 表前缀 $prefix
        $tb_users = $prefix.'region';

        $post_data['province'] = mb_substr($post_data['province'],0,2,'UTF-8');
        $post_data['city'] = mb_substr($post_data['city'],0,2,'UTF-8');
        $post_data['district1'] = $post_data['district'];
        if(strlen($post_data['district'])>9)
        {
            $post_data['district'] = mb_substr($post_data['district'],0,2,'UTF-8');
        }
        $post_data['city'] = mb_substr($post_data['city'],0,2,'UTF-8');
        $province_sql = "SELECT region_id FROM " . $tb_users . " WHERE region_name LIKE '%" . $post_data['province'] . "%' AND region_type = 1" ;
        // 查询sql
        $sth = $pdo_db->prepare($province_sql);
        $sth->execute();
        $data = array();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        $province_region = $data['region_id'];

        $city_sql = "SELECT region_id FROM " . $tb_users . " WHERE region_name LIKE '%" . $post_data['city'] . "%' AND region_type = 2" ;
        // 查询sql
        $sth = $pdo_db->prepare($city_sql);
        $sth->execute();
        $data = array();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        $city_region = $data['region_id'];

        $district_sql = "SELECT region_id FROM " . $tb_users . " WHERE region_name LIKE '%" . $post_data['district'] . "%' AND region_type = 3" ;
        // 查询sql
        $sth = $pdo_db->prepare($district_sql);
        $sth->execute();
        $data = array();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        $district_region = $data['region_id'];

        /*
         * 保存收货人信息
         */

        $consignee = array(
            'consignee'     => $post_data['consignee'],
            'country'       => 1,
            'province'      => $province_region,
            'city'          => $city_region,
            'district'      => $district_region,
            'address'       => $post_data['province'].$post_data['city'].$post_data['district1'].$post_data['address'],
            'tel'           => $post_data['tel'],
            'mobile'        => $post_data['tel'],
        );


        /* 如果用户已经登录，则保存收货人信息 */
        $consignee['user_id'] = $post_data['shop_user_id'];

        save_consignee($consignee, true);
    }


    // 输出json
    require_once('wch_json.php');
}
