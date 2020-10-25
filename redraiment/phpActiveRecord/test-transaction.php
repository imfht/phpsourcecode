<?php

require_once('activerecord.php');
require_once('unit-test.php');

$db = DB::open('pgsql:host=127.0.0.1;dbname=test;', 'dba', '');

$db->dropTable('zombies');
$Zombie = $db->createTable('zombies',
                           'name varchar(64) not null'
);

$db->tx(function() use($Zombie) {
    $Zombie->create("name:", "Ash");
    $Zombie->create("name:", "Bob");
    $Zombie->create("nae:", "Jim");
});
