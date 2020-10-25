<?php

require_once('activerecord.php');
require_once('unit-test.php');

$db = DB::open('pgsql:host=127.0.0.1;dbname=zombie;', 'dba', '');
$Zombie = $db->zombies;
$sql = $Zombie->select('count(*) as size')->one();
var_dump($sql->size);
