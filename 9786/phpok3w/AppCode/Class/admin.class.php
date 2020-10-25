<?php
defined('IN_SYSTEM') or exit('Access Denied');

class admin
{
    var $userid;
    var $username;
    var $db;
    var $pre;
    var $errmsg = errmsg;

    function admin()
    {
        global $db, $admin, $CFG;
        $this->db = & $db;
        $this->pre = $this->db->pre;
    }

    function is_member($username)
    {
        return $this->db->get_one("SELECT userid FROM {$this->pre}member WHERE username='$username'");
    }

    function count_admin()
    {
        $r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}member WHERE groupid=1 AND admin=1 ");
        return $r['num'];
    }

    function set_admin($username, $admin, $role, $aid)
    {
        $username = trim($username);
        $r = $this->is_member($username);
        if (!$r) return $this->_('会员不存在');
        $userid = $r['userid'];
        if ($this->founderid == $userid)
        {
            $admin = 1;
            $aid = 0;
        }
        if ($admin == 1) $aid = 0;
        $this->db->query("UPDATE {$this->pre}member SET groupid=1,admin=$admin,role='$role',aid=$aid WHERE userid=$userid");
        $this->db->query("UPDATE {$this->pre}company SET groupid=1 WHERE userid=$userid");
        return true;
    }

    function move_admin($username)
    {
        $r = $this->get_one($username);
        if ($r && $r['admin'] > 0)
        {
            if ($r['userid'] == $this->founderid) return $this->_('创始人不可改变级别');
            if ($r['admin'] == 1 && $this->count_admin() < 2) return $this->_('系统最少需要保留一位超级管理员');
            $admin = $r['admin'] == 1 ? 2 : 1;
            $this->db->query("UPDATE {$this->pre}member SET admin=$admin WHERE username='$username'");
            return true;
        } else
        {
            return $this->_('管理员不存在');
        }
    }

    function delete_admin($username)
    {
        $r = $this->get_one($username);
        if ($r)
        {
            if ($r['userid'] == $this->founderid) return $this->_('创始人不可删除');
            if ($r['admin'] == 1 && $this->count_admin() < 2) return $this->_('系统最少需要保留一位超级管理员');
            $userid = $r['userid'];
            $groupid = $r['regid'] ? $r['regid'] : 6;
            $this->db->query("UPDATE {$this->pre}member SET groupid=$groupid,admin=0,role='',aid=0 WHERE userid=$userid");
            $this->db->query("UPDATE {$this->pre}company SET groupid=$groupid WHERE userid=$userid");
            $this->db->query("DELETE FROM {$this->pre}admin WHERE userid=$userid");
            cache_delete('menu-' . $userid . '.php');
            cache_delete('right-' . $userid . '.php');
            return true;
        } else
        {
            return $this->_('会员不存在');
        }
    }

    function get_one($user, $type = 1)
    {
        $fields = $type ? 'username' : 'userid';
        return $this->db->get_one("SELECT * FROM {$this->pre}member WHERE `$fields`='$user'");
    }

    function get_list($condition)
    {
        global $pages, $page, $pagesize, $offset, $pagesize, $CFG, $sum;
        if ($page > 1 && $sum)
        {
            $items = $sum;
        } else
        {
            $r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}admin WHERE $condition");
            $items = $r['num'];
        }
        $pages = pages($items, $page, $pagesize);
        $admins = array();

        echo "SELECT * FROM {$this->pre}admin WHERE $condition ORDER BY adminid ASC LIMIT $offset,$pagesize";
        $result = $this->db->query("SELECT * FROM {$this->pre}admin WHERE $condition ORDER BY adminid ASC LIMIT $offset,$pagesize");
        while ($r = $this->db->fetch_array($result))
        {
            $r['logintime'] = timetodate($r['logintime'], 5);
            $r['adminname'] = $r['admin'] == 1 ? ($CFG['founderid'] == $r['userid'] ? '<span class="f_red">网站创始人</span>' : '<span class="f_blue">超级管理员</span>') : '普通管理员';
            $admins[] = $r;
        }
        return $admins;
    }

    function get_right($userid)
    {
        global $MODULE;
        $rights = array();
        $result = $this->db->query("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url='' ORDER BY adminid DESC ");
        while ($r = $this->db->fetch_array($result))
        {

            $rights[] = $r;
        }
        return $rights;
    }

    function get_menu($userid)
    {
        $menus = array();
        $result = $this->db->query("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url!='' ORDER BY listorder ASC,adminid ASC ");
        while ($r = $this->db->fetch_array($result))
        {
            $menus[] = $r;
        }
        return $menus;
    }

    function update($userid, $right, $admin)
    {
        if (isset($right[-1]))
        {
            $this->add($userid, $right[-1], $admin);
            unset($right[-1]);
            $type = 1; //right
        } else
        {
            $type = 0; //menu
        }
        $this->add($userid, $right[0], $admin);
        unset($right[0]);
        foreach ($right as $k => $v)
        {
            if (isset($v['delete']))
            {
                $this->delete($k);
                unset($right[$k]);
            }
        }
        $this->edit($right, $type);
        if ($admin == 1) $this->db->query("DELETE FROM {$this->pre}admin WHERE userid=$userid AND url=''");
        $this->cache_right($userid);
        $this->cache_menu($userid);
        return true;
    }

    function add($post)
    {
        if (isset($right['url']))
        {
            if (!$right['title'] || !$right['url']) return false;
            $r = $this->db->get_one("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url='" . $right['url'] . "'");
            if ($r) return false;

        }
        $right['userid'] = $userid;
        $sql1 = $sql2 = '';
        foreach ($right as $k => $v)
        {
            $sql1 .= ',' . $k;
            $sql2 .= ",'$v'";
        }
        $sql1 = substr($sql1, 1);
        $sql2 = substr($sql2, 1);
        $this->db->query("INSERT INTO {$this->pre}admin ($sql1) VALUES($sql2)");
    }

    function edit($right, $type = 0)
    {
        if ($type)
        {
            //when module admin, have all rights
            $moduleids = $adminids = array();
            foreach ($right as $k => $v)
            {
                if (!$v['file'])
                {
                    $moduleids[] = $v['moduleid'];
                    $adminids[$v['moduleid']] = $k;
                    $right[$k]['action'] = $right[$k]['catid'] = '';
                }
            }
            if ($moduleids)
            {
                foreach ($right as $k => $v)
                {
                    if (in_array($v['moduleid'], $moduleids) && !in_array($k, $adminids))
                    {
                        unset($right[$k]);
                        $this->delete($k);
                    }
                }
            }
        }
        foreach ($right as $key => $value)
        {
            if (isset($value['title']))
            {
                if (!$value['title'] || !$value['url']) continue;
            } else
            {
                $value['moduleid'] = intval($value['moduleid']);
                if (!$value['moduleid']) continue;
            }
            $sql = '';
            foreach ($value as $k => $v)
            {
                $sql .= ",$k='$v'";
            }
            $sql = substr($sql, 1);
            $this->db->query("UPDATE {$this->pre}admin SET $sql WHERE adminid='$key'");
        }
    }



    function cache_menu($userid)
    {
        $menus = $this->get_menu($userid);
        $menu = $r = array();
        foreach ($menus as $k => $v)
        {
            $r['title'] = $v['title'];
            $r['style'] = $v['style'];
            $r['url'] = $v['url'];
            $menu[] = $r;
        }
        cache_write('menu-' . $userid . '.php', $menu);
    }

    function delete($adminid)
    {
        $this->db->query("DELETE FROM {$this->pre}admin WHERE adminid=$adminid");
    }

    function _($e)
    {
        $this->errmsg = $e;
        return false;
    }
}

?>