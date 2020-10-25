<?php

$webApp = require_once __DIR__.'/../bootstrap/web.php';

$webApp->startApplication()
    ->terminate();

