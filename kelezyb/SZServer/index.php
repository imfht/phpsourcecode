<?php
include 'PRails/prails.php';

use PRails\Application;
use PRails\PError;

try {
    $app = Application::Instance();
    $app->run();
} catch (PError $ex) {
    echo '>>> ' . $ex->getMessage();
}