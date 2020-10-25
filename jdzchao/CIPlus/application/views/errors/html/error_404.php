<?php defined('BASEPATH') OR exit('No direct script access allowed');

header('content-type:application/json;charset=utf8');
echo json_encode(array(
    'code' => 404,
    'message' => 'API not found',
    'data' => null
));
exit;
