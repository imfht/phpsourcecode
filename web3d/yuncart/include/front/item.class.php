<?php

defined('IN_CART') or die;

/**
 *  
 * 商品详情
 *
 *
 * */
class Item extends Base
{

    /**
     *  
     * 商品详情页
     *
     *
     * */
    public function index()
    {
        $itemid = !empty($_GET["itemid"]) ? intval($_GET["itemid"]) : 0;
        $time = time();
        $this->data['time'] = $time;

        //item信息
        $where = "itemid='$itemid'";
        $this->data["itemid"] = $itemid;
        $this->data["item"] = DB::getDB()->selectrow("item", "*", "$where AND isdel = 0");
        if (!$this->data["item"])
            cerror(__("item_not_exist"));


        //图片
        $this->data["itemimgs"] = DB::getDB()->select("item_img", "*", $where, "order");

        //描述
        $this->data["itemdesc"] = DB::getDB()->selectrow("item_desc", "*", $where);

        //类别
        $this->data["cats"] = $this->getCats();


        //更新商品view
        DB::getDB()->updatecre("item", "view", $where);



        //httphost;
        $this->data['weburl'] = getConfig('weburl');

        //属性
        $this->getItemProperty($itemid);

        //相关商品
        $this->data['relations'] = DB::getDB()->join("item_correlation", "item", array("on" => "fitemid,itemid"), array("a" => "fitemid", "b" => "itemname,itemimg,price"), array("a" => $where, "b" => "isdel=0"), array("a" => "order"));

        //浏览历史
        $this->getHistoryList($itemid);

        //满就送活动
        $this->data['man'] = Promotion::getMan();

        //套餐
        $this->data['meal'] = Promotion::getMeal($itemid);
        unset($this->data['meal']['items'][$itemid]);


        //是否处于限时折扣中
        $this->data['tuan'] = false;
        $this->data['dis'] = Promotion::itemInDiscount($itemid);
        if (!$this->data['dis']) {//如果不在限时打折
            $this->data['tuan'] = Promotion::itemInTuan($itemid);
        }

        //赠品
        $this->data['gifts'] = Promotion::getGifts($itemid);

        //商品分享
        $share = getConfig("share");
        $search = array("{mallname}", "{itemname}", "{itemurl}", "{itemprice}");
        $replacement = array(getConfig("mallname"), $this->data["item"]["itemname"],
            getConfig("weburl") . "?" . $this->data["item"]["itemid"],
            getPrice($this->data["item"]["price"]));
        $this->data["share"] = $share ? str_replace($search, $replacement, $share) : '';

        //商品的规格
        $this->getItemSpec($itemid, $this->data['item']['typeid']);

        $this->output("item");
    }

