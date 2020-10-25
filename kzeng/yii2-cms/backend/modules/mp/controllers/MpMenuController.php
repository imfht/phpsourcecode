<?php
namespace backend\modules\mp\controllers;

class MpMenuController extends \yeesoft\controllers\admin\BaseController {
	public $modelClass = 'backend\modules\mp\models\MpMenu';
	public $modelSearchClass = 'backend\modules\mp\models\search\MpMenuSearch';

    public function menu($data, $parent_id=0) {
    	$return_data = [];

    	$model = new $this->modelClass;
        if ( !$parent_id ) $model::deleteAll();

        foreach ( $data as $key => $value ) {
        	$value['parent_id'] = $parent_id;
        	$value['order'] = $key;

        	if ( !isset($value['type']) ) $value['type'] = 'sub_button';

			switch ( $value['type'] ) {
				case 'view': // url
					$value['key'] = $value['url'];
					unset($value['url']);
				break;
				default:
			}

			$model->isNewRecord = true;
			$model->setAttributes($value);
			$model->save(false);

			if ( $value['sub_button'] ) $this->menu($value['sub_button'], $model->id);
			$model->id = 0;
		}

		return $return_data;
    }
    public function buildMenu($data, $parent_id=0) {
    	$return_data = [];

    	foreach ( $data as $key => $value ) {
			if ( $value['parent_id'] == $parent_id ) {
				$temp = $this->buildMenu($data, $value['id']);
				if ( $temp ) {
					$value['sub_button'] = $temp;
					unset($value['type']);
				} else if ( $value['type'] == 'view' ) {
					$value['url'] = $value['key'];
					unset($value['key']);
				}
				unset($value['id']);
				unset($value['parent_id']);
				
				$return_data[] = $value;
			}
		}

		return $return_data;
    }

	protected function getRedirectPage($action, $model=null) {
		switch ( $action ) {
			default:
				return ['index'];
		}
	}

	public function actionPush() { // 推送
		$data = (new \yii\db\Query())
		    ->select(['type', 'name', 'key', 'id', 'parent_id'])
		    ->from('mp_menu')
		    ->where([
		    	'status' => 1,
		    ])
		    ->orderBy('order asc')
		    ->all();
		$buttons = $this->buildMenu($data);
		
		// $buttons = [
			// [
			// 	"type" => "click",
			// 	"name" => "今日歌曲",
			// 	"key" => "V1001_TODAY_MUSIC"
			// ],
			// [
			// 	"name" => "菜单",
			// 	"sub_button" => [
			// 		[
			// 			"type" => "view",
			// 			"name" => "搜索",
			// 			"url" => "http://www.soso.com/"
			// 		],
			// 		[
			// 			"type" => "view",
			// 			"name" => "视频",
			// 			"url" => "http://v.qq.com/"
			// 		],
			// 		[
			// 			"type" => "click",
			// 			"name" => "赞一下我们",
			// 			"key" => "V1001_GOOD"
			// 		],
			// 	],
			// ],
		// ];
		// $buttons = [ // 小程序
		    // [
		    //     "type" => "miniprogram",
		    //     "name" => "一键呼叫",
		    //     "appid" => "wxxxxxxxxxxxxxx",
		    //     "pagepath" => "pages/call/phone",
		    //     "url" => "http://xxx.xxx/xxx",
		    // ],
		// ];
		// $buttons = [
			// [
			// 	"name" => "扫码", 
			// 	"sub_button" => [
			// 		[
			// 			"type" => "scancode_waitmsg", 
			// 			"name" => "扫码带提示", 
			// 			"key" => "rselfmenu_0_0", 
			// 			// "sub_button" => [ ],
			// 		], 
			// 		[
			// 			"type" => "scancode_push", 
			// 			"name" => "扫码推事件", 
			// 			"key" => "rselfmenu_0_1", 
			// 			// "sub_button" => [ ],
			// 		]
			// 	]
			// ], 
			// [
			// 	"name" => "发图", 
			// 	"sub_button" => [
			// 		[
			// 			"type" => "pic_sysphoto", 
			// 			"name" => "系统拍照发图", 
			// 			"key" => "rselfmenu_1_0", 
			// 		    // "sub_button" => [ ],
			// 		],
			// 		[
			// 			"type" => "pic_photo_or_album", 
			// 			"name" => "拍照或者相册发图", 
			// 			"key" => "rselfmenu_1_1", 
			// 			// "sub_button" => [ ],
			// 		], 
			// 		[
			// 			"type" => "pic_weixin", 
			// 			"name" => "微信相册发图", 
			// 			"key" => "rselfmenu_1_2", 
			// 			// "sub_button" => [ ],
			// 		]
			// 	]
			// ], 
			// [
			// 	"name" => "发送位置", 
			// 	"type" => "location_select", 
			// 	"key" => "rselfmenu_2_0"
			// ],
			// [
			//     "type" => "media_id", 
			//     "name" => "图片", 
			//     "media_id" => "MEDIA_ID1"
			// ], 
			// [
			//     "type" => "view_limited", 
			//     "name" => "图文消息", 
			//     "media_id" => "MEDIA_ID2"
			// ],
		// ];
		// $matchRule = [ // 个性化菜单
		//     "tag_id" => "2",
		//     "sex" => "1",
		//     "country" => "中国",
		//     "province" => "广东",
		//     "city" => "广州",
		//     "client_platform_type" => "2",
		//     "language" => "zh_CN",
		// ];

		$mp = \Yii::$app->wx->getApplication();
		$mp->menu->add($buttons);
		// $mp->menu->add($buttons, $matchRule);
		return $this->redirect('index');
	}
	public function actionPull() { // 拉取 ok
		$mp = \Yii::$app->wx->getApplication();
		$response_data = $mp->menu->all(); // 查询菜单

		self::menu($response_data['menu']['button']); // save to database
		return $this->redirect('index');
	}
	public function actionCurrent() {
		$mp = \Yii::$app->wx->getApplication();
		$response_data = $mp->menu->current(); // 获取自定义菜单

		error_log('----bee menu----', 3, "bee.txt");
		error_log(var_export($response_data, 1), 3, "bee.txt");
	}
	public function actionTest($userId) {
		$mp = \Yii::$app->wx->getApplication();
		$response_data = $mp->menu->test($userId); // 测试个性化菜单

		error_log('----bee menu----', 3, "bee.txt");
		error_log(var_export($response_data, 1), 3, "bee.txt");
	}
	public function actionDestroy() {
		$mp = \Yii::$app->wx->getApplication();

		$mp->menu->destroy(); // 全部
		$mp->menu->destroy($menuId); // 删除个性化菜单
	}
}