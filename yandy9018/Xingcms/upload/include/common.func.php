<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：方云工作室
 项目：xingcms v1.0.0
 Q  Q: 1378664755
 网址：http://xingcms.cn
 simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/

//判断会员登陆
function is_user_login()
{
    if (empty($_SESSION['USER_ID']) || empty($_SESSION['USER_NAME'])) return false;
    return true;
}

// 判断管理员登陆
function is_admin_login()
{
	if (empty($_SESSION['ADMIN_UID']) || empty($_SESSION['ADMIN_NAME'])) return false;
	return true;
} 

// 管理员用户组数组
function arr_admingroup() {
	global $db;
	$data = $db -> row_select('admingroup', "1=1", 'id,groupname', 0, 'id asc');
	return get_array($data, 'id', 'groupname');
} 

// 获得系统配置
function settings()
{
	global $db;
	$rs_settings = $db -> row_select('settings');
	$arr_settings = array();
	foreach ($rs_settings as $v) {
		$arr_settings[$v['k']] = $v['v'];
	} 
	return $arr_settings;
} 
// 上传电子档案
function upload_doc($fielName = '', $onlyName = 0)
{
	$allowType = array('.doc', '.xls');
	return upload($fileName, $onlyName, 'student/', $allowType);
} 
// 上传图片
function upload_pic($fileName = '', $onlyName = 0, $upPath = '')
{
	$allowType = array('.gif', '.jpg', '.png', '.bmp');
	return upload($fileName, $onlyName, $upPath , $allowType);
}  
// 上传pdf
function upload_pdf($fileName = '', $onlyName = 0 , $soucefile = '')
{
	$allowType = array('.pdf');
	return upload($fileName, $onlyName, 'pdf/', $allowType, $soucefile);
} 
// 上传文件
function upload($fileName = '', $onlyName = 0, $upPath = '', $allowType = array(), $soucefile = 'upload')
{
	include_once(INC_DIR . 'UpFile.class.php');
	$UpFile = new UpFile('upload/' . $upPath, 1024, $allowType);
	$UpFile -> upload($soucefile, $fileName);
	$upload_info = $UpFile -> getSaveInfo();

	if (!empty($upload_info['error'])) showmsg($upload_info['error'], -1);
	if ($onlyName) return $upload_info['saveName'];
	return $upload_info['savePath'];
} 

// 获得key=>value数组
function get_array($arr, $key, $val)
{
	$arr_type = array();
	foreach ($arr as $v) {
		$arr_type[$v[$key]] = $v[$val];
	} 
	return $arr_type;
} 
// 从表中获得 key=>value 数组
function get_array_from_table($tbname, $key, $val, $where = '1=1')
{
	global $db;
	$arr = $db -> row_select($tbname, $where, "$key,$val");
	$arr_type = array();
	foreach ($arr as $v) {
		$arr_type[$v[$key]] = $v[$val];
	} 
	return $arr_type;
} 

// 栏目数组
function get_channel()
{
	global $db;
	$channellist = $db -> row_select('channel', "1=1", 'c_id,c_name,c_url,c_target', 0, 'c_orderid asc');
	return $channellist;
} 

/**
*分类数组
* @param string $tablename 表名
*/
function get_category($tablename)
{
	global $db;
	$list = $db -> row_select($tablename, '1=1','*',0,'listorder');
	foreach($list as $key => $value) {
		$categorys[$value['catid']] = $value;
	} 
	return $categorys;
} 

/**
 * 分类选择
 * @param string $tablename 表名
 * @param intval $catid 别选中的ID，多选是可以是数组
 * @param string $str 属性
 * @param string $default_option 默认选项
 * @param intval $modelid 按所属模型筛选
 */
function select_category($tablename,$catid = 0, $str = '', $default_option = '', $sid, $modelid = 0)
{
	global $db,$tree;
	$list = $db -> row_select($tablename, '1=1');
	foreach($list as $key => $value) {
		if ($value['catid'] == $catid) $value['selected'] = 'selected';
		$categorys[$value['catid']] = $value;
	} 
	$string = '<select ' . $str . '>';
	if($default_option) $string .= "<option value=''>$default_option</option>";
	$str = "<option value='\$catid' \$selected>\$spacer \$catname</option>;";
	$tree -> init($categorys);
	$string .= $tree -> get_tree_category(0, $str, '', $sid);
	$string .= '</select>';
	return $string;
}
/**
 * 分类数组
 * 
 * @param string $tablename 表名
 */
