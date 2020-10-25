<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\HttpUserAgent;

return [
    'session_config' => [
        //'cookie_lifetime'     => 60*60,
        'remember_me_seconds' => 60*60*24*30,

        'gc_maxlifetime'      => 60*60*24*30,
    ],
    'session_manager' => [
        'validators' => [
            //RemoteAddr::class,
            //HttpUserAgent::class,
        ]
    ],

    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];