    private function getItemSpec($itemid = 0, $typeid = 0)
    {
        if (!$typeid || !DB::getDB()->selectexist("type", "typeid='$typeid' AND isdel=0") || !DB::getDB()->selectexist("type_spec", "typeid='$typeid'")) {
            //计算价格
            if ($this->data['dis']) {
                $this->data['pricestr'] = getPrice($this->data['item']['price'] * $this->data['dis']['discount'], -5);
            } else if ($this->data['tuan']) {
                $this->data['pricestr'] = getPrice($this->data['tuan']['price']);
            } else {
                $this->data['pricestr'] = getPrice($this->data['item']['price']);
            }
            return;
        }

        $specs = DB::getDB()->join("spec", "type_spec", array("on" => "specid"), array("a" => "specid,name,memo,type"), array("b" => "typeid='$typeid'"), array("b" => "order"), null, "specid");
        if (!$specs)
            return;
        $specvals = DB::getDB()->select("specval", "*", "specid in " . cimplode(array_keys($specs)), "", "", "specvalid");


        $where = "itemid='$itemid'";
        //商品自定义的规格
        $itemspecs = DB::getDB()->selectkv("item_spec", "specid", "self", $where);

        foreach ($itemspecs as $k => $v) {//反序列化
            if (!$v)
                continue;
            $itemspecs[$k] = @unserialize($v);
            $itemspecs[$k]['sel'] = is_array($itemspecs[$k]['text']) ? array_keys($itemspecs[$k]['text']) : array();
        }

        //货品
        $products = DB::getDB()->select("product", "*", $where, null, null, "productid");
        $productspecs = DB::getDB()->select("product_spec", "*", $where);

        !$products && $products = array();
        if ($this->data['tuan']) {
            $this->data["pricestr"] = getPrice($this->data["tuan"]["price"]);
        } else {
            //计算价格
            $minprice = $maxprice = $this->data['item']['price'];
            foreach ($products as $key => $product) {
                $price = $product['price'];
                if ($price < $minprice)
                    $minprice = $price;
                if ($price > $maxprice)
                    $maxprice = $price;
                $products[$key]['price'] = $this->data['dis'] ? getPrice($price * $this->data['dis']['discount'], -5) : getPrice($price);
                ;
            }
            $minprice = $this->data['dis'] ? getPrice($minprice * $this->data['dis']['discount'], -5) : getPrice($minprice);
            $maxprice = $this->data['dis'] ? getPrice($maxprice * $this->data['dis']['discount'], -5) : getPrice($maxprice);
            if ($minprice != $maxprice) {
                $this->data['pricestr'] = $minprice . '---￥' . $maxprice;
            } else {
                $this->data['pricestr'] = $minprice;
            }
        }


        $thespecs = array();
        foreach ($productspecs as $spec) {
            $thespecs[$spec['specid']][] = $spec['specvalid'];
            $products[$spec['productid']]['spec'][$spec['specid']] = $spec['specvalid'];
        }
        $this->data["specs"] = $specs;
        $this->data["thespecs"] = $thespecs;
        $this->data["itemspecs"] = $itemspecs;
        $this->data['productstr'] = json_encode($products);
    }

    //商品属性
    private function getItemProperty($itemid)
    {
        $ret = array();

        $properties = DB::getDB()->select("item_property", "*", "itemid='$itemid'", "", "", "propertyid");
        if (!$properties)
            return $ret;
        $propertyids = $propertyvalueids = array();

        foreach ($properties as $key => $val) {
            $propertyids[] = $key;
            $propertyvalueids[] = $val['propertyvalueid'];
        }

        $t_keys = DB::getDB()->selectkv("type_property", "propertyid", "propertyname", "propertyid in " . cimplode($propertyids));
        $t_vals = DB::getDB()->selectkv("type_propertyvalue", "valueid", "propertyvalue", "valueid in " . cimplode($propertyvalueids));


        foreach ($properties as $key => $val) {
            if (!isset($t_keys[$key]) || (!isset($t_vals[$val['propertyvalueid']]) && !$val['self'] ))
                continue;
            $ret[] = array("propertyid" => $key,
                "valueid" => $val,
                "propertyname" => $t_keys[$key],
                "propertyvalue" => !empty($t_vals[$val['propertyvalueid']]) ? $t_vals[$val['propertyvalueid']] : $val['self']
            );
        }
        $this->data['itemproperty'] = $ret;
    }

    //历史记录
    private function getHistoryList($theitemid)
    {
        $cookie = cgetcookie("history");
        $history = !empty($cookie) ? array_unique(explode(",", trim($cookie, ","))) : array();
        if (count($history) > 5)
            $history = array_slice($history, 0, 5);
        $itemids = array();
        foreach ($history as $k => $v) {
            if (!$v || ($v == $theitemid))
                continue;
            $itemids[$v] = intval($v);
        }
        if ($itemids) {
            $items = DB::getDB()->select("item", "itemid,itemimg,price,itemname", "itemid in " . cimplode($itemids), null, null, "itemid");
            foreach ($itemids as $itemid) {
                if (isset($items[$itemid])) {
                    $this->data['historylist'][$itemid] = $items[$itemid];
                }
            }
        }
        $cookie = $history ? "$theitemid," . implode(",", $history) : "$theitemid";
        csetcookie("history", trim($cookie, ","), time() + 604800);
    }

