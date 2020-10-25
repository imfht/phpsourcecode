<?php

function get_permission_str($uid='')
{
global $db;
if (empty($uid)) $uid = $_SESSION['ADMIN_UID'];
$rs = $db->row_select_one('admin',"adminid='$uid'",'admingroup');
$grouprs = $db->row_select_one('admingroup',"id=".$rs['admingroup'],'permission');
return $grouprs['permission'];
}
function get_permission_arr($uid='')
{
global $db;
$str_id = get_permission_str($uid);
if (empty($str_id)) return array();
$rs = $db->row_select('permission',"pid in($str_id)");
$arr = array();
foreach ($rs as $v)
{
if (!empty($v['mod'])) $arr[$v['mod']][] = $v['ac'];
}
return (array)$arr;
}
function has_permission()
{
global $m;
if (in_array($m,array('main','index','login'))) return true;
if ($_SESSION['ADMIN_TYPE'] == 'administrator') return true;
$arr_permission = get_permission_arr();
if (!isset($arr_permission[$m])) return false;
if (count($arr_permission[$m]) == 1 &&$arr_permission[$m][0] == '') return true;
foreach ($arr_permission[$m] as $v)
{
if (isset($_REQUEST['a']) &&in_array($_REQUEST['a'],(array)$v)) return true;
}
return false;
}
function permission_chk()
{
if (has_permission()) return '';
header('Content-type:text/html;Charset='.CHARSET);
exit('没有权限');
}
?>