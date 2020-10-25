<?php
namespace App\Controllers;

use Framework\SZConfig;
use Framework\SZController;
use Framework\SZLogger;
use Framework\SZServer;

class IndexController extends SZController {
    public function __construct() {

    }

    public function Index($id, $name, $timer) {
//        SZLogger::debug('test controller');
//        SZLogger::debug(SZConfig::Instance()->get('log_mode'));

        SZServer::Instance()->newTask('Main', array('test' => 10));

        return array($id, $name, $timer, posix_getpid());
    }

    public function test($key) {
        SZLogger::debug('server is reload');
        SZServer::Instance()->reload();

        return 'test';
//        return SZServer::Instance()->getTable($key);
    }
}