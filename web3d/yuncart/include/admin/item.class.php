<?php

defined('IN_CART') or die;

/**
 *  
 * 商品
 *
 *
 * */
class Item extends Base
{

    /**
     *  
     * 列出所有商品
     *
     * status 1出售中 2仓库中
     *
     * */
    public function index()
    {
        //参数
        $status = isset($_REQUEST["status"]) ? intval($_REQUEST["status"]) : 1;

        //分页
        list($page, $pagesize) = $this->getRequestPage();

        //类型
        $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
        if ($type == "zero")
            $status = 1; //零库存的

        $this->data['orderby'] = isset($_REQUEST["orderby"]) ? trim($_REQUEST["orderby"]) : 'itemid';
        $this->data["order"] = isset($_REQUEST["order"]) ? trim($_REQUEST["order"]) : 'desc';
        $this->data["orderrev"] = $this->data['order'] == "desc" ? 'asc' : 'desc';
        $orderstr = $this->data['orderby'] . " " . $this->data["order"];

        //零库存的商品
        if ($type == "zero") {
            $join = array("on" => "itemid", "jtype" => "left");
            $where = "a.isdel=0 AND (a.inventory= 0 or b.inventory = 0)";
            $count = DB::getDB()->joincount("item", "product", $join, $where, "itemid", true);
            if ($count) {
                $this->data['pagearr'] = getPageArr($page, $pagesize, $count);

                $tmp = DB::getDB()->join("item", "product", $join, array("a" => "itemid"), $where, array("a" => "order,itemid DESC"), $this->data['pagearr']['limit'], "itemid", true);

                $itemids = array_keys($tmp);
                $this->data['items'] = DB::getDB()->select("item", "*", "itemid in " . cimplode($itemids), "order,itemid DESC");
            }
        } else {
            //搜索
            $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
            $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";
            $this->data['q'] = $q;
            $this->data['type'] = $type;

            $where['isdel'] = 0;
            $where['status'] = $status;
            if ($q) {
                is_numeric($q) && ($where['itemid'] = $q) || ($where['itemname'] = "like '%" . $q . "%'");
            }

            if ($do == "import") {
                $this->_import($where, $orderstr);
                exit();
            } else {
                //商品数量
                $count = DB::getDB()->selectcount("item", $where);
                if ($count) {
                    //获取分页参数
                    $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
                    //查询数据
                    $this->data["items"] = DB::getDB()->select("item", "*", $where, $orderstr, $this->data['pagearr']['limit']);
                }
            }
        }
        $this->data['leftcur'] = "item_index_{$status}";
        $dphtml = $status == 2 ? "item_stock" : "item_sale";
        $this->output($dphtml);
    }

    /**
     *  
     * 导出
     *
     * */
    private function _import($where, $orderby)
    {
        $count = DB::getDB()->selectcount("item", $where);
        if (!$count) {
            $this->setHint(__("no_data_import"));
        }
        $items = DB::getDB()->select("item", "itemid,bn,itemname,itemimg,price,inventory,volume,order", $where, $orderby);
        $content = __("itemid") . ","
                . __("itembn") . ","
                . __("itemname") . ","
                . __("price") . ","
                . __("inventory") . ","
                . __("volume") . ","
                . __("bigpic") . ","
                . __("smallpic") . ","
                . CRLF
        ;
        foreach ($items as $item) {
            $content .= $item["itemid"] . ","
                    . $item['bn'] . "\t,"
                    . str_replace(",", " ", $item["itemname"]) . ","
                    . getPrice($item["price"]) . "\t,"
                    . $item["inventory"] . "\t,"
                    . $item["volume"] . "\t,"
                    . $item['itemimg'] . "\t,"
                    . $item['itemimg'] . '_50x50.jpg' . "\t"
                    . CRLF
            ;
        }
        import($content);
    }