function array_category($tablename)
{
	global $db;
	$data = $db -> row_select($tablename, "isshow=1", 'catid,catname', 0, 'listorder asc');
	return get_array($data, 'catid', 'catname');
} 

// 自定义参数数组处理
function arr_selfdefine($str)
{	
	$arrlist =array();
	if(!empty($str)){
		$arr = explode("|", $str);
		foreach ($arr as $v) {
			$arrlist[trim($v)] = trim($v);
		} 
	}
	return $arrlist;
} 

// 商家类型数组
function arr_dealer_category()
{
	global $db;
	$category = $db -> row_select('dealer_category', "1=1", 'id,catname', 0, 'orderid asc');
	return get_array($category, 'id', 'catname');
} 

// 车源品牌数组,搜索用
function arr_brand_with_search()
{
	global $db;
	$brand = $db -> row_select('brand', "b_parent=-1", 'b_id,b_name,mark', 0, 'mark asc');
	foreach ($brand as $k => $v) {
		$brand[$k]['b_name'] = $brand[$k]['mark'] . ' ' . $brand[$k]['b_name'];
		$brand[$k]['b_id'] = 'b_' . $brand[$k]['b_id'];
		unset($brand[$k]['mark']);
	}
	return get_array($brand, 'b_id', 'b_name');
	
} 

// 车源品牌数组,带字母索引
function arr_brand_with_index()
{
	global $db;
	$brand = $db -> row_select('brand', "b_parent=-1", 'b_id,b_name,mark', 0, 'mark asc');
	foreach ($brand as $k => $v) {
		$brand[$k]['b_name'] = $brand[$k]['mark'] . ' ' . $brand[$k]['b_name'];
		unset($brand[$k]['mark']);
	} 
	return get_array($brand, 'b_id', 'b_name');
} 

 //车源品牌数组
function arr_brand($parent)
{
	global $db;
	$brand = $db -> row_select('brand', "b_parent=".$parent, 'b_id,b_name,mark', 0, 'mark asc');
	return get_array($brand, 'b_id', 'b_name');
} 

//获取品牌名称
function arr_brandname($bid)
{
	global $db;
	$brand = $db -> row_select_one('brand', "b_id=".$bid, 'b_id,b_name,b_parent,classid');
	if($brand['classid']==5){
		$prebrand = $db -> row_select_one('brand', "b_id=".$brand['b_parent'], 'b_id,b_name');
		$brandname = $prebrand['b_name']." ".$brand['b_name'];
	}
	else{
		$brandname = $brand['b_name'];
	}
	return $brandname;
} 

// 推荐车源品牌数组
function arr_brand_recom()
{
	global $db;
	$brand = $db -> row_select('brand', "b_parent=-1 and b_type=1", 'b_id,b_name,mark', 0, 'mark asc');
	return get_array($brand, 'b_id', 'b_name');
} 
//子品牌选择
function select_subbrand($brandid = 0){
	global $db;
	$data = $db -> row_select_one('brand','b_id='.$brandid,'b_parent,b_name');
	$brandlist = "";
	if($data['b_parent']){
		$data2 = $db -> row_select_one('brand','b_id='.$data['b_parent'],'b_parent,b_name');
		if($data2['b_parent']){
			$list = $db->row_select('brand',"b_parent='".$data2['b_parent']."'");
			if($list){
				foreach($list as $key => $value){
					$brandlist .= "<optgroup label='".$value['b_name']."' style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
					$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
					foreach($sublist as $subkey => $subvalue){
						if ($subvalue['b_id'] == $brandid){
							$selected = 'selected';
						}
						else{
							$selected = '';
						}
						$brandlist .= "<option value=".$subvalue['b_id']." ".$selected.">".$subvalue['b_name']."</option>";
					}
				}
			}
		}
	}
	return $brandlist;
}

// 车型数组
function arr_model()
{
	global $db;
	$carsort = $db -> row_select('model', "1=1", 's_id,s_name', 0, 'orderid asc');
	return get_array($carsort, 's_id', 's_name');
} 

// 年份数组
function arr_year()
{
	global $db;
	$rs_year = settings();
	$year_arr = explode("\n", $rs_year['year']);
	foreach ($year_arr as $v) {
		$yearlist[trim($v)] = trim($v)."年";
	} 
	return $yearlist;
} 

// 颜色数组
function arr_color()
{
	global $db;
	$rs_color = settings();
	$color_arr = explode("\n", $rs_color['color']);
	foreach ($color_arr as $v) {
		$colorlist[trim($v)] = trim($v);
	} 
	return $colorlist;
} 

