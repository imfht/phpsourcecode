<?php


$lockfile = __DIR__.DIRECTORY_SEPARATOR.".." . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . ".time";


if (file_exists($lockfile)) {
    $time = (int)file_get_contents($lockfile);
} else {
    $time = 0;
}

$config_file = __DIR__.DIRECTORY_SEPARATOR.".." . DIRECTORY_SEPARATOR . "config.ini";
if (!file_exists($config_file)) {
    echo "It is not find config.ini,You can copy config.ini.example to config.ini  to set you config";
    exit;
}
if (filemtime($config_file) > $time) {
echo "re install \n";
$include_file = __DIR__.DIRECTORY_SEPARATOR.".." . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . "install.php";
require $include_file;
}

$is_win = strtoupper(substr(PHP_OS,0,3)) == 'WIN';
{{php_bat}}

echo "\nStarting nginx...";



runcmd('{{nginx_cmd}}{{nginx_root}}');

echo "\nit works";
function runcmd($cmd)
{

if(strtoupper(substr(PHP_OS,0,3)) == 'WIN')
{
pclose(popen('start /B '. $cmd, 'r'));
}else
{
pclose(popen($cmd.' > /dev/null &', 'r'));
}
}
exit;



