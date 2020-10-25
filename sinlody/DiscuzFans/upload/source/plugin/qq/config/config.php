<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: config.php 2015-5-13 15:24:06Z $
 */
$setting = $_G['cache']['plugin']['qq'];
$setting['isopen'] = intval($setting['isopen']);
$setting['onlyallowbind'] = intval($setting['onlyallowbind']);
$setting['callback_domain'] = trim($setting['callback_domain']);
$setting['appid'] = trim($setting['appid']);
$setting['appkey'] = trim($setting['appkey']);
$setting['newusergroupid'] = intval($setting['newusergroupid']);
$setting['disableregrule'] = intval($setting['disableregrule']);
$setting['register_birthday'] = intval($setting['register_birthday']);
$setting['register_gender'] = intval($setting['register_gender']);
$setting['register_uinlimit'] = trim($setting['register_uinlimit']);
$setting['register_rewardcredit'] = intval($setting['register_rewardcredit']);
$setting['register_addcredit'] = trim($setting['register_addcredit']);
$setting['guest_groupid'] = intval($setting['guest_groupid']);
$setting['register_regverify'] = intval($setting['register_regverify']);
$setting['register_invite'] = trim($setting['register_invite']);

include_once DISCUZ_ROOT . './source/plugin/qq/function/function_common.php';
include_once DISCUZ_ROOT . './source/plugin/qq/function/function_qq.php';
?>