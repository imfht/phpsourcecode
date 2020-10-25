<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-10
 * Time: 18:18
 */

namespace DataComposer\Providers\api;
use GuzzleHttp\Client;

class guzzle
{
	protected $client;
	public function __construct(){
		$this->client=new Client();
	}
	
	public function apiRequest($method, $url, $options){
		$data= $this->client->request($method, $url, $options)->getBody()->getContents();

		return $data;
	}



}