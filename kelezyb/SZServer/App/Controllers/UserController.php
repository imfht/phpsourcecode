<?php
namespace App\Controllers;

use Framework\SZController;
use Framework\SZServer;

class UserController extends SZController {
    public function Login($uid) {
        SZServer::Instance()->login($uid, $this->fd);
    }

    public function Forward($uid, $message) {
        SZServer::Instance()->forward($uid, $message);
    }
}