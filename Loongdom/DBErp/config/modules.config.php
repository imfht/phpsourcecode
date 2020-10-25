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

return [
    'Zend\Serializer',
    'Zend\InputFilter',
    'Zend\Filter',
    'Zend\Hydrator',
    'Zend\Paginator',
    'Zend\ServiceManager\Di',
    'Zend\Session',
    'Zend\Mvc\Plugin\Prg',
    'Zend\Mvc\Plugin\Identity',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Mvc\Plugin\FilePrg',
    'Zend\Mvc\I18n',
    'Zend\I18n',
    'Zend\Mvc\Console',
    'Zend\Log',
    'Zend\Form',
    'Zend\Db',
    'Zend\Cache',
    'Zend\Router',
    'Zend\Validator',
    'DoctrineModule',
    'DoctrineORMModule',
    'ZendDeveloperTools',

    'Admin',    //系统
    'Store',    //基础数据
    'Customer', //客户
    'Purchase', //采购
    'Finance',  //资金
    'Sales',    //销售
    'Shop',     //商城
    'Report',   //报告
    'Stock',    //库存
];
