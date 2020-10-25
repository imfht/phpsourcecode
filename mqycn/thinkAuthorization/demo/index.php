<?php
define("APP_PATH", "thinkAuthorization/");
define("APP_NAME", "ThinkAuthorization");
define("APP_DEBUG", true);
require "ThinkPHP/ThinkPHP.php";

require(APP_PATH . "Common/Behavior/AuthorizationBehavior.class.php");
?>