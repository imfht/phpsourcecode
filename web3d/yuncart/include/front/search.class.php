<?php

/**
 *  
 * 搜索
 *
 *
 * */
class Search extends Base
{

    /**
     *  
     * 搜索列表
     *
     *
     * */
    public function index()
    {
        //关键词
        $q = safehtml(trim($_GET['q']));
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where = "isdel=0 AND status=1"; //item过滤 未删除，状态为1
        //排序
        $orderby = isset($_GET["orderby"]) && in_array($_GET['orderby'], array('itemid', 'price', 'view')) ? trim($_GET['orderby']) : "itemid";
        $orderdesc = isset($_GET['orderdesc']) && in_array($_GET['orderdesc'], array('desc', 'asc')) ? trim($_GET['orderdesc']) : 'desc';
        $orderstr = $orderby . " " . $orderdesc;
        $this->data['orderby'] = $orderby;
        $this->data['orderdesc'] = $orderdesc;

        //显示
        $this->data['show'] = isset($_GET['show']) ? intval($_GET['show']) : 0;
        //价格
        $price1 = isset($_GET['price1']) ? getPrice($_GET['price1'], 2, 'int') : 0;
        $price2 = isset($_GET['price2']) ? getPrice($_GET['price2'], 2, 'int') : 0;
        $this->data['price1'] = $price1 / 100;
        $this->data['price2'] = $price2 / 100;
        if ($price1)
            $where .= " AND price > '$price1'";
        if ($price2)
            $where .= " AND price < '$price2'";


        //类别
        $this->data["cats"] = $this->getCats();
        $this->data['q'] = safehtml(stripslashes($q));

        if ($q)
            $where .= " AND itemname like '%$q%'";
        $count = DB::getDB()->selectcount("item", $where);
        if ($count) {
            $url = url('index', 'search', 'index', "orderby=view&orderdesc=desc&price1=$price1&price2=$price2&show=" . $this->data['show'] . "&q=$q");
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, $url);
            $this->data["items"] = DB::getDB()->select("item", "itemid,itemname,itemimg,price,bn", $where, $orderstr, $this->data['pagearr']['limit'], "itemid");
        }
        $this->output("search");
    }

}
