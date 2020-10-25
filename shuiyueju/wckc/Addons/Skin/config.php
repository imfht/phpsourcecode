<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-2-28
 * Time: 下午2:14
 * @author 郑钟良<zzl@ourstu.com>
 */
require_once(ONETHINK_ADDON_PATH . 'Skin/Common/function.php');

$skinList=getSkinList();
return array(
    'defaultSkin'=>array(
        'title'=>'设置默认皮肤：',
        'options'=>$skinList,
        'value'=>'default',//安装时有用，之后都是从数据库读取配置
    ),
    'mandatory'=>array(//是否强制执行默认皮肤（管理员设置的皮肤）
        'title'=>'优先执行默认皮肤：',
        'options'=>array(
            '1'=>'是',
            '0'=>'否',
        ),
        'value'=>'0',
        'tip'=>'默认不开启，开启后优先执行默认皮肤，即管理员设置的皮肤。'
    ),
    'canSet'=>array(//用户是否可以设置皮肤
        'title'=>'用户是否可以设置皮肤：',
        'options'=>array(
            '1'=>'是',
            '0'=>'否',
        ),
        'value'=>'1',
        'tip'=>'默认可以设置，即用户可换肤。'
    ),
);