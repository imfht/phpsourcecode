<?php
/**
 * wch_goods.php UTF8
 * User: weicaihong.com
 * Date: 14-10-14 17:14
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_goods = $prefix.'goods';
// 商品必须条件
$goods_is = ' AND is_delete = 0 AND is_on_sale = 1 AND is_alone_sale = 1';
$limit = 0;
// 显示条数
$offset = 6;

// 获取字段 goods_id goods_name goods_img shop_price market_price
$filed = ' goods_id,goods_name,goods_img,shop_price,market_price ';
// 排序
$order = 'ORDER BY sort_order ASC, last_update DESC ';
$keyword = $post_data['keyword'];
//用户微信id
$wxid = $post_data['wxid'];

// 加载登录模块
//require('wch_wxlogin.php');

// 新品
if($keyword == 'new')
{
	$where  = " `is_new` = 1 ";
}
// 精品
elseif($keyword == 'best')
{
	$where  = " `is_best` = 1 ";
}
// 热销
elseif($keyword == 'hot')
{
	$where  = " `is_hot` = 1 ";
}
// 搜索商品
elseif(!empty($keyword))
{
	$goods_name = $keyword;
	$where  = " `goods_name` LIKE '%$goods_name%' ";
}

$query_sql = "SELECT $filed FROM `$tb_goods` WHERE $where $goods_is $order LIMIT $limit , $offset ";

// 查询sql
$sth = $pdo_db->prepare($query_sql);
$sth->execute();
$data = array();
$data = $sth->fetchAll(PDO::FETCH_ASSOC);

// 转换数据
foreach($data as $k=>$v)
{
	// 生成微商城访问网址(注：最后加密后的wxid还要进行urlencode，否则特殊字符串会被过滤掉，导致获取的参数不是原始的)
	$data[$k]['uri'] = $w_shop_url.'goods.php?id='.$v['goods_id'].'&wxid='.urlencode(wch_encrypt($wxid));

	// 商品图片
	$data[$k]['image'] = $config_url.$v['goods_img'];
}

// 转换为json
$json_data = json_encode($data);

// 全部数据以UTF8 编码
if($ec_charset != 'UTF8')
{
	$json_data = mb_convert_encoding($json_data,'UTF-8','GBK');
}

// 输出
require('wch_json.php');

