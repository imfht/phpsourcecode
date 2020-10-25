<?php
require_once("chk.php");
require  '../AppCode/class/admin.class.php';

$psize = isset($psize) ? intval($psize) : 0;
if ($psize > 0 && $psize != $pagesize)
{
    $pagesize = $psize;
    $offset = ($page - 1) * $pagesize;
}

$page = isset($page) ? max(intval($page), 1) : 1;
$catid = isset($catid) ? intval($catid) : 0;
$areaid = isset($areaid) ? intval($areaid) : 0;
$itemid = isset($itemid) ? (is_array($itemid) ? array_map('intval', $itemid) : intval($itemid)) : 0;
$pagesize = 30;
$offset = ($page-1)*$pagesize;


$do = new admin();
$menus = array(
    array('添加管理员', '?moduleid=' . $moduleid . '&file=' . $file . '&action=add'),
    array('管理员管理', '?moduleid=' . $moduleid . '&file=' . $file),
);
$this_forward = '?file=' . $file;

switch ($action)
{
    case 'add':
        if ($submit)
        {
            if ($do->pass($post))
            {
                $do->add($post);
                dmsg('添加成功', $forward);
            } else
            {
                msg($do->errmsg);
            }
        } else
        {
            foreach ($do->fields as $v)
            {
                isset($$v) or $$v = '';
            }
            $filepath = 'about/';
            $filename = '';
            $menuid = 0;
            include tpl('webpage_edit', $module);
        }


         msg('管理员添加成功，下一步请分配权限和管理面板', '');
        break;
    case 'edit':
        if ($submit)
        {
            $admin = $admin == 1 ? 1 : 2;
            if ($do->set_admin($username, $admin, $role, $aid))
            {
                $r = $do->get_one($username);
                $userid = $r['userid'];
                if ($r['admin'] == 2)
                {
                    $do->cache_right($userid);
                    $do->cache_menu($userid);
                }
                dmsg('修改成功', '?file=' . $file);
            }
            msg($do->errmsg);
        } else
        {
            if (!$userid) msg();
            $user = $do->get_one($userid, 0);
            include tpl('admin_edit');
        }
        break;
    case 'delete':
        if ($do->delete_admin($username)) dmsg('撤销成功', $this_forward);
        msg($do->errmsg);
        break;
    default:
        $sfields = array('按条件', '用户名', '姓名', '角色');
        $dfields = array('username', 'username', 'truename', 'role');
        isset($fields) && isset($dfields[$fields]) or $fields = 0;
        $type = isset($type) ? intval($type) : 0;
        $areaid = isset($areaid) ? intval($areaid) : 0;
        $fields_select = dselect($sfields, 'fields', '', $fields);
        $condition = ' 1=1 ';
        if ($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
        if ($type) $condition .= " AND admin=$type";
        if ($areaid) $condition .= ($AREA[$areaid]['child']) ? " AND aid IN (" . $AREA[$areaid]['arrchildid'] . ")" : " AND aid=$areaid";
        $lists = $do->get_list($condition);

        include tpl('admin');
        break;
}

?>