    /**
     *  
     * 添加一个商品
     *
     * */
    public function itemadd()
    {

        $this->data["opertype"] = "add";

        //取得类别
        $typeid = isset($_REQUEST["typeid"]) ? intval($_REQUEST["typeid"]) : 0;
        $cur = isset($_REQUEST["cur"]) ? trim($_REQUEST["cur"]) : "base";

        $this->data["cur"] = $cur;

        //取得分类
        $this->data["cats"] = $this->getListCat();

        //商品标签
        $this->data["tags"] = $this->getTags();

        //所有类目
        $this->data["typeopt"] = $this->getTypes($typeid, "option");

        if ($typeid) {

            //该类目对应的属性
            $this->data["properties"] = DB::getDB()->select("type_property", "*", "typeid='$typeid'", "order", null, "propertyid");
            $propertyids = $this->data["properties"] ? array_keys($this->data["properties"]) : array();

            //取出类目对应的属性
            if ($propertyids) {
                $tmpselvals = DB::getDB()->select("type_propertyvalue", "*", "propertyid in " . cimplode($propertyids), array("propertyid", "order"));
                foreach ($tmpselvals as $val) {
                    $this->data["properties"][$val['propertyid']]["selvallist"][$val['valueid']] = $val["propertyvalue"];
                }
            }
            //dump($this->data['properties']);
            //类目对应的规格
            //该类目对象的规格
            $jpara = array("on" => "specid");
            $this->data["specs"] = DB::getDB()->join("spec", "type_spec", $jpara, array("a" => "specid,name,memo,type"), array("b" => "typeid='$typeid'"), array("b" => "order"), null, "specid");
            //规格对应的规格值
            if ($this->data["specs"]) {
                $specvals = DB::getDB()->select("specval", "*", "specid in " . cimplode(array_keys($this->data["specs"])), array("specid", "order"));
                foreach ($specvals as $val) {
                    $this->data["specs"][$val['specid']]["specval"][$val["specvalid"]] = array("name" => $val["name"],
                        "img" => $val["img"],
                        "specvalid" => $val["specvalid"]);
                }
            }

            //类目对应品牌
            $this->data["brandopt"] = $this->getTypeBrands($typeid);
        }


        $this->output("item_oper_add");
    }

