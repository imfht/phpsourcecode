<?php
define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
load()->web('common');
load()->web('template');
load()->func('communication');
load()->model('cache');
load()->model('cloud');
load()->classs('coupon');
load()->func('file');

for ($i=1; $i <= 100000; $i++) {
	$data=array('emo'=>$i, 'contents'=>"YToxNDp7czo5OiJzdWJzY3JpYmUiO2k6MTtzOjY6Im9wZW5pZCI7czoyODoib1RLekZqc2JOdHpFUUJUMXB3S1JGVTUwNmk5NYToxNDp7czo5OiJzdWJzY3JpYmUiO2k6MTtzOjY6Im9wZW5pZCI7czoyODoib1RLekZqc2JOdHpFUUJUMXB3S1JGVTUwNmk5NYToxNDp7czo5OiJzdWJzY3JpYmUiO2k6MTtzOjY6Im9wZW5pZCI7czoyODoib1RLekZqc2JOdHpFUUJUMXB3S1JGVTUwNmk5NYToxNDp7czo5OiJzdWJzY3JpYmUiO2k6MTtzOjY6Im9wZW5pZCI7czoyODoib1RLekZqc2JOdHpFUUJUMXB3S1JGVTUwNmk5NYToxNDp7czo5OiJzdWJzY3JpYmUiO2k6MTtzOjY6Im9wZW5pZCI7czoyODoib1RLekZqc2JOdHpFUUJUMXB3S1JGVTUwNmk5N");
	pdo_insert('test', $data);
}
?>