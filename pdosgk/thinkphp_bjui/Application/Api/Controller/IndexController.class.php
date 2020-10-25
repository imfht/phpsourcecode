<?php
namespace Api\Controller;
use Think\Controller;

class IndexController extends Controller {
	public function _initialize() {
        header('Content-type: application/json');
  		$this->save();
  		$this->categorys = F('category_content');
        $this->db = D('Content');
        $this->db->set_model(3);
	}
	public function index() {
		echo 12;

	}

    public function get_session_userinfo(){
        $loginToken = I('get.loginToken');
        $map['openid'] = $loginToken;
        $detail = M('User')->where($map)->find();
        $return['errcode'] = 0;
        $return['errMsg'] = '登录成功';
        $return['relateObj'] = array(
            'platformUser'  => [
                'loginToken' => $loginToken,
            ],
            // 'id' => 1,
            // 'nickName' => 'demo',
        );
        $this->ajaxReturn($return);
    }

	public function wx_mini_code_login() {

		//开发者使用登陆凭证 code 获取 session_key 和 openid
		$APPID = 'wx0b1a73f4d330c505';
		$AppSecret = '1dcc8a7b363e4a0e215d81d6fe037ab7';
		$code = I('post.code');
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
		$arr = $this->_curl($url); // 一个使用curl实现的get方法请求
        // $this->ajaxReturn($arr);
		$openid = $arr['openid'];
		$session_key = $arr['session_key'];
		if($openid){
			$info['openid'] = $openid;
            //查看是否存在
            $detail = M('User')->where($info)->find();
            if(empty($detail)){
                M('User')->add($info);
            }
			

			$return['errcode'] = 0;
			$return['errMsg'] = '登录成功1111';
			$return['relateObj'] = array(
                'platformUser'  => [
                    'loginToken' => $openid,
                ],
				// 'id' => 1,
				// 'nickName' => 'demo',
			);
		}
		$this->ajaxReturn($return);

	}

    public function change_user_info(){
        //第一次登录， 用户设置头像昵称
        //根据loginToken, 来给用户赋值。
        $info['headimg'] = I('post.headimg');
        $info['nickname'] = I('post.nickname');
        $info['sex'] = I('post.sex');
        $map['openid'] = I('post.loginToken');

        M('User')->where($map)->save($info);
        $return['errcode'] = "0";
        $this->ajaxReturn($return);
    }

	public function get_platform_setting(){
        //返回平台设置
		$return['platformSetting'] = [
			'id' => 1,
			'siteTitle' => '泉州艺术考级',
			'topColor' => '#ffffff',
			'topBgColor' => '#FE7AAC',
			'platformName' => '鲜花',
			'defaultColor' => '#FE7AAC',
			'indexPage' => 'custom_page_index.html',
            'categories' => $this->getCategory(),
            'categoryList' => $this->getCategoryList(),
            'platformWuliuComs' => [],

		];
        $return['menus'] = null;
		$this->ajaxReturn($return);
	}

    public function getCategory(){
        $categorys = [];
        if($this->categorys){
            foreach ($this->categorys as $key => $value) {
                $cat['id'] = $value['catid'];
                $cat['name'] = $value['catname'];
                $cat['children'] = [];
                $categorys[] = $cat;
            }
        }
        return $categorys;
    }

    public function getCategoryList(){
        $categorys = [];
        if($this->categorys){
            foreach ($this->categorys as $key => $value) {
                $cat['key'] = $value['catid'];
                $cat['value'] = $value['catname'];
                $categorys[] = $cat;
            }
        }
        return $categorys;
    }

