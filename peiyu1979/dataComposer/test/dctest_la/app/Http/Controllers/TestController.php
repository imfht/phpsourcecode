<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2017-5-8
 * Time: 17:03
 */

namespace App\Http\Controllers;

use App\Libs\Request;
use App\Libs\RequestParams;
use Illuminate\Http\Request as HttpRequest;
use DB;
use Config;
use Exception;
use Illuminate\Support\Str;
use DataComposer\Engine;

class TestController
{


	public function test()
	{
		$t=new Engine("worker");
		$t->SetParameterValue('worker',['v'=>23]);
		/*$t->SetCallback('apids',function ($name, $db, $para) {

					return ["data" => [
						["a" => 24, "dddd" => 4343],
						["a" => 24, "dddd" => 444444444444444],
						["a" => 25, "dddd" => 55555555],
					]];

		}, 'end' );*/

		return $t->GetData();//['category','area']
		return "ok";
	}
	
	public function cb($name, $db, $para){
		if($name=='apids' && array_key_exists('extend',$para) && $para['extend']=='end'){
			return ["data" => [
				["a" => 24, "dddd" => 4343],
				["a" => 24, "dddd" => 444444444444444],
				["a" => 25, "dddd" => 55555555],
			]];
		}
	}


}