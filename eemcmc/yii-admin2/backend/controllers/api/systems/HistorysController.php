<?php

namespace backend\controllers\api\systems;

use backend\models\AdminHistory;

class HistorysController extends \backend\controllers\BaseController
{

	/**
	 * 分页获取数据 
	 * @return array
	 */
	public function actionIndex($page = 1, $page_size = 20)
	{
		$get = $this->request->get();
		//创建对象
		$history = AdminHistory::find()->page($page, $page_size)->orderBy('id desc');

		//根据订单编号搜索
		if (!empty($get['user_id']))
		{
			$history->andWhere(['user_id' => $get['user_id']]);
		}
		if (!empty($get['url']))
		{
			$history->andWhere(['url' => $get['url']]);
		}

		//排序
		if (!empty($get['sort_field']) && !empty($get['sort_type']))
		{
			$history->orderBy("{$get['sort_field']} {$get['sort_type']}");
		}
		$historys = $history->all();
		$data = [
			'items' => $historys,
			'total' => $history->count()
		];
		return $this->success($data);
	}

}