    /**
     * {beanRemark:'页面装饰项',belongPageId:'归属页面ID(参考 ProductChannelPage)',innerPageIndex:'内页索引(Tab页面的位置索引)partialType:'装饰类型 1 富文本 2 辅助线 3 空白辅助 4 标题 5 文本导航链接 6图片导航链接 7、图片广告轮播 8、grid 导航 9、详细列表 10、grid列表 11、底部TAB 12、头部TITLE 13、TabPage',orderNo:'页内排序',jsonData:'装饰json数据',remark:'备注',wapTemplate:'wap模板',iosTemplate:'APP模板',platformNo:'平台号',androidTemplate:'小程序模板'}",
     * @param  [type] $partialType [description]
     * @param  [type] $data        [description]
     * @return [type]              [description]
     */
    public function getPartials($partialType, $data = []){
        $return['partialType'] = $partialType;
        $return['jsonData'] = json_encode($data);
        return $return;
    }

    public function getGird(){
        $gird['column'] = 4;
        $gird['row'] = 2;
        $gird['iconType'] = 2;
        $gird['showType'] = 0;
        $gird['padding'] = 15;
        $cells = [];
        $cells[] = [
            'text' => '全部',
            'loginCheck' => 'noCheck',
            'linkUrl' => 'search_product.html?showType=1',
            'iconPath' => 'http://image1.sansancloud.com/shuiguo/2017_12/07/09/29/33_528.jpg',
        ];
        if($this->categorys){
            foreach ($this->categorys as $key => $value) {
                $cat['text'] = $value['catname'];
                $cat['loginCheck'] = 'noCheck';
                $cat['fontSize'] = 12;
                $cat['align'] = 'left';
                $cat['targetType'] = 'section';
                $cat['bgColor'] = '#FFFFFF';
                $cat['iconPath'] = 'http://image1.sansancloud.com/shuiguo/2017_12/07/09/28/12_092.jpg';

                $cat['linkUrl'] = 'search_product.html?showType=1&productTypeId='.$value['catid'];
                $cells[] = $cat;
            }
        }
        $gird['cells'] = $cells;
        return $gird;
    }

	public function custom_page_index(){
        $banner_list = ['images' => [
            ['imageUrl' => 'http://image1.sansancloud.com/shuiguo/2017_12/07/11/21/54_447.jpg'],
            ['imageUrl' => 'http://image1.sansancloud.com/shuiguo/2017_12/07/11/21/54_447.jpg'],
            ['imageUrl' => 'http://image1.sansancloud.com/shuiguo/2017_12/07/11/21/54_447.jpg'],
        ]];
        $gird = $this->getGird();
        //banner
        $partials[] = $this->getPartials(7, $banner_list);
        $partials[] = $this->getPartials(3, ['color' => '#f9f9f9', 'height' => 10]);
        $partials[] = $this->getPartials(8, $gird);
        $partials[] = $this->getPartials(3, ['color' => '#f9f9f9', 'height' => 10]);
        $partials[] = $this->getPartials(5, $gird);

        $return['partials'] = $partials;
        $this->ajaxReturn($return);
	}

	public function custom_page_userinfo(){
	}

    public function get_product_comment_list(){

    }

  public function more_product_list(){
  	if(I('get.categoryId')){
        $map['catid'] = I('get.categoryId');
    }
  	$curPage = 1;
    $pageSize = 16;
  	$page_list = $this->db->where($map)->field('id,price,title name,thumb')->page($page, $pageSize)->select();
  	foreach ($page_list as $key => $value) {
  		$page_list[$key]['imagePath'] = thumb($value['thumb'], 180, 120);
  	}
  	$return['pageSize'] = $pageSize;
  	$return['curPage'] = $curPage;
  	$return['totalSize'] = 4;
  	$return['result'] = $page_list;
  	$this->ajaxReturn($return);
  }

  	public function product_detail(){
  		$id = I('get.productId');
  		$detail = $this->db->getDetail($id);
  		$productInfo['imagePath'] = thumb($detail['thumb'], 180, 120);
        //测试用
        $detail['belongShopBean'] = [
            'id' => 1,
        ];
        $detail['productId'] = $detail['id'];
  		$return['productInfo'] = $detail;
        $return['images'][] = [
            'imagePath' => $detail['thumb']
        ];
        $return['measures'] = [];
        $return['description']['description'] = html_entity_decode($detail['content']);
  		$this->ajaxReturn($return);
  	}

