<?php

/**
 * Description of JsonResponseFormatter
 *
 * @author ken <vb2005xu@qq.com>
 */
class JsonResponseFormatter extends yii\web\JsonResponseFormatter
{

	protected function formatJson()
	{
		$response->getHeaders()->set('Content-Type', 'text/html; charset=UTF-8');
		$response->content = Json::encode($response->data);
	}

}
