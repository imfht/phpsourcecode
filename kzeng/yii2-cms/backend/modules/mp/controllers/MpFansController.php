<?php
namespace backend\modules\mp\controllers;

use backend\modules\mp\models\MpFans;

class MpFansController extends \yeesoft\controllers\admin\BaseController {
    public $modelClass = 'backend\modules\mp\models\MpFans';
    public $modelSearchClass = 'backend\modules\mp\models\search\MpFansSearch';
    private $listSize = 10000;
    private $infoSize = 100;

    public function actionSyn() {
        $mp = \Yii::$app->wx->getApplication();
    	$userService = $mp->user;

    	//先清空粉丝表
    	MpFans::deleteAll();

		do {
			$users = $userService->lists();
			$lists = $users->data['openid'];

			$lists_array = array_chunk($lists, $this->infoSize);

			foreach ( $lists_array as $lists ) {
				$infos = $userService->batchGet($lists);
				$info_array = $infos->user_info_list;

			

				foreach ( $info_array as $info ) {
					unset($info['unionid']);
					unset($info['tagid_list']);
					//error_log(print_r($info, 1), 3, "bee.txt");
				    // $model->isNewRecord = true;
				    // $model->setAttributes($info);
				    // $model->save(false) && $model->id = 0;

					$model = new $this->modelClass;
					$model->setAttributes($info);
					$model->save(false);
				}
			}
		} while ( count($lists) >= $this->listSize );

	 	return $this->redirect('index');
    }
}