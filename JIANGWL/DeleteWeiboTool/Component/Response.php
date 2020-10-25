<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 0:16
 */
class Response
{
	const success_status = 1;
	const failed_status = 2;

	public function info($info='',$status = self::success_status){
		echo json_encode(array(
			'info' => $info,
			'status' => $status
		));
	}

	public function success($info='',$status = self::success_status){
		echo json_encode(array(
			'info' => $info,
			'status' => $status
		));
	}

	public function failed($info='',$status = self::failed_status){
		echo json_encode(array(
			'info' => $info,
			'status' => $status
		));
	}
}