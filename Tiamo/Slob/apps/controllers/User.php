<?php

namespace App\Controller;

use App\BasicController;
use Swoole;

class User extends BasicController {

	function home() {
		$this->session->start();
		Swoole\Auth::login_require();
	}

	function logout() {
		$this->user->logout();
		$this->http->redirect(WEBROOT);
	}
}
