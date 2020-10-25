<?php
require 'DB.php';
$db = &DB();

/**
 * 现在，你就可以用$db进行数据库操作了，类似CI中的this->db
 * 例如：$db->select('value')->get_where('options', ['name'=>'site_url'], 1)->result_array();
 *
 * 详细使用文档请见：http://codeigniter.org.cn/user_guide/database/index.html
 */