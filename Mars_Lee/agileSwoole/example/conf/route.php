<?php

/**
 * route
 */
return [
        'route' =>[
                'get'     =>      [
                        [
                                'path'          =>      '/',
                                'dispatch'      =>      [\Controller\Welcome::class, 'index']
                        ],
                        [
                                'path'          =>      '/sync',
                                'dispatch'      =>      [\Controller\Sync::class, 'run'],
                                'type'          =>      \Component\Producer\Producer::PRODUCER_SYNC
                        ],
                        [
                                'path'          =>      '/process',
                                'dispatch'      =>      [\Controller\Process::class, 'run'],
                                'before'        =>      [\Controller\Process::class, 'before'],
                                'after'         =>      [\Controller\Process::class, 'after'],
                                'type'          =>      \Component\Producer\Producer::PRODUCER_PROCESS
                        ]
                ],
                'post'  =>      [
                        [
                                'path'          =>      '/process',
                                'dispatch'      =>      [\Controller\Process::class, 'run'],
                                'before'        =>      [\Controller\Process::class, 'before'],
                                'after'         =>      [\Controller\Process::class, 'after'],
                                'type'          =>      \Component\Producer\Producer::PRODUCER_PROCESS
                        ],
                ],
                'put'   =>      [
                        //put
                ],
                'delete'=>      [
                        //delete
                ]
        ]
];