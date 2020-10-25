<?php

namespace tests;

use Ke\Utils\CurlRequest;
use PHPUnit\Framework\TestCase;

class CurlRequest_Test extends TestCase
{

	public function testRequestWithHeader()
	{
		$req = new CurlRequest('http://ip-api.com/json/');
		$req->setFetchResponseHeaders(true);
		$req->send();


		var_dump($req->getResponse());
		var_dump($req->getResponseHeaders());
	}
}
