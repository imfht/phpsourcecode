<?php

defined('IN_CART') or die;

/**
 *  
 * 商品列表
 *
 *
 * */
class Listing extends Base
{

    /**
     *  
     * 商品列表页
     *
     *
     * */
    public function index()
    {
        //接受参数
        //商品类别
        $catid = isset($_GET["catid"]) ? intval($_GET["catid"]) : 0;

        $where = "catid='$catid'";
        $this->data["thecat"] = DB::getDB()->selectrow("cat", "*", $where);

        if (!$this->data["thecat"]) {
            cerror(__("access_error"));
        }
        $this->data["catid"] = $catid;
        $wherea = "isdel=0 AND status=1"; //item过滤 未删除，状态为1
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        //排序
        $orderby = isset($_GET["orderby"]) && in_array($_GET['orderby'], array('itemid', 'price', 'view')) ? trim($_GET['orderby']) : "itemid";
        $orderdesc = isset($_GET['orderdesc']) && in_array($_GET['orderdesc'], array('desc', 'asc')) ? trim($_GET['orderdesc']) : 'desc';
        $orderstr = $orderby . " " . $orderdesc;
        $this->data['orderby'] = $orderby;
        $this->data['orderdesc'] = $orderdesc;

        //显示 图文，列表
        $this->data['show'] = isset($_GET['show']) ? intval($_GET['show']) : 0;

        //价格
        $price1 = isset($_GET['price1']) ? getPrice($_GET['price1'], 2, 'int') : 0;
        $price2 = isset($_GET['price2']) ? getPrice($_GET['price2'], 2, 'int') : 0;
        $this->data['price1'] = $price1 / 100;
        $this->data['price2'] = $price2 / 100;
        if ($price1)
            $wherea .= " AND a.price > '$price1'";
        if ($price2)
            $wherea .= " AND a.price < '$price2'";


        //品牌
        $brandid = isset($_GET['brandid']) ? intval($_GET['brandid']) : 0;
        $this->data['brandid'] = $brandid;
        if ($brandid)
            $wherea .= " AND a.brandid='$brandid'";


        //类别
        $this->data["cats"] = $this->getCats();
        $this->data["catlevel"] = $this->getCatLevelUp($catid);
        //该分类下面的一级子类别
        $catdown = $this->getCatLevelDown($catid);
        if (isset($catdown[$catid]['children'])) {
            $this->data['childcat'] = $catdown[$catid]['children'];
        }

        $catdownlist = $this->getCatList($catdown);


        $catids = array_keys($catdownlist);
        $whereb = "catid in " . cimplode($catids);


        $this->data['path'] = '';
        //类目
        $typeid = $this->data["thecat"]["typeid"];
        if ($typeid) {
            //该类目对应的品牌
            $this->data['brands'] = DB::getDB()->join("brand", "type_brand", array("on" => "brandid"), array("a" => "brandid,brandname"), array("a" => "isdel=0", "b" => "typeid='$typeid'"), array("a" => "order"));

            //该类目对应的规格
            $this->getTypeProperty($typeid);

            //属性
            $path = !empty($_GET['path']) ? preg_replace("/[^\d:,]/", "", $_GET["path"]) : '';
            $this->data['path'] = $path;
            $this->data['selpro'] = array();
            if ($this->data['path']) {
                $tmp = explode(",", $path);
                foreach ($tmp as $v) {
                    if (!cstrpos($v, ":"))
                        continue;
                    $tmpv = explode(":", $v);
                    $this->data['selpro'][intval($tmpv[0])] = intval($tmpv[1]);
                }
            }
            //属性过滤
            if ($this->data['selpro']) {
                $sql = "SELECT count(1) as countval,itemid FROM "
                        . DB::getDB()->getTableName('item_property')
                        . " WHERE propertyvalueid in " . cimplode($this->data['selpro'])
                        . " GROUP BY itemid"
                        . " HAVING countval = " . count($this->data['selpro'])
                ;
                $initemids = DB::getDB()->selectsql($sql, "col", "itemid");
                if (!$initemids) { //如果过滤后无结果，直接输入页面
                    $this->output("list");
                } else { //过滤后有结果，增加查询条件
                    $wherea .= " AND a.itemid in " . cimplode($initemids);
                }
            }
        }


        //参数
        $jpara = array("on" => "itemid");
        $where = array("a" => $wherea, "b" => $whereb);
        $count = DB::getDB()->joincount("item", "item_cat", $jpara, $where, "itemid", true);
        //echo DB::getDB()->getLastSql();
        if ($count) {
            $url = url('index', 'listing', 'index', "catid=$catid&brandid=$brandid&path=" . $this->data['path'] . "&orderby=view&
						orderdesc=desc&price1=$price1&price2=$price2&show=" . $this->data['show']);
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, $url);
            $fields = array("a" => "itemid,itemname,itemimg,price,bn");
            $this->data["items"] = DB::getDB()->join("item", "item_cat", $jpara, $fields, $where, $orderstr, $this->data["pagearr"]['limit'], "", true);
        }

        $this->output("list");
    }

