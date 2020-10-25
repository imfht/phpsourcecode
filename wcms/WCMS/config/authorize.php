<?php
/**
 * 以下是需要权限认证的
 * 如果不包含，那么就需要管理员权限
 */
return array(
        'news' => array(
                'i',
                'v',
                'c',
                'search',
                'views'),
        'member' => array('setface','repassword','setpassword','account','savearea','intro','info','center','savebirthday','add','getusernamebymobile','adduser'),
        'xingfu'=>array('add'),
        'face'=>array ('setface','index','resize'),
        'membergroup'=>array(),
        'system'=>array(),
        'barcode'=>array(),
        'qq'=>array('bind','savebind','qq','canclebind','removebind'),
        'changyan'=>array('rating','api'),
        'erp'=>array(),
        'app'=>array('iframe'),
        'geek'=>array(),
        'receipt'=>array(),
        'sign'=>array('vip','mass','sign','myclass','rating','saverating','refresh','listing','actual','edit','save'),
        'stores'=>array('mystore','saveaddress','confirm','add','addstores','remove','edit','save'),
        'customer'=>array('info','qr','create','ewm','add','save','iframe','edit','my','hot','article','news'),
        'guessmap'=>array('guess','today'),
        'adv'=>array(),
        'bank'=>array('pay','notify','bank'),
        'buy'=>array('iframe','hot','center','mx','cart','search','goods','flag','upload','scene','find'),
        'plugin'=>array('api','l'),
        'factory' => array('upload'),
        'flag'=>array(),
        'batch' => array(),
        'chart'=>array(),
        'case'=>array(),
        'cate' => array(),
        'ios'=>array(),
        'temp' => array(),
        'lang'=>array('language'),
        'log'=>array(),
        'rating'=>array('server','save'),
        'attr' => array('create'),
        'comment' => array(
                'comment','reply'),
        'order'=>array('shop','add','trade','cart','coupons','account','find','iframe'),
        'orderinfo'=>array(),
        'coupons'=>array('coupons','transfer','subtransfer','user','gethistorybytransfersno'),
        'csv'=>array('i','upload','export'),
        'stock'=>array(),
        'suppliers'=>array(),
        'vote' => array(
                'tickets',
                't'))

;