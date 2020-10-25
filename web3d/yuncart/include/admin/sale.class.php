<?php

defined('IN_CART') or die;

/**
 *  
 *  销售统计
 *
 */
class Sale extends Base
{

    /**
     *  
     *  总览
     *
     */
    public function index()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where = array();
        $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $qtype = isset($_REQUEST['qtype']) ? trim($_REQUEST['qtype']) : "trade";
        $time1 = isset($_REQUEST['time1']) ? strtotime(trim($_REQUEST['time1'])) : "";
        $time2 = isset($_REQUEST['time2']) ? strtotime(trim($_REQUEST['time2'])) : "";
        $this->data += array('q' => $q, "qtype" => $qtype, "time1" => $time1, "time2" => $time2);

        //排序
        $this->data['orderby'] = isset($_REQUEST["orderby"]) ? trim($_REQUEST["orderby"]) : 'saleid';
        $this->data["order"] = isset($_REQUEST["order"]) ? trim($_REQUEST["order"]) : 'desc';
        $this->data["orderrev"] = $this->data['order'] == "desc" ? 'asc' : 'desc';
        $orderstr = $this->data['orderby'] . " " . $this->data["order"];

        if ($q) {
            if ($qtype == "trade") { //订单
                $where[] = "tradeid='" . $q . "'";
            } elseif ($qtype == "item") {//商品
                is_numeric($q) && ($where[] = "itemid='" . $q . "'") || ($where[] = "(itemname like '%" . $q . "%' or ibn like '%" . $q . "%')");
            } elseif ($qtype == "product") {//货品
                $where[] = "pbn like '%" . $q . "%'";
            } elseif ($qtype == "user") { //用户
                is_numeric($q) && ($where[] = "uid='" . $q . "'") || ($where[] = "uname like '%" . $q . "%'");
            }
        }
        $time1 && $where[] = "saletime > '$time1'";
        $time2 && $where[] = "saletime < '$time2'";

        $wherestr = implode(' AND ', $where);

        if ($do == "import") { //如果为导出
            $this->_import($wherestr);
        } else {
            //数量
            $count = DB::getDB()->selectcount("sales", $wherestr);
            if ($count) {
                //分页
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

                //查询
                $this->data["sales"] = DB::getDB()->select("sales", "*", $wherestr, $orderstr, $this->data['pagearr']['limit']);
            }
            $this->output("sale_index");
        }
    }

    /**
     *  
     *  导出
     *
     */
    private function _import($wherestr)
    {
        $count = DB::getDB()->selectcount("sales", $wherestr);
        if (!$count) {
            $this->setHint(__("no_data_import"));
        }
        $sales = DB::getDB()->select("sales", "*", $wherestr, "saleid DESC");
        $content = __("trade") . ","
                . __("itemid") . ","
                . __("itemname") . ","
                . __("smallpic") . ","
                . __("itembn") . ","
                . __("productbn") . ","
                . __("order_num") . ","
                . __("price") . ","
                . __("uid") . ","
                . __("uname") . ","
                . __("orderbegin") . ","
                . __("orderend")
                . CRLF;
        foreach ($sales as $sale) {
            $content .= $sale['tradeid'] . "\t,"
                    . $sale['itemid'] . ","
                    . str_replace(array(",", "&nbsp;"), " ", $sale['itemname']) . ","
                    . $sale['itemimg'] . "_50x50.jpg" . "\t,"
                    . str_replace(",", "", $sale['ibn']) . "\t,"
                    . str_replace(",", "", $sale['pbn']) . "\t,"
                    . $sale['num'] . ","
                    . getPrice($sale['price']) . "\t,"
                    . $sale['uid'] . ','
                    . $sale['uname'] . ','
                    . date('m-d', $sale['saletime']) . ','
                    . date('m-d', $sale['finishtime'])
                    . CRLF;
        }
        import($content);
    }

    /**
     *  
     *  销售指标
     *
     */
    public function itemview()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        //排序
        $this->data['orderby'] = isset($_REQUEST["orderby"]) ? trim($_REQUEST["orderby"]) : 'view';
        $this->data["order"] = isset($_REQUEST["order"]) ? trim($_REQUEST["order"]) : 'desc';
        $this->data["orderrev"] = $this->data['order'] == "desc" ? 'asc' : 'desc';
        $orderstr = $this->data['orderby'] . " " . $this->data["order"];

        $where = "isdel=0 AND view>1";
        $count = DB::getDB()->selectcount("item", $where);
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            //商品查看列表
            $this->data['items'] = DB::getDB()->select("item", "itemid,bn,itemname,itemimg,price,inventory,view,volume", $where, $orderstr, $this->data['pagearr']['limit']);
        }
        $this->output("sale_itemview");
    }

}