    /**
     *  
     * 商品大图
     *
     *
     * */
    public function bigimg()
    {
        $itemid = intval($_GET["itemid"]);
        $where = "itemid='$itemid'";

        $this->data["item"] = DB::getDB()->selectrow("item", "itemid,itemname", $where); //商品信息
        if (!$this->data["item"])
            cerror(__("item_not_exist"));


        $this->data["itemimgs"] = DB::getDB()->select("item_img", "*", $where); //商品图片
        if (!$this->data["itemimgs"])
            cerror(__("itemimg_not_exist"));

        $this->data['curimg'] = $this->data['itemimgs'][0]['imgpath'];
        $this->output("bigimg");
    }

    public function getOther()
    {
        $type = trim($_GET["type"]);
        if (!in_array($type, array('sale', 'qa', 'comment')))
            exit('failure');
        $func = "get" . $type;
        $this->$func();
    }

    /**
     *  
     * 销售记录
     *
     *
     * */
    public function getSale()
    {
        $itemid = intval($_GET['itemid']);
        $where = "itemid='$itemid'";
        list($page, $pagesize) = $this->getRequestPage();

        $count = DB::getDB()->selectcount("sales", $where);
        if ($count) {
            $this->data['pagearr'] = getPageArr($page, $pagesize, $count, url('index', 'item', 'getSale', "itemid=$itemid"));
            $this->data['salelist'] = DB::getDB()->select("sales", "uname,price,num,saletime", $where, "saleid DESC", $this->data['pagearr']['limit']);
        }
        $this->data['itemid'] = $itemid;
        $this->output("item_sale");
    }

    /**
     *  
     * 评论
     *
     *
     * */
    public function getComment()
    {
        $itemid = intval($_GET["itemid"]);
        list($page, $pagesize) = $this->getRequestPage();
        $where = "itemid='$itemid' AND replytime!=0 AND isdel=0";
        $count = DB::getDB()->selectcount("user_comment", $where);
        if ($count) {
            $this->data['pagearr'] = getPageArr($page, $pagesize, $count, url('index', 'item', 'getComment', "itemid=$itemid"));
            $this->data['commentlist'] = DB::getDB()->select("user_comment", "*", $where, "commentid DESC", $this->data['pagearr']['limit']);
        }
        $this->data['itemid'] = $itemid;
        $this->output("item_comment");
    }

    /**
     *  
     * 咨询
     *
     *
     * */
    public function getQa()
    {
        $itemid = intval($_GET["itemid"]);
        list($page, $pagesize) = $this->getRequestPage();

        $where = "itemid='$itemid' AND replytime!=0 AND isdel=0";
        $count = DB::getDB()->selectcount("user_qa", $where);
        if ($count) {
            $this->data['pagearr'] = getPageArr($page, $pagesize, $count, url('index', 'item', 'getQa', "itemid=$itemid"));
            $this->data['qalist'] = DB::getDB()->select("user_qa", "*", $where, "qaid DESC", $this->data['pagearr']['limit']);
        }
        $this->data['itemid'] = $itemid;
        $this->output("item_qa");
    }

