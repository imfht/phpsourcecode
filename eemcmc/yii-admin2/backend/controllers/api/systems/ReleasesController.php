<?php

namespace backend\controllers\api\systems;

use common\models\Release;

class ReleasesController extends \backend\controllers\BaseController
{

	/**
	 * 版本列表
	 */
	function actionIndex($page = 1, $page_size = 20)
	{
		$get = $this->request->get();
		//创建对象
		$release = Release::find()->page($page, $page_size);

		//排序
		if (!empty($get['sort_field']) && !empty($get['sort_type']))
		{
			$release->orderBy("{$get['sort_field']} {$get['sort_type']}");
		}

		$releases = $release->all();
		$data = [
			'items' => $releases,
			'total' => $release->count()
		];
		return $this->success($data);
	}

	/**
	 * 
	 * @return type
	 */
	function actionUpdate()
	{
		$release = Release::findOne($this->post('id'));
		$release->attributes = $this->post();
		$release->save();
		if ($release->hasErrors())
		{
			return $this->failure($release->getError());
		}
		return $this->success();
	}

	/**
	 * 创建版本
	 */
	function actionCreate()
	{
		$release = Release::create($this->post());
		if ($release->hasErrors())
		{
			return $this->failure($release->getError());
		}
		return $this->success();
	}

	/**
	 * 删除版本
	 */
	function actionDelete()
	{
		$id = $this->post('id');
		$release = Release::findOne($id);
		if (empty($release))
		{
			return $this->failure('要删除的版本不存在');
		}
		$release->delete();
		return $this->success();
	}

}
