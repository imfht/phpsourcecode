<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: table_qq_member_guset.php 2015-5-13 15:24:06Z $
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_qq_member_bindlog extends discuz_table {

    public function __construct() {
        $this->_table = 'qq_member_bindlog';
        $this->_pk = 'id';
        parent::__construct();
    }

    public function count_uid_openid_type($openid, $type) {
        $count = (int) DB::result_first('SELECT count(DISTINCT uid) FROM %t WHERE openid=%s AND type=%d', array($this->_table, $openid, $type));
        return $count;
    }

}