<?php
use \Cute\Utility\IP;
use \Cute\Network\JobServer;


app()->route('/', function () {
    $job_server = JobServer::getInstance();
    $job_server->setWorkerFile(APP_ROOT . '/workers/geo_worker.php');
    var_dump($job_server->reverse('Hello World'));
    var_dump($job_server->reverse('Hello', 'World'));
    $ipaddr = IP::getServerIP();
    var_dump($ipaddr);
    var_dump($job_server->ip_search_country($ipaddr));
    var_dump($job_server->ip_search_address($ipaddr));
    var_dump($job_server->phone_search_city('0035818', '028', '18475870001'));
});