    /**
     *  
     * 修改一个商品
     *
     * */
    public function itemedit()
    {
        $itemid = intval($_GET["itemid"]);
        $cur = isset($_REQUEST["cur"]) ? trim($_REQUEST["cur"]) : "base";

        $this->data["opertype"] = "edit";
        $this->data["cur"] = $cur;
        $this->data["itemid"] = $itemid;

        $where = "itemid='$itemid'";
        $typeid = isset($_REQUEST["typeid"]) ? intval($_REQUEST["typeid"]) : 0;

        //查询商品
        $item = DB::getDB()->selectrow("item", "*", $where);
        if ($item) {
            !$typeid && $typeid = $item["typeid"];

            $this->data["item"] = $item;

            //商品描述，图片
            $this->data['desc'] = DB::getDB()->selectrow("item_desc", "*", $where);
            $this->data['imgs'] = DB::getDB()->select("item_img", "*", $where, "order");

            //商品类别
            $this->data["cats"] = $this->getListCat();
            $this->data['itemcats'] = DB::getDB()->selectcol("item_cat", "catid", $where);

            //商品标签
            $this->data["tags"] = $this->getTags();
            $this->data["itemtags"] = DB::getDB()->selectcol("item_tag", "tagid", $where);

            //商品属性
            $this->data["typeopt"] = $this->getTypes($typeid, "option");
            $this->data["properties"] = $typeid ? DB::getDB()->select("type_property", "*", "typeid='$typeid'", "order", null, "propertyid") : array();
            $propertyids = array_keys($this->data["properties"]);
            if ($propertyids) {
                //取出类型对应的属性
                $tmpselvals = DB::getDB()->select("type_propertyvalue", "*", "propertyid in " . cimplode($propertyids), array("propertyid", "order"));

                foreach ($tmpselvals as $val) {
                    $this->data["properties"][$val['propertyid']]["selvallist"][$val['valueid']] = $val["propertyvalue"];
                }
            }
            $this->data['selproperties'] = DB::getDB()->select("item_property", "propertyid,propertyvalueid,self", $where, "", "", "propertyid");

            //商品品牌
            $this->data["brandopt"] = $this->getTypeBrands($typeid, $item['brandid']);

            //相关商品
            $jpara = array("on" => "fitemid,itemid");
            $fields = array("a" => "*", "b" => "itemname,itemimg");
            $this->data["itemrels"] = DB::getDB()->join("item_correlation", "item", $jpara, $fields, array("a" => $where), array("a" => "order"));

            $this->data["itemspec"] = array();
            if ($typeid) {
                //类目对应规格spec
                $this->data["specs"] = DB::getDB()->join("spec", "type_spec", array("on" => "specid"), array("a" => "specid,name,memo,type"), array("b" => "typeid='$typeid'"), array("b" => "order"), null, "specid");
                if ($this->data["specs"]) {
                    $specids = array_keys($this->data["specs"]);
                    foreach ($specids as $specid) {
                        $this->data['itemspec'][$specid] = array();
                        $this->data['itemspec'][$specid]['sel'] = array();
                    }
                    //规格值specval
                    $tmp = DB::getDB()->select("specval", "*", "specid in " . cimplode($specids), "", "", "specvalid");
                    foreach ($tmp as $k => $v) {
                        $this->data['specvals'][$v['specid']][$v['specvalid']] = $v;
                    }
                    //自定义文字
                    $itemspec = DB::getDB()->selectkv("item_spec", "specid", "self", "itemid='$itemid'");
                    foreach ($itemspec as $k => $v) {
                        $this->data["itemspec"][$k] = unserialize($v);
                        $this->data['itemspec'][$k]['sel'] = array_keys($this->data["itemspec"][$k]['text']);
                    }

                    //货品
                    $this->data["products"] = DB::getDB()->select("product", "*", $where, null, null, "productid");
                    $productspecs = DB::getDB()->select("product_spec", "*", $where);
                    foreach ($productspecs as $spec) {
                        $this->data["products"][$spec["productid"]]["specs"][$spec["specid"]] = $spec["specvalid"];
                    }
                }
            }
            $this->data["leftcur"] = "item_index_" . $item["status"];
        }
        $this->output("item_oper_edit");
    }

    public function getspec()
    {
        $specid = intval($_GET["specid"]);
        $where = "specid='$specid'";
        $this->data["spec"] = DB::getDB()->selectrow("spec", "*", $where);
        $this->data["specvals"] = DB::getDB()->select("specval", "*", $where, "order");
        $this->output("item_getspec");
    }

