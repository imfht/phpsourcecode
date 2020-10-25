<?php
namespace Test;

use Monkey;

/**
 * AppWeb
 * Web应用服务类，这个类是每个应用必须的，而且类名也必须是AppWeb
 * @package DefaultApp
 */
class App extends Monkey\App {

    public function __construct($staticDir) {
        $this->DEBUG = E_ALL ^ E_NOTICE ^ E_WARNING;

        parent::__construct($staticDir);
    }
}




