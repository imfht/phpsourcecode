<?php

defined('IN_CART') or die;

/**
 *
 * 选择商品，赠品 
 * 
 */
class Select extends Base
{

    /**
     *
     * 选择商品 
     * 
     */
    public function selectitem()
    {
        list($page, $pagesize) = $this->getRequestPage(5);

        $q = isset($_REQUEST['q']) ? trim(strip_tags($_REQUEST['q'])) : '';
        $inajax = isset($_REQUEST["inajax"]) ? true : false;
        $where = array("isdel" => 0, "status" => 1);
        $q && $where['itemname'] = "like '%" . $q . "%'";

        //type
        $type = trim($_REQUEST["type"]);
        !in_array($type, array("select", "gift", "iframe", "rel")) && $type = 'select';

        $count = DB::getDB()->selectcount("item", $where);
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["items"] = DB::getDB()->select("item", "itemid,itemname,itemimg,price,order,inventory,volume", $where, "order", $this->data['pagearr']['limit']);
        }
        $this->data['q'] = $q;
        $this->data['type'] = $type;
        $html = $type . "_item";
        $inajax && ($html .= "_list");
        $this->output($html);
    }

}