    /**
     *  
     * 保存一个商品
     *
     * */
    public function itemsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $time = time();
        $text = __('item');
        switch ($opertype) {
            case 'edit':
            case 'add':
                //接收参数
                $itemid = intval($_POST["itemid"]);
                $typeid = intval($_POST["typeid"]);

                $itemname = $_POST["itemname"];
                $bn = trim($_POST["bn"]);

                $price = getPrice($_POST["price"], 2, 'int');
                $mkprice = getPrice($_POST["mkprice"], 2, 'int');
                $pnotify = isset($_POST["pnotify"]) ? 1 : 0;
                $comprice = isset($_POST["comprice"]) ? 1 : 0;



                $inventory = intval($_POST["inventory"]);
                $point = intval($_POST["point"]);
                $status = $_POST["status"];
                $catids = empty($_POST["catid"]) ? array() : $_POST['catid'];


                //商品描述
                $itemdesc = trim($_POST["itemdesc"]);
                $pagetitle = trim($_POST["pagetitle"]);
                $pagedesc = trim($_POST["pagedesc"]);
                $pagekeywords = trim($_POST["pagekeywords"]);

                //商品图片
                $tmpimgs = empty($_POST["img"]) ? array() : $_POST["img"];
                $itemimg = empty($tmpimgs) ? "" : current($tmpimgs);

                //商品属性
                $tmpproperties = empty($_POST["property"]) ? array() : $_POST["property"];

                //商品品牌
                $brandid = isset($_POST["brandid"]) ? intval($_POST["brandid"]) : 0;

                //商品标签
                $tagids = empty($_POST["tagid"]) ? array() : $_POST["tagid"];

                //相关商品
                $fitemids = empty($_POST["fitemid"]) ? array() : $_POST["fitemid"];
                $linktypes = empty($_POST["linktype"]) ? array() : $_POST["linktype"];


                //规格
                $pbns = empty($_POST["pbn"]) ? array() : $_POST["pbn"];

                $data = array("itemname" => $itemname, "bn" => $bn,
                    "price" => $price, "mkprice" => $mkprice, "pnotify" => $pnotify, "comprice" => $comprice,
                    "point" => $point, "inventory" => $inventory, "status" => $status,
                    "itemimg" => $itemimg, "typeid" => $typeid, "brandid" => $brandid);

                if ($itemid) {
                    $data['modified'] = $time; //商品的最后修改时间
                    $where = "itemid='$itemid'";
                    //更新商品
                    $ret = DB::getDB()->update("item", $data, $where);

                    //更新商品描述
                    $ret = DB::getDB()->update("item_desc", array("itemdesc" => $itemdesc,
                        "pagetitle" => $pagetitle,
                        "pagekeywords" => $pagekeywords,
                        "pagedesc" => $pagedesc), $where);

                    //更新图片
                    $adddata = array();
                    $order = 0;
                    $dbimgs = DB::getDB()->selectkv("item_img", "imgid", "order", $where);
                    foreach ($tmpimgs as $key => $img) {
                        $order++;
                        if (cstrpos($key, "key")) { //修改
                            $imgid = intval(trim($key, "key_"));
                            if ($dbimgs[$imgid] != $order) { //如果排序不一样，修改
                                DB::getDB()->update("item_img", array("imgpath" => $img, "order" => $order), "imgid='$imgid'");
                            }
                            unset($dbimgs[$imgid]);
                        } else { //增加
                            $adddata[] = array("itemid" => $itemid, "imgpath" => $img, "order" => $order);
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_img", $adddata);
                    if ($dbimgs)
                        DB::getDB()->delete("item_img", "imgid in " . cimplode(array_keys($dbimgs)));


                    //更新商品类别,删除和增加 操作
                    $adddata = array();
                    $dbcats = DB::getDB()->selectcol("item_cat", "catid", $where);
                    foreach ($catids as $catid) {
                        if (($index = array_search($catid, $dbcats)) === false) { //需要增加
                            $adddata[] = array("catid" => $catid, "itemid" => $itemid);
                        } else {
                            unset($dbcats[$index]);
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_cat", $adddata);
                    if ($dbcats)
                        DB::getDB()->delete("item_cat", "catid in " . cimplode($dbcats));

                    //更新属性
                    $dbproperties = DB::getDB()->select("item_property", "*", $where, "", "", "propertyid");
                    $adddata = array();
                    //以提交参数为准，判断需要更新的
                    foreach ($tmpproperties as $propertyid => $propertyvalueid) {
                        if (cstrpos($propertyid, "self_")) {//如果是自己手动输入
                            $propertyid = intval(trim($propertyid, "self_")); //求得propertyid
                            if (isset($dbproperties[$propertyid])) {//如果库中已经存在该数据,且数据不等
                                if ($propertyvalueid != $dbproperties[$propertyid]['self']) {
                                    DB::getDB()->update("item_property", "propertyvalueid=0,self='$propertyvalueid'", "itemid='$itemid' AND propertyid='$propertyid'");
                                }
                                unset($dbproperties[$propertyid]);
                            } else {
                                $adddata[] = array("itemid" => $itemid, "propertyid" => $propertyid, "propertyvalueid" => 0, "self" => $propertyvalueid);
                            }
                        } else {
                            if (isset($dbproperties[$propertyid])) { //如果propertyid存在
                                if ($propertyvalueid != $dbproperties[$propertyid]['propertyvalueid']) { //值不一样
                                    DB::getDB()->update("item_property", "propertyvalueid='$propertyvalueid',self=''", "itemid='$itemid' AND propertyid='$propertyid'");
                                }
                                unset($dbproperties[$propertyid]);
                            } else { //需要增加
                                $adddata[] = array("itemid" => $itemid, "propertyid" => $propertyid, "propertyvalueid" => $propertyvalueid, "self" => '');
                            }
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_property", $adddata);

                    //商品标签
                    $dbtags = DB::getDB()->selectcol("item_tag", "tagid", "itemid='$itemid'");
                    $adddata = array();
                    if ($tagids) {
                        foreach ($tagids as $tagid) {
                            if (($index = array_search($tagid, $dbtags)) === false) { //需要增加
                                $adddata[$tagid] = array("tagid" => $tagid, "itemid" => $itemid);
                            } else { //
                                unset($dbtags[$index]);
                            }
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_tag", $adddata);
                    if ($dbtags) { //删除标签
                        $tagstr = "tagid in" . cimplode($dbtags);
                        DB::getDB()->delete("item_tag", "itemid='$itemid' AND $tagstr");
                        DB::getDB()->updatecre("tag", "num", $tagstr, "decre");
                    }

                    //相关商品
                    $replacedata = array();
                    $dbfitemids = DB::getDB()->selectkv("item_correlation", "fitemid", "itemid", "itemid='$itemid'");
                    $order = 0;
                    foreach ($fitemids as $key => $fitemid) {
                        $order ++;
                        if (isset($dbfitemids[$fitemid]))
                            unset($dbfitemids[$fitemid]);
                        $linktype = intval($linktypes[$key]);
                        $replacedata[] = array("itemid" => $itemid, "fitemid" => $fitemid, "linktype" => $linktype, "order" => $order);
                        if ($linktype == 2) {
                            $replacedata[] = array("itemid" => $fitemid, "fitemid" => $itemid, "linktype" => $linktype, "order" => $order);
                        } else {
                            DB::getDB()->delete("item_correlation", "itemid='$fitemid' AND fitemid='$itemid'");
                        }
                    }
                    if ($dbfitemids) { //删除没有关系的
                        DB::getDB()->delete("item_correlation", "itemid='$itemid' AND fitemid in " . cimplode(array_keys($dbfitemids)));
                        DB::getDB()->delete("item_correlation", "itemid in " . cimplode(array_keys($dbfitemids)) . " AND fitemid='$itemid'");
                    }
                    if ($replacedata)
                        DB::getDB()->replaceMulti("item_correlation", $replacedata);

                    //商品规格
                    $products = DB::getDB()->select("product", "productid", "itemid=$itemid", null, null, "productid");
                    $specids = array();
                    if ($pbns) {
                        $pinventorys = $_POST["pinventory"];
                        $pprices = $_POST["pprice"];
                        $specs = $_POST["spec"];
                        $specids = array_keys($specs);
                        $replacedata = array();
                        foreach ($pbns as $k => $pbn) {
                            $data = array("bn" => $pbn, "inventory" => $pinventorys[$k], "price" => floatval($pprices[$k]) * 100,
                                "itemid" => $itemid);
                            if (cstrpos($k, "key_")) { //如果是更新
                                $productid = trim($k, "key_");
                                unset($products[$productid]);
                                DB::getDB()->update("product", $data, "productid='$productid'");
                                foreach ($specids as $specid) {
                                    $replacedata[] = array("productid" => $productid,
                                        "specid" => $specid,
                                        "specvalid" => $specs[$specid][$k],
                                        "itemid" => $itemid,
                                        "typeid" => $typeid);
                                }
                            } else { //增加
                                $productid = DB::getDB()->insert("product", $data);
                                foreach ($specids as $specid) {
                                    $replacedata[] = array("productid" => $productid,
                                        "specid" => $specid,
                                        "specvalid" => $specs[$specid][$k],
                                        "itemid" => $itemid,
                                        "typeid" => $typeid);
                                }
                            }
                        }
                        if ($replacedata)
                            DB::getDB()->replaceMulti("product_spec", $replacedata);
                    }
                    //删除
                    if ($products) {
                        $where = "productid in " . cimplode(array_keys($products));
                        DB::getDB()->delete("product", $where);
                        DB::getDB()->delete("product_spec", $where);
                    }
                    //自定义名称
                    $self = empty($_POST["self"]) ? array() : $_POST["self"];
                    $selfpic = empty($_POST["selfpic"]) ? array() : $_POST["selfpic"];
                    $dbself = DB::getDB()->selectkv("item_spec", "specid", "itemid", "itemid='$itemid'");
                    $adddata = array();
                    if ($self) {
                        foreach ($self as $key => $val) {
                            $seri_self = array();
                            if (isset($selfpic[$key])) {
                                $seri_self = serialize(array("text" => $val, "pic" => $selfpic[$key]));
                            } else {
                                $seri_self = serialize(array("text" => $val));
                            }
                            if (isset($dbself[$key])) {
                                DB::getDB()->update("item_spec", array("self" => $seri_self), "itemid='$itemid' AND specid = '$key'");
                                unset($dbself[$key]);
                            } else {
                                $adddata[] = array("itemid" => $itemid, "specid" => $key, "self" => $seri_self);
                            }
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_spec", $adddata);
                    if ($dbself)
                        DB::getDB()->delete("item_spec", "itemid=$itemid AND specid in " . cimplode(array_keys($dbself)));
                    $this->adminlog("al_item", array("do" => "edit", "itemname" => $itemname));
                    $this->setHint(__("edit_success", $text), "item_index_" . $status);
                } else {
                    $data['created'] = $data['modified'] = $time; //商品的最后修改时间 = 商品的上传时间，
                    $data['order'] = 50;
                    //添加商品
                    $itemid = DB::getDB()->insert("item", $data);

                    //添加商品描述
                    $ret = DB::getDB()->insert("item_desc", array("itemid" => $itemid,
                        "itemdesc" => $itemdesc,
                        "pagekeywords" => $pagekeywords,
                        "pagedesc" => $pagedesc,
                        "pagetitle" => $pagetitle));

                    //添加商品图片
                    $adddata = array();
                    foreach ($tmpimgs as $k => $img) {
                        $adddata[] = array("itemid" => $itemid, "imgpath" => $img, "order" => $k + 1);
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_img", $adddata);

                    //添加商品类别
                    $adddata = array();
                    foreach ($catids as $catid) {
                        $adddata[] = array("itemid" => $itemid, "catid" => $catid);
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_cat", $adddata);


                    //添加商品属性
                    $adddata = array();
                    foreach ($tmpproperties as $propertyid => $propertyvalue) {
                        if (cstrpos($propertyid, "self_")) {
                            $adddata[] = array("itemid" => $itemid,
                                "propertyid" => intval(trim($propertyid, "self_")), "propertyvalueid" => 0, "self" => $propertyvalue);
                        } else {
                            $adddata[] = array("itemid" => $itemid, "propertyid" => $propertyid, "propertyvalueid" => $propertyvalue, "self" => '');
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("item_property", $adddata);

                    //商品标签
                    $adddata = array();
                    foreach ($tagids as $tagid) {
                        $adddata[$tagid] = array("tagid" => $tagid, "itemid" => $itemid);
                    }
                    if ($adddata) {
                        DB::getDB()->insertMulti("item_tag", $adddata);
                    }

                    //相关商品
                    $replacedata = array();
                    $order = 0;
                    foreach ($fitemids as $key => $fitemid) {
                        $order ++;
                        $linktype = intval($linktypes[$key]);
                        $replacedata[] = array("itemid" => $itemid, "fitemid" => $fitemid, "linktype" => $linktype, "order" => $order);
                        if ($linktype == 2) {
                            $replacedata[] = array("fitemid" => $itemid, "itemid" => $fitemid, "linktype" => $linktype, "order" => $order);
                        }
                    }
                    if ($replacedata)
                        DB::getDB()->replaceMulti("item_correlation", $replacedata);

                    //规格
                    if ($pbns) {
                        //添加货品记录
                        $pinventorys = $_POST["pinventory"];
                        $pprices = $_POST["pprice"];
                        $specs = $_POST["spec"];
                        $specids = array_keys($specs);
                        $adddata = array();
                        foreach ($pbns as $k => $pbn) {
                            //添加一个货品
                            $productid = DB::getDB()->insert("product", array("bn" => $pbn,
                                "inventory" => $pinventorys[$k],
                                "price" => getPrice($pprices[$k], 2, 'int'),
                                "itemid" => $itemid));
                            //货品对应的规格
                            foreach ($specids as $specid) {
                                $adddata[] = array("specid" => $specid,
                                    "specvalid" => $specs[$specid][$k],
                                    "productid" => $productid,
                                    "itemid" => $itemid,
                                    "typeid" => $typeid);
                            }
                        }
                        if ($adddata)
                            DB::getDB()->insertMulti("product_spec", $adddata);

                        //处理用户自定义的规格
                        $self = $_POST["self"];
                        $selfpic = $_POST["selfpic"];
                        $adddata = array();
                        if ($specids) {
                            foreach ($specids as $specid) {
                                //如果存在图片，记录用户自定义的图片，
                                $seri_self = isset($selfpic[$specid]) ? serialize(array("text" => $self[$specid], "pic" => $selfpic[$specid])) : serialize(array("text" => $self[$specid]));

                                $adddata[] = array("itemid" => $itemid, "specid" => $specid, "self" => $seri_self);
                            }
                        }
                        if ($adddata)
                            DB::getDB()->insertMulti("item_spec", $adddata);
                    }
                    $this->adminlog("al_item", array("do" => "add", "itemname" => $itemname));
                    $this->setHint(__("add_success", $text), "item_index_" . $status);
                }
                break;
            case 'editfield':   //修改特定字段
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "list" || $field == "delist" || $field == "remove") {//上架，下架，回收站
                    $itemidstr = trim($_POST["idstr"]);
                    $ret = false;
                    if ($itemidstr) {
                        $itemids = explode(",", $itemidstr);
                        $where = "itemid in " . cimplode($itemids);

                        $titles = DB::getDB()->selectkv("item", "itemid", "itemname", $where);
                        //下面应该交换变成titles
                        foreach ($titles as $itemname) {
                            $this->adminlog("al_item", array("do" => $field, "itemname" => $itemname));
                        }
                        if ($field == "list") {
                            $ret = DB::getDB()->update("item", "`status`=1,modified='$time'", $where);
                        } else if ($field == "delist") {
                            $ret = DB::getDB()->update("item", "`status`=2,modified='$time'", $where);
                        } else if ($field == "remove") {
                            $ret = DB::getDB()->update("item", "isdel=1,modified='$time'", $where);
                            $recycledata = array();
                            $table = array("table" => "item", "type" => "item", "tablefield" => "itemid", "addtime" => time());
                            foreach ($titles as $itemid => $itemname) {
                                $recycledata[] = $table + array("tableid" => $itemid, "title" => $itemname);
                            }
                            DB::getDB()->insertMulti("recycle", $recycledata);
                        }
                    }
                } else {
                    !in_array($field, array("itemname", "order")) && exit("failure");
                    $itemid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_item", array("do" => "edit", "itemid" => $itemid));
                    $ret = DB::getDB()->update("item", array($field => $value, 'modified' => $time), "itemid='$itemid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