// 排量数组
function arr_gas()
{
	global $db;
	$rs_gas = settings();
	$gas_arr = explode("\n", $rs_gas['gas']);
	foreach ($gas_arr as $v) {
		$gaslist[trim($v)] = trim($v);
	} 
	return $gaslist;
} 

//变速箱数组
function arr_transmission()
{
	global $db;
	$rs_transmission = settings();
	$transmission_arr = explode("\n", $rs_transmission['transmission']);
	foreach ($transmission_arr as $v) {
		$transmissionlist[trim($v)] = trim($v);
	} 
	return $transmissionlist;
} 

// 关键词分类数组
function arr_keywordscategory()
{
	global $db;
	$list = $db -> row_select('keywords_category', "1=1", 'catid,catname', 0, 'listorder asc');
	return get_array($list, 'catid', 'catname');
} 
 
// 单页分类数组
function arr_pagesort()
{
	global $db;
	$pagesort = $db -> row_select('page_sorts', "1=1", 's_id,s_name', 0, 'orderid asc');
	return get_array($pagesort, 's_id', 's_name');
} 

// 单页数组
function arr_page()
{
	global $db;
	$pagelist = $db -> row_select('page', "1=1", 'p_id,p_title', 0, 'p_id asc');
	return get_array($pagelist, 'p_id', 'p_title');
} 

// 验证日期的合法性
function chk_date($value, $str = '-')
{
	$date = explode($str, $value);
	if (count($date) != 3 || !checkdate($date[1], $date[2], $date[0])) return false;
	return true;
} 

// 城市数组
function arr_city($aid=0) {
	global $db;
	if($aid!=0){
		$where = "parentid=".$aid;
	}
	else{
		$where = "parentid!=-1";
	}
	$data = $db -> row_select('area', $where, 'id,name', 0, 'orderid asc');
	return get_array($data, 'id', 'name');
} 

// 城市数组（推荐）
function arr_city_recom()
{
	global $db;
	$data = $db -> row_select('area', "parentid!=-1 and isrecom=1", 'id,name', 0, 'orderid asc');
	return get_array($data, 'id', 'name');
} 

/**
 * 车源列表
 */
function get_carlist($cid=0,$where,$num,$orderby)
{
	global $db;
	if(empty($where)){
		$where = "issell=1 and isshow=1";
	}
	if($cid!=0){
		$where .= " and cid=".$cid;
	}
	if(empty($num)){
		$num = 30;
	}
	if(empty($orderby)){
		$orderby = 'listime desc';
	}
	$list = $db -> row_select('cars', $where, '*', $num, $orderby);
	foreach($list as $key => $value) {
		//商家信息
		if(!empty($value['uid'])){ 
			$user = $db -> row_select_one('member', 'id=' . $value['uid'],'isdealer,ischeck');
			if($user['isdealer']==2 and $user['ischeck']==1){
				$list[$key]['isshop'] = 1;
			}
			else{
				$list[$key]['isshop'] = 0;
			}
		}
		if(!empty($value['p_mainpic'])){
			$pic = explode(".", $value['p_mainpic']);
			$list[$key]['p_mainpic'] = WEB_DOMAIN.$pic[0] . "_small" . "." . $pic[1];
		}
		$list[$key]['p_shortname'] = _substr($value['p_allname'], 0, 24);
		$list[$key]['listtime'] = date('Y-m-d', $value['listtime']);
		$list[$key]['p_details'] = _substr($value['p_details'], 0, 80);
		$list[$key]['p_price'] = intval($value['p_price']) == 0 ? "面谈" : "￥" . $value['p_price']."万";
		$list[$key]['p_url'] = WEB_PATH.HTML_DIR."/buycars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
	} 
	return $list;
}

/*今日推荐*/
function get_todaycar($cid=0){
	$where = "issprecom=1 and issell=0 and isshow=1";
	$list = get_carlist($cid,$where,'3','listtime desc');
	return $list;
}

/**
 * 推荐商家
 */
function get_comshop($cid=0)
{
	global $db;
	$where = "isdealer=2 and ischeck=1 and isrecom=1";
	if($cid!=0){
		$where .= " and cid=".$cid;
	}
	$list = $db -> row_select('member', $where, 'id,logo,company,mobilephone,shoptype', '10', 'id desc');
	foreach($list as $key => $value){
		$list[$key]['carcount'] = $db -> row_count('cars','uid='.$value['id']);
		$list[$key]['company_short'] = _substr($value['company'],0,26);
	}
	return $list;
} 

