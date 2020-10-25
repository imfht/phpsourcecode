<?php
/**
 * 网站前端首页
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 *
 */

class SiteController extends MyPageController
{
	// 首页
	// 跳转到实际管理后台
	public function actionIndex(){
		Yii::app()->request->redirect('/admin/site');
	}
}
?>
