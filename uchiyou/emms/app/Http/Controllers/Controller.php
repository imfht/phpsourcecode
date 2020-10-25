<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * 输出 Json 信息
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function jsonResult($errcode, $data = null,$message = null)
    {
    
    	$message = empty($message) ? config('errors.'.$errcode) : $message;
    
    	$content = ['errcode' => $errcode, 'message' => $message];
    	if(empty($data) === false){
    		$content['data'] = $data;
    	}
    
    	$response = response();
    	$response = $response->json($content);
    	$response->header('Pragma','no-cache')
    	->header('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    	return $response;
    }
    
}
