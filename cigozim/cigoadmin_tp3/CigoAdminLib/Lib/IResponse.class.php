<?php

namespace CigoAdminLib\Lib;

interface IResponse
{
	const FLAG_STATUS = 'status';
	const FLAG_DATA = 'data';
	const FLAG_MSG = 'msg';
	const FLAG_ERRORCODE = 'error_code';

	function makeResponse($status = false, $data = array(), $msg = '', $errorCode = '');
}