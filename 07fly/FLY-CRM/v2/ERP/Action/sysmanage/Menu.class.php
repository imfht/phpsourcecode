<?php
/*
 *
 * sysmanage.Menu  后台菜单管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */

class Menu extends Action
{

    var $common;
    private $cacheDir = '';//缓存目录
    private $auth;

    public function __construct()
    {
        $this->auth = _instance('Action/sysmanage/Auth');
    }

    public function menu()
    {
        $sql = "select *,name as text,id as tags  from fly_sys_menu order by sort asc,id desc";
        $list = $this->C($this->cacheDir)->findAll($sql);
        return $list;
    }

    /**列表请求
     * @return  echo json
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function menu_json()
    {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize = $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum) ? 1 : $pageNum;
        $pageSize = empty($pageSize) ? $GLOBALS["pageSize"] : $pageSize;
        //**************************************************************************

        //**获得传送来的数据做条件来查询
        $keywords = $this->_REQUEST("keywords");
        $pid = $this->_REQUEST("pid");
        $pid_son = $this->get_menu_self_son($pid);
        //$pid_txt=implode(",",$pid_son);
        $pid_txt = $pid;

        $where_str = " id>'0' ";
        if (!empty($keywords)) {
            $where_str .= " and name like '%$keywords%'";
        }
        if (!empty($pid)) {
            $where_str .= " and parentID in ($pid_txt)";
        } else {
            $where_str .= " and parentID='0'";
        }
        $countSql = "select *  from fly_sys_menu where  $where_str order by sort asc;";
        $totalCount = $this->C($this->cacheDir)->countRecords($countSql);    //计算记录数
        $beginRecord = ($pageNum - 1) * $pageSize;//计算开始行数

        $sql = "SELECT *  FROM fly_sys_menu WHERE  $where_str  order by sort asc limit $beginRecord,$pageSize";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $assignArray = array('list' => $list, "pageSize" => $pageSize, "totalCount" => $totalCount, "pageNum" => $pageNum);
        echo json_encode($assignArray);
    }

    /**右边树形
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function menu_tree_json()
    {
        $list = $this->menu();
        $tree = list2tree($list, 0, 0, 'id', 'parentID', 'name');
        echo json_encode($tree);
    }

    public function menu_check_list()
    {
        $sql = "select *,name as text,id as tags  from fly_sys_menu where visible='1' order by sort asc,id desc";
        $list = $this->C($this->cacheDir)->findAll($sql);
        return $list;
    }

    //得到数形参数
    function getTree($data, $pId = 0, $level = 0)
    {
        $tree = '';
        foreach ($data as $k => $v) {
            if ($v['parentID'] == $pId) { //父亲找到儿子
                $v['children'] = $this->getTree($data, $v['id'], $level + 1);
                $v['level'] = $level + 1;
                $v['treename'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '|--' . $v['name'];
                $tree[] = $v;
            }
        }
        return $tree;
    }


    //得到数形参数,针对bootstrop
    function leftTree($data, $pId = 0, $level = 0)
    {
        $tree = '';
        foreach ($data as $k => $v) {
            if ($v['parentID'] == $pId) { //父亲找到儿子
                $v['nodes'] = $this->leftTree($data, $v['id'], $level + 1);
                $v['level'] = $level + 1;
                $tree[] = $v;
            }
        }
        return $tree;
    }

    //boot tree格式输出
    public function menu_left_json()
    {
        $sql = "select *,name as text,id as tags  from fly_sys_menu where visible='1'  order by sort asc";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $list = $this->leftTree($list);
        echo json_encode($list);
    }

    //栏目显示
    public function menu_show()
    {
        $smarty = $this->setSmarty();
        $smarty->display('sysmanage/menu_show.html');
    }


    //输出树形参数
    function getTreeSelect($tree, $sid)
    {
        $html = '';
        if (!empty($tree)) {
            foreach ($tree as $key => $t) {
                $selected = ($t['id'] == $sid) ? "selected" : "";
                if ($t['children'] == '') {
                    $html .= "<option value='" . $t['id'] . "' $selected>" . $t['treename'] . "</option>";
                } else {
                    $html .= "<option value='" . $t['id'] . "' $selected>" . $t['treename'] . "</option>";
                    $html .= $this->getTreeSelect($t['children'], $sid);
                }
            }
        }
        return $html;
    }

    //输出树形参数
    function getTreeSelectHtml($optid, $sid = 0)
    {
        $list = $this->menu();
        $tree = $this->getTree($list, 0);
        $html = "<select name='$optid' id='$optid' class=\"form-control\"><option value='0'>请选上一级</option>";
        $html .= $this->getTreeSelect($tree, $sid);
        $html .= "</select>";
        return $html;
    }

    //添加
    public function menu_add()
    {
        $id = $this->_REQUEST("id");
        if (empty($_POST)) {
            $parentID = $this->getTreeSelectHtml("parentID", $id);
            $smarty = $this->setSmarty();
            $smarty->assign(array("parentID" => $parentID));//框架变量注入同样适用于smarty的assign方法
            $smarty->display('sysmanage/menu_add.html');
        } else {
            $into_data = array(
                'name' => $this->_REQUEST("name"),
                'url' => $this->_REQUEST("url"),
                'parentID' => $this->_REQUEST("parentID"),
                'sort' => $this->_REQUEST("sort"),
                'visible' => $this->_REQUEST("visible")
            );
            $this->C($this->cacheDir)->insert('fly_sys_menu', $into_data);
            $this->L("Common")->ajax_json_success("操作成功");
        }
    }

    //修改
    public function menu_modify()
    {
        $id = $this->_REQUEST("id");
        if (empty($_POST)) {
            $sql = "select * from fly_sys_menu where id='$id'";
            $one = $this->C($this->cacheDir)->findOne($sql);
            $parentID = $this->getTreeSelectHtml("parentID", $one["parentID"]);
            $smarty = $this->setSmarty();
            $smarty->assign(array("one" => $one, "parentID" => $parentID));
            $smarty->display('sysmanage/menu_modify.html');
        } else {
            $upt_data = array(
                'name' => $this->_REQUEST("name"),
                'url' => $this->_REQUEST("url"),
                'parentID' => $this->_REQUEST("parentID"),
                'sort' => $this->_REQUEST("sort"),
                'visible' => $this->_REQUEST("visible")
            );
            $this->C($this->cacheDir)->modify('fly_sys_menu', $upt_data, "id='$id'", true);
            $this->L("Common")->ajax_json_success("操作成功");
        }
    }

    //删除
    public function menu_del()
    {
        $id = $this->_REQUEST("id");
        $sql = "delete from fly_sys_menu where id in($id)";
        $this->C($this->cacheDir)->update($sql);
        $this->location("操作成功", "/sysmanage/Menu/menu_show/");
    }


    //将数组转化为树形数组
    public function arrToTree($data, $pid)
    {
        //echo $pid;
        $tree = array();
        foreach ($data as $k => $v) {
            if ($v['parentID'] == $pid) {
                $v['parentID'] = $this->arrToTree($data, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    public function menu_select_tree($optid, $sid = "")
    {
        $list = $this->menu();
        $tree = $this->getTree($list, 0);
        $html = "<select name='$optid' id='$optid' class=\"form-control\"><option value='0'>请选上一级</option>";
        $html .= $this->getTreeSelect($tree, $sid);
        $html .= "</select>";
        return $html;
    }

    //修改权限时调用
    public function menu_tree_arr()
    {
        $sql = "select *,name as text,id as tags  from fly_sys_menu  where visible='1' order by sort asc";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $data = $this->arrToTree($list, 0);
        return $data;
    }


    //输出所有需加入权限的id
    public function menu_auth_list()
    {
        $rtArr = array();
        $sql = "select id from fly_sys_menu  where visible='1' order by sort asc,id desc ";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $v) {
            $rtArr[] = $v["id"];
        }
        return $rtArr;
    }

    //排序
    public function menu_modify_sort()
    {
        $id = $this->_REQUEST('id');
        $sort = $this->_REQUEST('sort');
        $upt_data = array(
            'sort' => $this->_REQUEST("sort")
        );
        $this->C($this->cacheDir)->modify('fly_sys_menu', $upt_data, "id='$id'", true);
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //修改地址
    public function menu_modify_url()
    {
        $id = $this->_REQUEST('id');
        $url = $this->_REQUEST('url');
        $upt_data = array(
            'url' => $this->_REQUEST("url")
        );
        $this->C($this->cacheDir)->modify('fly_sys_menu', $upt_data, "id='$id'", true);
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //是否启用
    public function menu_modify_visible()
    {
        $id = $this->_REQUEST('id');
        $upt_data = array(
            'visible' => $this->_REQUEST("value")
        );
        $this->C($this->cacheDir)->modify('fly_sys_menu', $upt_data, "id='$id'", true);
        $this->L("Common")->ajax_json_success("操作成功");
    }

    /**获得所有指定id所有父级
     * @param int $menuid
     * @param array $data
     * @return array
     */
    public function get_menu_all_pid($menuid = 0, $data = [])
    {
        $sql = "select *  from fly_sys_menu where parentID='$menuid' order by sort asc;";
        $info = $this->C($this->cacheDir)->findOne($sql);
        if (!empty($info) && $info['parentID']) {
            $data[] = $info['parentID'];
            return $this->get_menu_all_pid($info['parentID'], $data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $menuid
     * @param array $data
     * @return array
     */
    public function get_menu_all_son($menuid = 0, $data = [])
    {

        $sql = "select *  from fly_sys_menu where parentID='$menuid' order by sort asc;";
        $sons = $this->C($this->cacheDir)->findAll($sql);
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->get_menu_all_son($v['id'], $data); //注意写$data 返回给上级
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return false;
        }
        return $data;
    }

    /**得到自己的和子级
     * @param $id
     * @return array
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function get_menu_self_son($id)
    {
        $sons = $this->get_menu_all_son($id);
        $sons[] = $id;
        return $sons;
    }

}//
?>