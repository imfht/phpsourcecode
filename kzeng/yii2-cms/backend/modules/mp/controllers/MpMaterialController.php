<?php
namespace backend\modules\mp\controllers;

use backend\modules\mp\models\MpMaterial;
use yii\filters\VerbFilter;


class MpMaterialController extends \yeesoft\controllers\admin\BaseController {
	public $modelClass = 'backend\modules\mp\models\MpMaterial';
    public $modelSearchClass = 'backend\modules\mp\models\search\MpMaterialSearch';
    public $pageSize = 20;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }
    public function actionSynimages() {
    	MpMaterial::deleteAll(); // 先清空素材表

    	$mp = \Yii::$app->wx->getApplication();

    	$stats = $mp->material->stats(); // 获取素材统计信息
    	
    	$offset = 0;

    	while ( $offset < $stats['image_count'] ) {
			$lists = $mp->material->lists('image', $offset, $this->pageSize);
			$items = $lists['item'];

			for ( $i=0; $i<count($items); $i++ ) {
				$model = new MpMaterial;

				$model->media_id = $items[$i]['media_id'];
				$model->name = $items[$i]['name'];
				$model->update_time = $items[$i]['update_time'];
				$model->url = $items[$i]['url'];
				$model->type = 1; //1: image 2: news
				$model->content = '';
				$model->save(false);
			}
			$offset += $this->pageSize;
    	};

		return $this->redirect('index');
    }
    public function actionDelete($media_id) {
    	$mp = \Yii::$app->wx->getApplication();
		$mp->material->delete($media_id);

		//delete from bookgoal server 
		MpMaterial::find()->where(['media_id' => $media_id])->delete();

		return $this->redirect('index');
    }
}