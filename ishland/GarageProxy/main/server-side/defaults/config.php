<?php
//This is the configration of GarageProxy.

define('CONFIG', Array(
    'workers' => Array(
        Array(
            'addr' => "tcp://0.0.0.0:12345",
            'remote' => "tcp://www.google.com",
            'processes' => 6
        ),
        Array(
            'addr' => "tcp://0.0.0.0:12346",
            'remote' => "tcp://www.youtube.com",
            'processes' => 6
        )
    ),
    'settings' => Array(
        'mode' => 1
    )
));
