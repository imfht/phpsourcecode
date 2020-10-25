<?php

use \Cute\Network\JobServer;
use \Cute\Contrib\GEO\PhoneLoc;
use \Cute\Contrib\GEO\IPCountry;
use \Cute\Contrib\GEO\QQWry;

$job_server = JobServer::getInstance();

//反转字符串或数组
$job_server->reverse = function ($job) {
    $data = $job->worknorm();
    if (count($data) === 1 && is_string($data[0])) {
        return strrev($data[0]);
    } else {
        return array_reverse($data);
    }
};

//查找电话号码归属地
$job_server->phone_search_city = function ($job) {
    $phones = $job->worknorm();
    $dat = new PhoneLoc(CUTE_ROOT . '/misc/phoneloc.dat');
    $result = [];
    foreach ($phones as $phone) {
        $result[$phone] = $dat->search($phone);
    }
    return $result;
};

//查找IP所在国家代码
$job_server->ip_search_country = function ($job) {
    @list($ipaddr) = $job->worknorm();
    $dat = new IPCountry(CUTE_ROOT . '/misc/ipcountry.dat');
    $result = $dat->search($ipaddr);
    return $result;
};

//查找IP所在位置
$job_server->ip_search_address = function ($job) {
    @list($ipaddr) = $job->worknorm();
    $dat = new QQWry(CUTE_ROOT . '/misc/qqwry.dat');
    $result = $dat->search($ipaddr);
    return implode(' ', $result);
};

$job_server->run();
?>
