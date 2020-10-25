<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：北京市普艾斯科技有限公司
 项目：simcms_锐车1.0
 电话：010-58480317
 Q  Q: 228971357
 网址：http://www.simcms.net
 simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/
if (!defined('APP_IN')) exit('Access Denied');

include ('page.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$tpl -> assign('shopid', $id); 
// 店铺信息
$data = $db -> row_select_one('member', "id=" . $id);
$data['shopdetail'] = htmlspecialchars_decode($data['shopdetail']);
if (!$data) showmsg('参数错误', -1);
if ($data['ischeck']!=1) showmsg('该商家信息暂未通过审核！', -1);
$tpl -> assign('shop', $data); 

//销售代表
$dealerlist = $db -> row_select('member_dealer','uid='.$id);
$tpl -> assign('dealerlist', $dealerlist);

// 允许操作
$ac_arr = array('index' => '店铺首页', 'about' => '店铺介绍', 'cars' => '二手车', 'news' => '促销信息', 'ask' => '问答');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'index';

$brand_search = arr_brand_with_search();
$select_brand_search = select_make('', $brand_search, '请选择品牌');
$tpl -> assign('selectbrandsearch', $select_brand_search);

if ($ac=="index") {
	$tpl -> assign('smstate', 1);
	// 查询条件
	$where = "uid=" . $id . " and issell=0 and isshow=1";
	if (!empty($_GET['carsort'])) {
		$where .= " and p_sort = " . $_GET['carsort'];
	} 
	if (!empty($_GET['p_type'])) {
		$where .= " and p_type = 2";
	} 
	// 排序
	$_SESSION['order'] = isset($_GET['order'])?$_GET['order']:1;
	$orderby = "";
	if (!empty($_SESSION['order'])) {
		switch ($_SESSION['order']) {
			case 1:
				$orderby = "listtime desc";
				break;
			case 2:
				$orderby = "listtime asc";
				break;
			case 3:
				$orderby = "p_price asc";
				break;
			case 4:
				$orderby = "p_price desc";
				break;
			case 5:
				$orderby = "p_year desc,p_month desc";
				break;
			case 6:
				$orderby = "p_year asc,p_month asc";
				break;
			case 7:
				$orderby = "p_hits desc";
				break;
			case 8:
				$orderby = "p_addtime desc";
				break;
		} 
	} 
	// 每页显示
	$_SESSION['pagenum'] = isset($_GET['pagenum']) ? $_GET['pagenum'] : 20;
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'cars', $where, '*', $_SESSION['pagenum'], $orderby);
	$listnum = $Page -> total_num;
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		if (!empty($value['p_mainpic'])) {
			$pic = explode(".", $value['p_mainpic']);
			$list[$key]['p_mainpic'] = $pic[0] . "_small" . "." . $pic[1];
		} 
		$list[$key]['p_shortname'] = _substr($value['p_allname'], 0, 26);
		$list[$key]['listtime'] = date('Y-m-d', $value['listtime']);
		$list[$key]['p_price'] = intval($value['p_price']) == 0 ? "面谈" : "￥" . $value['p_price']."万";
		if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
		$list[$key]['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
	} 
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('carslist', $list);
	$tpl -> display('default/' . $settings['templates'] . '/shop_index.html');
} elseif ($ac == "cars") {
	$tpl -> assign('smstate', 2);
	// 查询条件
	$arr_p = array('1' => '3万以下', '2' => '3-5万', '3' => '5-8万', '4' => '8-12万', '5' => '12-18万', '6' => '18-24万', '7' => '24-35万', '8' => '35-50万', '9' => '50-100万', '10' => '100万以上');
	$tpl -> assign('arr_price', $arr_p);
	$arr_a = array('1' => '1年以内', '2' => '2年以内', '3' => '3年以内', '4' => '4年以内', '5' => '5年以内', '6' => '6年以内', '7' => '6年以上');
	$tpl -> assign('arr_age', $arr_a);
	$arr_b = arr_brand_recom(6);
	$tpl -> assign('arr_brand', $arr_b); 

	// 查询条件
	$where = "uid=" . $id . " and issell=0 and isshow=1";
	if (!empty($_GET['carsort'])) {
		$where .= " and p_sort = " . $_GET['carsort'];
	} 

	if (isset($_GET['c'])) {
		$arr_c = explode("_", trim($_GET['c'])); 
		// 价格
		if ($arr_c['0'] == "p") {
			if (isset($arr_c[1])) {
				setMyCookie("sprice", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['price']) and $_COOKIE['price'] == 0) {
				setMyCookie("sprice", '', time() - COOKIETIME);
			} 
		} 
		// 车龄
		elseif ($arr_c['0'] == "a") {
			if (isset($arr_c[1])) {
				setMyCookie("sage", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['age']) and $_COOKIE['age'] == 0) {
				setMyCookie("sage", '', time() - COOKIETIME);
			} 
		} 
		// 车型
		elseif ($arr_c['0'] == "m") {
			if (isset($arr_c[1])) {
				setMyCookie("smodel", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['model']) and $_COOKIE['model'] == 0) {
				setMyCookie("smodel", '', time() - COOKIETIME);
			} 
		} 
		// 品牌
		elseif ($arr_c['0'] == "b") {
			if (isset($arr_c[1])) {
				setMyCookie("sbrand", intval($arr_c[1]), time() + COOKIETIME);
				if (!empty($_GET['sb'])) {
					setMyCookie("ssubbrand", intval($_GET['sb']), time() + COOKIETIME);
				} 
			} 
			if (isset($_COOKIE['sbrand']) and $_COOKIE['sbrand'] == 0) {
				setMyCookie("sbrand", '', time() - COOKIETIME);
				setMyCookie("ssubbrand", '', time() - COOKIETIME);
			} 
		} 
	} 

	if (isset($_COOKIE['ssubbrand']) and isset($_GET['sb']) and $_GET['sb'] == 0) {
		setMyCookie("ssubbrand", '', time() - COOKIETIME);
	} 

	if (isset($_COOKIE['sbrand']) and $_COOKIE['sbrand'] <> 0) {
		$where .= " and p_brand = " . $_COOKIE['sbrand'];
	} 

	if (isset($_COOKIE['ssubbrand']) and $_COOKIE['ssubbrand'] <> 0) {
		$where .= " and p_subbrand = " . $_COOKIE['ssubbrand'];
	} 

	if (isset($_COOKIE['sprice']) and $_COOKIE['sprice'] <> 0) {
		switch ($_COOKIE['sprice']) {
			case 1:
				$where .= " and p_price > 0 and p_price <= 3";
				break;
			case 2:
				$where .= " and p_price > 3 and p_price <= 5";
				break;
			case 3:
				$where .= " and p_price > 5 and p_price <= 8";
				break;
			case 4:
				$where .= " and p_price > 8 and p_price <= 12";
				break;
			case 5:
				$where .= " and p_price > 12 and p_price <= 18";
				break;
			case 6:
				$where .= " and p_price > 18 and p_price <= 35";
				break;
			case 7:
				$where .= " and p_price > 18 and p_price <= 35";
				break;
			case 8:
				$where .= " and p_price > 35 and p_price <= 50";
				break;
			case 9:
				$where .= " and p_price > 50 and p_price <= 100";
				break;
			case 10:
				$where .= " and p_price > 100";
				break;
			default:
				$where .= "";
		} 
	} 

	if (isset($_COOKIE['sage']) and $_COOKIE['sage'] <> 0) {
		$compareyear = date("Y") - $_COOKIE['sage'];
		switch ($_COOKIE['sage']) {
			case 7:
				$where .= " and p_year < " . $compareyear;
				break;
			default:
				$where .= " and p_year > " . $compareyear;
		} 
	} 

	if (isset($_COOKIE['smodel']) and $_COOKIE['smodel'] <> 0) {
		$where .= " and p_model = " . $_COOKIE['smodel'];
	} 

	if (isset($_COOKIE['sarea']) and $_COOKIE['sarea'] <> 0) {
		$where .= " and cid = " . $_COOKIE['sarea'];
	} 
	// 关键字
	if (isset($_GET['k']) and $_GET['k'] != "" and $_GET['keywords'] != "请输入要搜索的关键词,如:宝马") {
		setMyCookie("skeywords", $_GET['k'], time() + COOKIETIME);
	} elseif (isset($_GET['k']) and $_GET['k'] == "") {
		setMyCookie("skeywords", '', time() - COOKIETIME);
	} 

	if (!empty($_COOKIE['skeywords'])) {
		$where .= " AND (`p_allname` like '%" . $_COOKIE['skeywords'] . "%' or `p_keyword` like '%" . $_COOKIE['skeywords'] . "%' or `p_no` like '%" . $_COOKIE['skeywords'] . "%')";
	} 

	if (!empty($_GET['p_type'])) {
		$where .= " and p_type = 2";
	} 
	// 排序
	$_SESSION['order'] = isset($_GET['order'])?$_GET['order']:1;
	$orderby = "";
	if (!empty($_SESSION['order'])) {
		switch ($_SESSION['order']) {
			case 1:
				$orderby = "listtime desc";
				break;
			case 2:
				$orderby = "listtime asc";
				break;
			case 3:
				$orderby = "p_price asc";
				break;
			case 4:
				$orderby = "p_price desc";
				break;
			case 5:
				$orderby = "p_year desc,p_month desc";
				break;
			case 6:
				$orderby = "p_year asc,p_month asc";
				break;
			case 7:
				$orderby = "p_hits desc";
				break;
			case 8:
				$orderby = "p_addtime desc";
				break;
		} 
	} 

	// 每页显示
	$_SESSION['pagenum'] = isset($_GET['pagenum']) ? $_GET['pagenum'] : 20;
	include(INC_DIR . 'Page.class.php');
	if($data['shoptype']==2){
		$Page = new Page($db -> tb_prefix . 'newcars', $where, '*', $_SESSION['pagenum'], $orderby);
	}
	else{
		$Page = new Page($db -> tb_prefix . 'cars', $where, '*', $_SESSION['pagenum'], $orderby);
	}
	$listnum = $Page -> total_num;
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		if (!empty($value['p_mainpic'])) {
			$pic = explode(".", $value['p_mainpic']);
			$list[$key]['p_mainpic'] = $pic[0] . "_small" . "." . $pic[1];
		} 
		$list[$key]['p_shortname'] = _substr($value['p_allname'], 0, 26);
		$list[$key]['listtime'] = date('Y-m-d', $value['listtime']);
		$list[$key]['p_price'] = intval($value['p_price']) == 0 ? "面谈" : "￥" . $value['p_price'] ."万";
		if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
		$list[$key]['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
	} 
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('carslist', $list);
	$tpl -> display('default/' . $settings['templates'] . '/shop_cars.html');
} elseif ($ac == "about") {
	$tpl -> assign('smstate', 3);
	
	$address =  $data['address'];
	
	$apiurl = "http://api.map.baidu.com/geocoder/v2/?address=".$address."&output=json&ak=3075ca513c180f154a83789e3f93c1c4&callback=showLocation";
	$file = file_get_contents($apiurl);
	$file = str_replace("showLocation&&showLocation","",$file);
	$file = substr($file, 0, -1);
	$file = substr($file,1);
	$arrpoint = json_decode($file,true);

	$x = $arrpoint['result']['location']['lng'];
	$y = $arrpoint['result']['location']['lat'];
	
	$tpl -> assign('x', $x);
	$tpl -> assign('y', $y);
	$tpl -> display('default/' . $settings['templates'] . '/shop_about.html');
} elseif ($ac == "news") {
	$tpl -> assign('smstate', 4);
	if(isset($_GET['nid'])){
		$data = $db -> row_select_one('member_news', "n_id=" . intval($_GET['nid']));
		$data['addtime'] = date('Y-m-d H:i:s', $data['n_addtime']);
		$data['n_info'] = htmlspecialchars_decode($data['n_info']);
		$tpl -> assign('news', $data); 
		$tpl -> display('default/' . $settings['templates'] . '/shop_news.html');
	}
	else{
		$where = 'uid=' . $id;
		include(INC_DIR.'Page.class.php');
		$Page = new Page($db->tb_prefix.'member_news',$where,'*','20','n_id desc');
		$list = $Page->get_data();
		foreach($list as $key => $value){
			$list[$key]['addtime'] = date('Y-m-d H:i:s',$value['n_addtime']);
		}
		$button_basic = $Page->button_basic();
		$button_select = $Page->button_select();
		$tpl->assign( 'newslist', $list );
		$tpl->assign( 'button_basic', $button_basic );
		$tpl -> display('default/' . $settings['templates'] . '/shop_newslist.html');
	}
} elseif ($ac == "ask") {
	$tpl -> assign('smstate', 5);
	if (submitcheck('a')) {
		if(isset($_POST['anonymity']) and $_POST['anonymity']==1){
			 $post['auid'] = 0;
			 $post['ask'] = $_POST['ask'];
		}
		else{
			$arr_not_empty = array('username'=>'用户名不能为空','password'=>'密码不能为空');
			can_not_be_empty($arr_not_empty,$_POST);
			$rs_user = $db->row_select_one('member',"username='".trim($_POST['username'])."' AND password='".md5(trim($_POST['password']))."'");
			if (!$rs_user) showmsg('用户不存在或密码错误',-1);
			$post['auid'] = $rs_user['id'];
			$post['ask'] = $_POST['ask'];
		}
        $post['uid'] = $_SESSION['USER_ID'];
		$post['asktime'] = time();
        $rs = $db->row_insert('member_feedback',$post);
		showmsg("您的留言已提交成功！", -1);
	}
	else{
		include(INC_DIR.'Page.class.php');
		$Page = new Page($db->tb_prefix.'member_feedback',"reply<>'' and uid=".$id,'*','20','id desc');
		$list = $Page->get_data();
		foreach($list as $key => $value){
			$list[$key]['asktime'] = date('Y-m-d H:i:s',$value['asktime']);
			if($value['auid']==0){
				$list[$key]['username'] = "匿名";
			}
			else{
				$rs_user = $db->row_select_one('member',"id='".$value['auid']."'");
				$list[$key]['username'] = $rs_user['username'];
			}
		}
		$button_basic = $Page->button_basic();
		$button_select = $Page->button_select();
		$tpl->assign( 'feedbacklist', $list );
		$tpl->assign( 'button_basic', $button_basic );
	}
	$tpl -> display('default/' . $settings['templates'] . '/shop_ask.html');
} 

?>