    /**
     *  
     * 获取类目对应的属性
     *
     *
     * */
    private function getTypeProperty($typeid)
    {
        $type_property = DB::getDB()->join("type_property", "type_propertyvalue", array("on" => "propertyid"), array("a" => "propertyid,propertyname", "b" => "valueid,propertyvalue"), array("a" => "typeid='$typeid' AND isdp=1 AND dptype=1"), array("a" => "order", "b" => "order"));
        $this->data["properties"] = array();
        if (!$type_property)
            return;

        foreach ($type_property as $k => $v) {
            if (!isset($this->data["properties"][$v['propertyid']])) {
                $this->data["properties"][$v["propertyid"]] = array(
                    "propertyname" => $v["propertyname"],
                    "propertyid" => $v["propertyid"]
                );
            }
            $this->data["properties"][$v["propertyid"]]['value'][$v['valueid']] = array(
                "valueid" => $v["valueid"],
                "propertyvalue" => $v["propertyvalue"]
            );
        }
        //print_R($this->data["properties"]);
    }

}

//		if($catid) {
//			$data['catid']    = $catid;
//			//$data['catlevel'] = array_reverse(getCatLevel($catid),true);
//			$data['curcat']	  = DB::getDB()->selectrow("cat","*","catid=$catid");
//			!$data['curcat'] && hint("cat_not_exist","","onlymsg");
//			//类目
//			$typeid			= $data['curcat']['typeid'];
//			$data['specs']	= array();
//			if($typeid) {
//				$data['specs']	= isset($_type[$typeid]['spec']) ? $_type[$typeid]['spec'] :array();
//				foreach($data['specs'] as $k => $v) {
//					if($v['dptype'] != 1) unset($data['specs'][$k]);
//				}
//				//品牌
//				$data['brands'] = DB::getDB()->join("type_brand","brand",array("on"=>"brandid"),array("b"=>"brandid,brandname"),
//											array("a"=>"typeid=$typeid","b"=>"isdel=0"),array("b"=>"order"));
//				//过滤
//				$initemids = array();
//				if($data['specsel']) {
//					$sql = "SELECT count(1) as countval,itemid FROM "
//						 . DB::getDB()->getTableName('item_property')
//						 . " WHERE propertyvalueid in ".cimplode($data['specsel'])
//						 . " GROUP BY itemid"
//						 . " HAVING countval = ".count($data['specsel'])
//						 ;
//					$initemids = DB::getDB()->selectsql($sql,"col","itemid");
//				}
//				$wherea = "catid=$catid";
//			} else {
//				$data['filtercats'] = DB::getDB()->select("cat","*","pid=$catid","order","","catid");
//				$wherea = "catid in ".cimplode(array_keys($data['filtercats']));
//			}
//			//商品
//			$jpara = array("on"=>"itemid");
//			$whereb[] = "isdel=0";
//			if($price1) $whereb[] = "price> $price1";
//			if($price2) $whereb[] = "price< $price2";
//			
//			$brandid		 && $whereb[] = "brandid=$brandid";
//			$data['specsel'] && $whereb[] = "b.itemid in " . cimplode($initemids);
//			$where = array("a"=>$wherea,"b"=>implode(' AND ',$whereb));
//			
//			$count = DB::getDB()->joincount("item_cat","item",$jpara,$where);
//			
//			if($count) {
//				//获取分页参数
//				$data["pagearr"] = getPageArr($page,$pagesize,$count);
//				$fields			 = array("b"=>"itemid,itemname,itemimg,price,bn");
//				$data['items']	 = DB::getDB()->join("item_cat","item",$jpara,$fields,$where,array("b"=>$orderstr),
//											$data['pagearr']['limit'],"itemid");
//			}
//		} 
