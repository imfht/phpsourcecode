#!/usr/bin/env php
<?php
if (PHP_SAPI !== 'cli') {
    exit('Please run in CLI mode');
}
require_once './public/index.php';
$commands = new system\Command\Command($argv);
echo $commands::generate($commands);