/**
 * 热门商家
 */
function get_hotshop($cid=0)
{
	global $db;
	$where = "isdealer=2 and ischeck=1 and ishot=1";
	if($cid!=0){
		$where .= " and cid=".$cid;
	}
	$list = $db -> row_select('member', $where, 'id,logo,company,mobilephone,shoptype', '6', 'id desc');
	foreach($list as $key => $value){
		$list[$key]['carcount'] = $db -> row_count('cars','uid='.$value['id']);
		$list[$key]['company_short'] = _substr($value['company'],0,26);
	}
	return $list;
} 


/**
 * 推荐新闻
 */
function get_comnews($sid, $num)
{
	global $db;
	$list = $db -> row_select('news', "catid=" . $sid, '*', $num, 'n_addtime desc');
	foreach($list as $key => $value) {
		$list[$key]['shorttitle'] = _substr($value['n_title'], 0, 60);
		$list[$key]['shorttitle2'] = _substr($value['n_title'], 0, 46);
		$list[$key]['n_addtime'] = date('Y-m-d', $value['n_addtime']);
		$list[$key]['n_url'] = WEB_PATH."/news/". date('Ym', $value['n_addtime']) . "/".$value['n_id'].".html";
	} 
	return $list;
} 

/**
 * 单页
 */
function get_page($id = 0)
{
	global $db;
	$data = $db -> row_select_one('page', "p_id=" . $id);
	$data['p_info'] = htmlspecialchars_decode($data['p_info']);
	$data['detail'] = _substr(strip_tags($data['p_info']), 0, 200);
	return $data;
} 

/**
 * 幻灯片
 */
function get_filmstrip($typeid)
{
	global $db;
	$filmlist = $db -> row_select('filmstrip', 'typeid='.$typeid, 'id,pic,url', 0, 'orderid asc');
	return $filmlist;
} 


// 广告
function arr_ad()
{
	global $db;
	$ads = $db -> row_select('ad', '', '*', 0, 'id');
	foreach($ads as $k => $v) {
		$adlist[$v['id']] = $v;
	} 
	return $adlist;
} 
// 友情链接
function get_flink()
{
	global $db;
	$flinklist = $db -> row_select('friendlink', 'isshow=1', 'l_url,l_name', 0, 'l_orderid asc');
	return $flinklist;
} 



/*底部热门车系*/
function get_bottomkeywords()
{
	global $db;
	$keywordlist = array();
	$list = $db -> row_select('keywords', "catid=2",'*', '0','mark asc');
	foreach($list as $key => $value){
		$keywordlist[strtoupper($value['mark'])][$key]['keywords'] = $value['keywords'];
		$keywordlist[strtoupper($value['mark'])][$key]['keyword'] = urlencode($value['keywords']);
	}
	return $keywordlist;
} 


/**
 * 热门关键词
 */
function get_keywords()
{
	global $db;
	$list = $db -> row_select('keywords', "catid=1");
	foreach($list as $key => $value){
		$list[$key]['keyword'] = urlencode($value['keywords']);
	}
	return $list;
} 
// 省份数组
function arr_province() {
	global $db;
	$data = $db -> row_select('area', "parentid=-1", 'id,name', 0, 'orderid asc');
	return get_array($data, 'id', 'name');
} 

// 缓存函数
function display_common_cache() {
	$commoncache = array();
	$commoncache['news_category'] = get_category('news_category');
	//系统配置
	$commoncache['settings'] = 	settings();
	//带ABC品牌数组
	$commoncache['markbrandlist'] = arr_brand_with_index();
	//不带ABC品牌数组
	$commoncache['brandlist'] = arr_brand(-1);
	//车型数组
	$commoncache['modellist'] = arr_model();
	//年份
	$commoncache['yearlist'] = arr_year();
	//颜色
	$commoncache['colorlist'] = arr_color();
	//排量
	$commoncache['gaslist'] = arr_gas();
	//变速箱
	$commoncache['transmissionlist'] = arr_transmission();
	//省份数组
	$commoncache['provincelist'] = arr_province();
	//城市数组
	$commoncache['citylist'] = arr_city();
	//二手车行情
	$commoncache['hotnewslist'] = get_comnews(7, 6);
	/*头部搜索关键词*/
	$commoncache['topkeywords'] = get_keywords();
	return $commoncache;
} 
?>