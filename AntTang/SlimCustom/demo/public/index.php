<?php

/*
 |--------------------------------------------------------------------------
 | Require SlimCustom Application
 |--------------------------------------------------------------------------
 | From SlimCustom application are introduced, and then start your tour
 | Just simple!
 |
*/
$application = require __DIR__ . '/../../SlimCustom/index.php';

/*
 |--------------------------------------------------------------------------
 | Run The Application
 |--------------------------------------------------------------------------
 |
 | Once we have the application, we can handle the incoming request
 | through the kernel, and send the associated response back to
 | the client's browser allowing them to enjoy the creative
 | and wonderful application we have prepared for them.
 |
 */
$application->setName('Demo')->setPath(realpath(__DIR__ . '/../'))->run();
