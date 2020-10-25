<?php
if (!$_ENV) {
    echo "It is need set variables_order = \"EGPCS\"  in php.ini";
    exit;
}
$base_root = str_replace(DIRECTORY_SEPARATOR."default", "", __DIR__);
$ini = parse_ini_file($base_root . DIRECTORY_SEPARATOR . "config.ini", true);


if (isset($ini['php_env']) && isset($ini['php_env'][$_ENV['APP_JT']])) {
    foreach ($ini['php_env'][$_ENV['APP_JT']] as $key => $value) {
        putenv($key . "=" . $value);
        $_ENV[$key] = $_SERVER[$key] = $value;

    }
}




chdir(dirname($_ENV['SCRIPT_FILENAME_origin']));
$_ENV['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] = $_ENV['SCRIPT_FILENAME_origin'];
unset($_ENV['SCRIPT_FILENAME_origin']);
unset($_SERVER['SCRIPT_FILENAME_origin']);
unset($_ENV['APP_JT']);
unset($_SERVER['APP_JT']);
if(file_exists($root_file = "./" . basename($_ENV['SCRIPT_NAME']))){
    require_once $root_file;
}else{
    echo "who you are?";
    exit;
}
?>