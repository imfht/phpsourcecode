<?php
use Testify\Testify;
require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';
load()->func('global');

$tester = new Testify('测试ver_compare函数');
$tester->test('测试', function(){
    global $_W,$tester;
    $version1 = '5.6.17-0+deb8u1';
    $version2 = '5.6';
    // $version1 = '5.6.01';
    // $version2 = '5.6.0a';
//    $version1 = '1.1.a';
//    $version2 = '1.2.a';
    $ver = ver_compare($version1, $version2);
    $sys = version_compare($version1, $version2);
    echo $ver;
    echo "<br>";
    echo $sys;
    $tester->assertEquals($ver, $sys);
});
$tester->run();