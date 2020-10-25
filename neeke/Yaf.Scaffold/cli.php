<?php
define ("APPLICATION_PATH", dirname(__FILE__) . "/app");
$application = new Yaf_Application(APPLICATION_PATH."/../conf/app.ini");


$response = $application->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());

/**
 *  php cli.php request_uri="/blog/"
 *  php cli.php request_uri="/scripts/Order/index"
 */

?>
