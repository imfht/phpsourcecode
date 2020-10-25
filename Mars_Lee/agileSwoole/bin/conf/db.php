<?php

/**
 * db connections
 */
return [
    'pool'  =>  [
        'pdo'   =>  [
            'start' =>  3,
            'max'   =>  5
        ],
        'async'   =>  [
            'start' =>  3,
            'max'   =>  5
        ],
//        'mongo'   =>  [
//            'start' =>  3,
//            'max'   =>  5
//        ]
    ],
        'mongodb'       =>      [
                'uri'                   =>      'mongodb://127.0.0.1:27017,127.0.0.1:27017,127.0.0.1:27017/',
                'uriOptions'            =>      [],
                'driverOptions'         =>      [
                        'replicaSet'            => 'rs',
                        'readPreference'        => 'primary'
                ],
//                'pool'  =>      [
//                        'max'   =>      10,
//                        'init'  =>      3
//                ]
        ],
        'redis'         =>      [
                'host'  =>      '127.0.0.1',
                'port'  =>      '6379',
                'db'    =>      5
        ],
        'mysql'         =>      [
                'host'          =>      '127.0.0.1',
                'user'          =>      'root',
                'password'      =>      'sa'
        ],
];