    /**
     *  
     * 保存咨询
     *
     *
     * */
    public function saveQa()
    {
        //如果用户未登录，设置为error
        if (empty($_SESSION['uid']))
            exit('error_access');

        //如果两次操作时间过快
        $uid = $_SESSION['uid'];
        $time = time();
        if ($time - $_SESSION['lastpost'] < 60) {
            exit(json_encode(array("code" => "failure", "text" => __("oper_fast"))));
        }

        $content = safehtml(trim($_POST["content"]));
        $itemid = intval($_POST["itemid"]);

        $qaseccode = strtolower(trim($_POST["qaseccode"]));
        $sess_verify = '';
        if (isset($_SESSION['verify'])) {
            $sess_verify = strtolower($_SESSION['verify']);
            //销毁verify
            unset($_SESSION['verify']);
        }

        if (!$qaseccode || ($sess_verify != $qaseccode)) {
            exit(json_encode(array("code" => "failure", "text" => __("wrong_seccode"))));
        } else {
            if (getClength($content) < 10) {
                exit(json_encode(array("code" => "failure", "text" => __("qa_short"))));
            } else {
                //入库
                DB::getDB()->insert("user_qa", array("uid" => $uid, "uname" => $_SESSION['uname'],
                    "itemid" => $itemid, "addtime" => $time,
                    "content" => $content, "ip" => getClientIp()));
                //更新用户最后操作时间
                $this->setLastPost();
                exit(json_encode(array("code" => "success", "text" => __("qa_success"))));
            }
        }
    }

    /**
     *  
     * 保存评论
     *
     *
     * */
    public function savecomment()
    {
        //如果用户未登录，设置为error
        if (empty($_SESSION['uid']))
            exit('error_access');

        //如果两次操作时间过快
        $uid = $_SESSION['uid'];
        $time = time();
        if ($time - $_SESSION['lastpost'] < 60) {
            exit(json_encode(array("code" => "failure", "text" => __("oper_fast"))));
        }


        $content = safehtml(trim($_POST["content"]));
        $score = intval($_POST["score"]);
        //判断用户是否购买了该商品
        $itemid = intval($_POST["itemid"]);
        $exist = DB::getDB()->selectexist("sales", "itemid='$itemid' AND uid='" . $_SESSION['uid'] . "'");
        if (!$exist) {
            exit(json_encode(array("code" => "failure", "text" => __("item_notbuy"))));
        }

        //如果评价内容不存在
        if (getClength($content) < 10) {
            exit(json_encode(array("code" => "failure", "text" => __("comment_short"))));
        } else {
            //判断用户有没有购买该商品
            DB::getDB()->insert("user_comment", array("uid" => $uid, "uname" => $_SESSION['uname'],
                "itemid" => $itemid, "addtime" => $time, "score" => $score,
                "content" => $content, "ip" => getClientIp()));
            $this->setLastPost();
            exit(json_encode(array("code" => "success", "text" => __("comment_success"))));
        }
    }

    /**
     *  
     * 关注一个商品
     *
     *
     * */
    public function addFavor()
    {
        if (empty($_SESSION['uid'])) {
            $this->data["opertype"] = "login";
            $this->output("ajaxloginreg");
        } else {
            $uid = $_SESSION['uid'];
            $itemid = intval($_POST["itemid"]);
            if (DB::getDB()->selectexist("user_favor", "itemid='$itemid' AND uid='$uid'")) {
                $this->data["info"] = __("addfavor_exist");
            } else {
                $favordata = array(
                    "uid" => $uid,
                    "itemid" => $itemid,
                    "addtime" => time()
                );
                DB::getDB()->insert("user_favor", $favordata);
                $this->data["info"] = __("addfavor_success");
            }
            $this->output("ajaxfavor");
        }
    }

