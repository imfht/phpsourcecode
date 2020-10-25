<?php
require 'class.builder.php';
$x = new CHMBuilder();
echo 'Core> Compiling HHP'."\r\n";
$x->buildHHP();
echo 'Core> HHP Compiled'."\r\n";
echo 'Core> Compiling HHC'."\r\n";
$x->buildHHC();
echo 'Core> HHC Compiled'."\r\n";
echo 'Core> Compiling HHI'."\r\n";
$x->buildHHI();
echo 'Core> HHI Compiled'."\r\n";
echo 'Core> Compiling CHM'."\r\n";
echo 'CHM Builder> ';
passthru(dirname(__FILE__).'/Core/hhc.exe hhp.ini');
echo "\r\n".'Core> CHM Compiled'."\r\n";
echo 'Core> Removing Temporary Files'."\r\n";
$x->clean();
echo 'Core> Temporary Files Removed'."\r\n";