    public function getUserIdByLoginToken($loginToken){
        $map['openid'] = $loginToken;
        $userid = M('User')->where($map)->getField('userid');
        return $userid;
    }

  	public function change_shopping_car_item(){
        $userid = $this->getUserIdByLoginToken(I('post.loginToken'));
        $productId = I('post.productId');
        $type = I('post.type');
        $detail = $this->db->getDetail($productId);

        //查看是否已经添加。 已经添加的， 就数量加一
        $map['userid'] = $userid;
        $map['productId'] = $productId;
        $exist_cart = M('UserCart')->where($map)->find();

        if($exist_cart){
            $exist_cart['belongShop'] = 1;
            if($type == 'add'){
                $count = $exist_cart['count'] + 1;
                $update_info['count'] = ['exp', 'count+1'];
                M('UserCart')->where($map)->save($update_info);
            }elseif($type == 'dec'){
                $count = max($exist_cart['count'] - 1, 0);
                $update_info['count'] = $count;
                M('UserCart')->where($map)->save($update_info);
            }elseif ($type == 'change') {
                M('UserCart')->where($map)->delete();
            }
        }else{
            $count = 1;
            $info['userid'] = $userid;
            $info['productId'] = $productId;
            $info['name'] = $detail['title'];
            $info['tagPrice'] = $detail['price'];
            $info['price'] = $detail['price'];
            $info['imagePath'] = $detail['thumb'];
            $info['count'] = $count;
            $info['create_time'] = NOW_TIME;
            M('UserCart')->add($info);
        }
        $item = M('UserCart')->where($map)->find();

        $totalCarItemCount = M('UserCart')->where(['userid' => $userid])->count();
        $totalCarItemPrice = M('UserCart')->where(['userid' => $userid])->count('price');

        $return['totalCarItemCount'] = $totalCarItemCount ? $totalCarItemCount : 0;
        $return['totalCarItemPrice'] = $totalCarItemPrice ? $totalCarItemPrice : 0;

        $return['count'] = $count;
        $return['productId'] = $productId;
        $return['belongShop'] = 1;
        $return['carItemPrice'] = $item['price'] * $count;

        $return['item'] = $item;
        $return['id'] = $item['id'];
        $this->ajaxReturn($return);

        //添加到用户购物车
  		echo '{
        addDate:"2018-01-16 10:03:57"
        belongShop:257
        belongUserId:"60176"
        carItemPrice:278
        carItemYunfei:0
        count:2
        id:33777
        measureCartesianId:0
        platformNo:"xianhua"
        productId:"8221"
        stock:10
        totalCarItemCount:16
        totalCarItemPrice:2708
        zhekou:100
    }';
  	}

    public function delete_shopping_car_list_item(){
        $userid = $this->getUserIdByLoginToken(I('post.loginToken'));
        $cartids = I('post.selectedIds');
        $type = I('post.type');

        $map['userid'] = $userid;
        if($type == 'shopall'){
            
        }else{
            $map['id'] = ['in', $cartids];
        }

        $result = M('UserCart')->where($map)->delete();
        if($result){
            $return['errcode'] = 0;
            $return['errMsg'] = 'success';
            $return['relateObj'] = 1;
            return $this->ajaxReturn($return);
        }
    }

  	public function get_shopping_car_list_item(){
        $return = [];
        $userid = $this->getUserIdByLoginToken(I('get.loginToken'));
        $map['userid'] = $userid;
        $cart_list = M('UserCart')->where($map)->select();
        if($cart_list){
            foreach ($cart_list as $key => $value) {
                $value['belongShop'] = 1;
                $value['imagePath'] = thumb($value['imagepath'], 180, 120);
                $cart_list[$key]['item'] = $value;
                $cart_list[$key]['productId'] = $value['productid'];
                $cart_list[$key]['carItemPrice'] = $value['price'];
            }

            $cart_detail['id'] = 1;
            $cart_detail['carItems'] = $cart_list;
            $cart_detail['serviceStartTime'] = 0;
            $return['result'][] = $cart_detail;
        }
        //获取用户购物车内容
        
        $this->ajaxReturn($return);
  		echo '{
      "result": [
        {
          "id": 236,
          "shopName": "一方建盏",
          "shopDescription": "",
          "shopLogo": "",
          "belongAreaId": 0,
          "belongUserId": 0,
          "belongShangquanId": 0,
          "shopLevelId": 0,
          "hotShop": 0,
          "shopLevelValue": 0,
          "shopTip": "",
          "checkTime": "2017-09-23 14:56:49",
          "checkUserId": 0,
          "checkState": 1,
          "setEnable": 1,
          "platformNo": "jianzhan",
          "telno": "13665075175",
          "range": 0,
          "ownerName": "",
          "favoriteCount": 0,
          "ownerQq": "",
          "ownerEmail": "",
          "deleteFlag": 0,
          "turnover": 10113.07,
          "shopContent": "",
          "adverts": [

          ],
          "productCount": 0,
          "platformShop": 0,
          "managerPlatformUserId": 49631,
          "shopScore": 10,
          "scoreNum": 4,
          "averageScore": 2,
          "averageScoreHundred": 100,
          "shopOrder": 0,
          "shopIndexPage": "",
          "orderItems": [

          ],
          "carItems": [
            {
              "id": 29611,
              "belongUserId": "47446",
              "productId": "8036",
              "count": 1,
              "addDate": "2017-12-14 10:36:56",
              "zhekou": 100,
              "item": {
                "id": 8036,
                "imagePath": "http://image1.sansancloud.com/jianzhan/upload/diyii/B38240639193915528/1.jpg",
                "name": "张中钦一叶菩提",
                "tagPrice": 5200,
                "price": 5200,
                "price2": 0,
                "saleCount": 0,
                "category": 1007,
                "hotSale": 0,
                "disable": 0,
                "description": "毫纹条达。盏心一抹蓝色 外围毫纹满满自然挂釉一滴",
                "orderNumber": "20171102123943",
                "readCount": 0,
                "stock": 1,
                "price3": 0,
                "productCode": "B38240639193915528",
                "belongAreaId": 0,
                "belongShangquanId": 0,
                "belongShopId": 236,
                "belongAreaName": "",
                "belongShangquanName": "",
                "belongShopName": "一方建盏",
                "tags": "",
                "promotion": "0",
                "platformNo": "jianzhan",
                "addTime": "2017-11-02 12:39:43",
                "minSaleCount": 1,
                "bigSmallUnitScale": 1,
                "inCarCount": 0,
                "hgZhekou": 100,
                "lgZhekou": 100,
                "hzZhekou": 100,
                "lzZhekou": 100,
                "fxyZhekou": 100,
                "bdZhekou": 100,
                "categoryParent": 0,
                "categoryGradparent": 0,
                "newSale": 0,
                "brandId": 40,
                "brandName": "张中钦",
                "commentCount": 0,
                "yunfei": 0,
                "yunfeiTemplate": 0,
                "productType": 0,
                "presalePrice": 0,
                "distributeProfit": 0,
                "daidingPlatformUserId": 0,
                "pingfen": 0,
                "pingfenCount": 0,
                "leaseUnitTypeStr": "小时",
                "attributesCombind": "[口径-9.1x5.5cm][器型-敛口][斑纹-其他]",
                "leaseUnitType": 0,
                "leaseUnitAmount": 0,
                "leaseUnitExpireAmount": 0,
                "leaseNeedBackUnitCount": 1,
                "measureItem": 0,
                "saleStrategyDetails": [

                ]
              },
              "belongShop": 236,
              "measureCartesianId": 0,
              "platformNo": "jianzhan",
              "carItemPrice": 5200,
              "carItemYunfei": 0,
              "totalCarItemCount": 0,
              "totalCarItemPrice": 0,
              "stock": 1
            }
          ],
          "shopFavorite": 0,
          "backOrderCount": 2,
          "maxOrderPerDay": 0,
          "todayOrderCount": 0,
          "serviceOrderCount": 7,
          "printerType": 1,
          "printerPartner": "",
          "printerMachineCode": "",
          "printerApiKey": "",
          "printerMachineKey": "",
          "serviceStartTime": 0,
          "serviceEndTime": 24,
          "account": {
            "id": 38,
            "shopId": 236,
            "shopName": "一方建盏",
            "platformNo": "jianzhan",
            "account": 1541.5
          }
        }
      ],
      "pageSize": 10000,
      "curPage": 1,
      "pageCount": 1
    }';
  	}

  	public function shopping_car_list_item_create_order(){
  		echo '{
        "addTime":"2018-01-16 14:29:07",
        "adminChangeAmount":0,
        "availableCoupons":[],
        "backAmount":0,
        "belongShop":257,
        "belongShopName":"福州鲜花批发",
        "belongStorageId":0,
        "buyerArea":"",
        "buyerBestTime":"",
        "buyerCity":"",
        "buyerId":60176,
        "buyerLoginName":"112728",
        "buyerName":"蒋",
        "buyerProvince":"",
        "buyerTelno":"",
        "chatOrder":0,
        "comment":0,
        "commentId":0,
        "easyStatus":0,
        "easyStatusStr":"",
        "expressNo":"-1",
        "fromSource":"",
        "fxProfit":0,
        "gainJifen":0,
        "goodsAmount":2708,
        "goodsOnlyAmount":0,
        "id":84313,
        "innerOrder":0,
        "invNeed":0,
        "invType":0,
        "isComment":0,
        "jifenDikou":0,
        "leaseRecordId":0,
        "orderNo":"20180116142906000001",
        "orderProcessList":[],
        "orderShops":[],
        "orderStatus":0,
        "orderType":0,
        "payAmount":0,
        "payStatus":0,
        "payType":0,
        "payTypeStr":"货到付款",
        "platformNo":"xianhua",
        "prepayAmount":0,
        "pressCount":0,
        "promotionId":"0",
        "promotionName":"",
        "pushToErp":0,
        "reversePressCount":0,
        "shippingStatus":0,
        "tempShopOrderItems":{},
        "thirdOrderNo":"",
        "unshowStatus":0,
        "useCouponId":0,
        "useCouponTypeId":0,
        "useCouponTypeName":"",
        "userTagPriceOrder":0,
        "wuliuImportNo":"",
        "wuliuPackageNo":"",
        "youhuiAmount":0,
        "yunfeiAmount":0
    }';
  	}
	//保存post信息
	public function save($data = null) {
		if (!$data) {
			if (IS_POST) {
				$data = file_get_contents("php://input"); //接收post数据
			} else {
				$data = $_GET;
			}
		}
		//$file_in = file_get_contents("php://input"); //接收post数据
		$info['refer'] = $_SERVER['REQUEST_URI'];
		$info['data'] = serialize($data);
		$info['create_time'] = NOW_TIME;
		$info['ip'] = get_client_ip(0, true);
		$result = M('post_log')->add($info);

	}

	public function _curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$txt = curl_exec($ch);
		if (curl_errno($ch)) {
			return false;
		}
		curl_close($ch);
		return json_decode($txt, true);
	}
	public function vget($url){
	    $info=curl_init();
	    curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($info,CURLOPT_HEADER,0);
	    curl_setopt($info,CURLOPT_NOBODY,0);
	    curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($info,CURLOPT_URL,$url);
	    $output= curl_exec($info);
	    curl_close($info);
	    return $output;
	}

}