    public function comprice()
    {
        if (ispostreq()) {
            if (!isset($_SESSION['uid'])) {
                $this->data["info"] = __("need_login");
            } else {
                $this->data["opertype"] = "save";
                $uid = intval($_SESSION['uid']);
                $uname = $_SESSION['uname'];

                $comprice = trim($_POST["comprice"]);
                $comweburl = trim($_POST["comweburl"]);
                $content = trim($_POST["comcont"]);

                $itemid = intval($_POST["itemid"]);
                $productid = intval($_POST["productid"]);
                $item = DB::getDB()->selectrow("item", "itemname,itemimg,price", "itemid='" . $itemid . "'");
                if (!$item) {
                    $this->data['info'] = __("item_not_exist");
                } else {
                    $data = array(
                        "uid" => $uid,
                        "uname" => $uname,
                        "productid" => $productid,
                        "itemid" => $itemid,
                        "itemname" => $item['itemname'],
                        "itemimg" => $item['itemimg'],
                        "price" => $item['price'],
                        "content" => $content,
                        "comprice" => $comprice,
                        "comweburl" => $comweburl,
                        "addtime" => time(),
                    );
                    DB::getDB()->insert("user_comprice", $data);
                    $this->data["info"] = __("comprice_success");
                }
            }
        } else {
            if (empty($_SESSION['uid'])) {
                $this->data["opertype"] = "login";
                $this->output("ajaxloginreg");
            } else {
                $this->data["itemid"] = !empty($_GET["itemid"]) ? intval($_GET["itemid"]) : 0;
                $this->data["productid"] = !empty($_GET["productid"]) ? intval($_GET["productid"]) : 0;
                $this->data["item"] = DB::getDB()->selectrow("item", "itemname,price", "itemid='" . $this->data['itemid'] . "' AND isdel=0");
                $this->data['opertype'] = "input";
            }
        }
        $this->output("ajaxcomprice");
    }

    /**
     *  
     * 到货通知
     *
     *
     * */
    public function addNotify()
    {
        if (empty($_SESSION['uid'])) {
            $this->data["opertype"] = "login";
            $this->output("ajaxloginreg");
        } else {
            $this->data["itemid"] = !empty($_POST["itemid"]) ? intval($_POST["itemid"]) : 0;
            $this->data["productid"] = !empty($_POST["productid"]) ? intval($_POST["productid"]) : 0;
            $this->data["type"] = !empty($_POST["type"]) ? trim($_POST["type"]) : "downprice";
            $this->data["item"] = DB::getDB()->selectrow("item", "itemname,price", "itemid='" . $this->data['itemid'] . "' AND isdel=0");
            $this->data['opertype'] = "input";
            $this->output("ajaxnotify");
        }
    }

    /**
     *  
     * 保存到货通知
     *
     *
     * */
    public function saveNotify()
    {
        $this->data['opertype'] = "save";
        if (empty($_SESSION['uid'])) {
            $this->data["info"] = __("need_login");
        } else {
            //属性
            $uid = intval($_SESSION['uid']);
            $uname = $_SESSION['uname'];

            $productid = intval($_POST["productid"]);
            $itemid = intval($_POST["itemid"]);
            $type = !empty($_POST["type"]) ? trim($_POST["type"]) : "downprice";
            $spectext = array();
            $email = trim($_POST["email"]);
            $mobile = trim($_POST["mobile"]);

            //如果productid存在，计算该商品的属性
            if ($productid) {
                $itemid = DB::getDB()->selectval("product", "itemid", "productid='$productid'");

                //商品spec
                $itemspecs = DB::getDB()->selectkv("item_spec", "specid", "self", "itemid='$itemid'");
                foreach ($itemspecs as $k => $v) {
                    $itemspecs[$k] = unserialize($v);
                }
                //货品spec
                $productspecs = DB::getDB()->join("type_spec", "product_spec", array("on" => "typeid"), array("b" => "specid,specvalid"), array("b" => "productid=$productid"), array("a" => "order"), '', 'specid');
                foreach ($productspecs as $v) {
                    $spectext[] = $itemspecs[$v['specid']]['text'][$v['specvalid']];
                }
            }

            //item信息
            $item = DB::getDB()->selectrow("item", "itemname,itemimg", "itemid='" . $itemid . "'");
            $itemname = $item['itemname'] . ($spectext ? "  " . implode("  ", $spectext) : "");

            //入库
            $notifydata = array(
                "uid" => $uid,
                "uname" => $uname,
                "productid" => $productid,
                "itemid" => $itemid,
                "itemname" => $itemname,
                "itemimg" => $item['itemimg'],
                "email" => $email,
                "type" => $type,
                "mobile" => $mobile,
                "addtime" => time(),
            );
            DB::getDB()->insert("user_notify", $notifydata);
            $this->data["info"] = __("addnotify_success", __($type));
        }
        $this->output("ajaxnotify");
    }

}
