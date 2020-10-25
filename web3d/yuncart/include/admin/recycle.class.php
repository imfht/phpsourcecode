<?php

defined('IN_CART') or die;

/**
 *
 * 回收站
 * 
 */
class Recycle extends Base
{

    /**
     *
     * 回收站记录
     * 
     */
    public function index()
    {

        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where = array();
        //搜索
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $this->data['q'] = $q;
        $q && $where['title'] = "like '%" . $q . "%'";

        $count = DB::getDB()->selectcount("recycle", $where);
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["records"] = DB::getDB()->select("recycle", "title,recycleid,type,addtime", $where, "recycleid DESC", $this->data['pagearr']['limit']);
        }
        $this->data['delhint'] = __('delete_m_n_records', array(1, 10));
        $this->output("recycle_index");
    }

    /**
     *
     * 保存回收站
     * 
     */
    public function recyclesave()
    {
        $opertype = strtolower(trim($_REQUEST["opertype"]));
        $text = __("recycle");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_POST["field"]);
                if ($field == "delete") { //删除记录
                    $recycleidstr = $_POST["idstr"];
                    if ($recycleidstr) {
                        $recycleids = explode(",", $recycleidstr);
                        $where = "recycleid in " . cimplode($recycleids);
                        $records = DB::getDB()->select("recycle", "tableid,table,tablefield", $where);
                        foreach ($records as $record) {
                            $this->deletetable($record['table'], $record['tablefield'], $record['tableid']);
                        }
                        DB::getDB()->delete("recycle", $where);
                    }
                } elseif ($field == "restore") { //恢复
                    $recycleidstr = $_POST['idstr'];
                    if ($recycleidstr) {
                        $recycleids = explode(",", $recycleidstr);
                        $where = "recycleid in " . cimplode($recycleids);
                        $records = DB::getDB()->select("recycle", "tableid,table,tablefield", $where);
                        foreach ($records as $record) {
                            DB::getDB()->update($record['table'], "isdel=0", "{$record['tablefield']}={$record['tableid']} AND isdel =1");
                        }
                        DB::getDB()->delete("recycle", $where);
                    }
                }
                exit('success');
                break;
        }
    }

    /**
     *
     * 回收站清空
     * 
     */
    public function recyclempty()
    {

        $page = empty($_GET["page"]) ? 1 : intval($_GET["page"]);
        $count = DB::getDB()->selectcount("recycle");
        $pagesize = 10;
        $totalpage = ceil($count / $pagesize);
        $offset = ($page - 1) * $pagesize;

        $records = DB::getDB()->select("recycle", "tableid,table,tablefield", null, "recycleid DESC", $offset . "," . $pagesize);
        foreach ($records as $record) {
            $this->deletetable($record['table'], $record['tablefield'], $record['tableid']);
        }
        if ($page >= $totalpage) {
            $prefix = DB::getDB()->getTablePrefix();
            DB::getDB()->query("TRUNCATE TABLE {$prefix}recycle");
            echo __("recycle_empty_finish") . "<script>setTimeout(function(){window.location.reload()},1000)</script>";
        } else {
            $page ++;
            $url = url("admin", "recycle", "recyclempty", "page={$page}", false);
            echo __("delete_m_n_records", array(($page - 1) * $pagesize + 1, $page * $pagesize)) . "<script>$.oper.runjs('{$url}')</script>";
        }
    }

    /**
     *
     * 记录删除
     * 
     */
    private function deletetable($table, $field, $fieldvalue)
    {
        if (!$field)
            return '';
        $where = $field . "=" . $fieldvalue;
        $wheredel = $where . " AND isdel = 1";
        DB::getDB()->delete($table, $wheredel);
        if (DB::getDB()->getAffectedRows()) {
            switch ($table) {
                case 'type':
                    DB::getDB()->delete("type_brand", $where);
                    DB::getDB()->delete("type_property", $where);
                    DB::getDB()->delete("type_propertyvalue", $where);
                    DB::getDB()->delete("type_spec", $where);
                    break;
                case 'cat':
                    DB::getDB()->delete("item_cat", $where);
                    break;
                case 'item':
                    DB::getDB()->delete("item_cat", $where);
                    DB::getDB()->delete("item_correlation", $where . " OR fitemid = " . $fieldvalue);
                    DB::getDB()->delete("item_desc", $where);
                    DB::getDB()->delete("item_img", $where);
                    DB::getDB()->delete("user_notify", $where);
                    DB::getDB()->delete("item_property", $where);
                    DB::getDB()->delete("item_spec", $where);
                    DB::getDB()->delete("item_tag", $where);
                    DB::getDB()->delete("gifts", $where . " OR gitemid = " . $fieldvalue);

                    DB::getDB()->delete("discount_item", $where);
                    DB::getDB()->delete("meal_item", $where);
                    DB::getDB()->delete("product", $where);
                    DB::getDB()->delete("product_spec", $where);
                    break;
                case 'user':
                    break;
                case 'express':
                    DB::getDB()->delete("express_opt", $where);
                    break;
                case 'brand':
                    DB::getDB()->delete("type_brand", $where);
                    break;
            }
        }
    }

}
