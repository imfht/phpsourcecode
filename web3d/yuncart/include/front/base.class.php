<?php

/**
 *
 * 前台操作基本类
 * 
 */
class Base
{

    public $data = array();

    /**
     *
     * 构造函数
     * 
     */
    public function __construct($model, $action)
    {
        $this->data['model'] = $model;
        $this->data['action'] = $action;

        //判断是否营业
        $status = getConfig("status");
        if ($status == "close") { //如果店铺被关闭
            cerror(getConfig("closenotice"));
        }

        $this->cookieToCart();
    }

    public function __destruct()
    {
        $this->cartToCookie();
    }

    /**
     *
     * 把购物车的商品加入cookie
     * 
     */
    public function cartToCookie()
    {
        $list = isset($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        $setcookie = array();
        foreach ($list as $k => $v) {
            if (preg_match("/^meal.+/", $k)) {//如果是套餐
                if (count($list) != 1) {//出错了，套餐必须单独购买
                    break;
                }
                $mealitems = $_SESSION["cart"][$k];
                $temp = array();
                foreach ($mealitems as $itemid => $product) {
                    $temp[] = $itemid . '_' . $product['productid'];
                }
                $setcookie[] = $k . '_' . $v['num'] . "::" . ($temp ? implode(";", $temp) : '');
                break;
            } else {
                $setcookie[] = $k . '_' . $v['num'];
            }
        }
        $setcookie = implode(",", $setcookie);
        $getcookie = cgetcookie("cart");
        if (md5($getcookie) !== md5($setcookie)) {//如果md5不一样
            csetcookie("cart", $setcookie, 604800);
        }
    }

    /**
     *
     * 把购物车中的商品加入cookie
     * 
     */
    public function cookieToCart()
    {
        $getcookie = cgetcookie("cart");
        if (!$getcookie)
            return;

        $list = isset($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        $getcookie = explode(",", $getcookie);
        foreach ($getcookie as $k => $v) {
            $str = cstrpos($v, "::") ? substr($v, 0, strpos($v, "::")) : $v;
            @list($type, $id, $num) = explode("_", $str, 3);
            if (!$type || !$id || !$num)
                continue; //参数不正确
            $sessionkey = $type . '_' . $id;
            if (isset($list[$sessionkey]))
                continue; //购物车已经存在，
            if ($type == 'item') {
                $this->toCart(intval($id), 0, intval($num), true);
            } elseif ($type == "product") {
                $this->toCart(0, intval($id), intval($num), true);
            } elseif ($type == "meal" && count($getcookie) == 1) {
                $mealitemstr = substr($v, strpos($v, "::") + 2);
                $temp = explode(";", $mealitemstr);
                $mealitems = array();
                foreach ($temp as $v) {
                    @list($itemid, $productid) = explode("_", $v);
                    $mealitems[$itemid] = $productid;
                }
                $this->mealToCart($id, $num, $mealitems, true);
            }
        }
    }

    /**
     *
     * 套餐加入购物车
     * 
     */
    public function mealToCart($mealid, $num = 1, $itemids = array(), $silent = false)
    {
        $time = time();
        $meal = DB::getDB()->selectrow("meal", "price,inventory", "mealid='$mealid' AND ispublish=1 AND begintime<'$time' AND endtime>'$time'");
        if (!$meal) {
            !$silent && cerror(__("meal_not_exist_or_expire"));
        }
        $price = $meal['price'];
        $inventory = $meal['inventory'];
        if ($num <= 0 || $inventory <= 0 || $inventory < $num) {
            !$silent && cerror(__("inventory_not_enough"));
        }

        //保存mealid到session，不允许购买其他商品
        $_SESSION['cart']['meal'] = array("mealid" => $mealid);

        $sessionkey = "meal_{$mealid}";


        foreach ($itemids as $itemid => $productid) {
            $_SESSION['cart'][$sessionkey][$itemid] = array("itemid" => $itemid,
                "productid" => $productid,
                "spectext" => implode(" ", SKU::getProductSpecs($itemid, $productid)));
        }

        $price = getPrice($price, -2, 'float');

        //某一个商品的数目
        if (!isset($cart[$sessionkey])) {
            $cart[$sessionkey] = array("num" => $num, "price" => $price);
        } else {
            $cart[$sessionkey] = array("num" => $num + intval($cart[$sessionkey]["num"]), "price" => $price);
        }

        //计算商品总数目，总价格
        $num = $itemtotal = 0;
        foreach ($cart as $k => $v) {
            $num += $v['num'];
            $itemtotal += $v['price'] * $v['num'];
        }

        $_SESSION["cart"]["list"] = $cart;
        $_SESSION['cart']['nums'] = $data["nums"] = $num;
        $_SESSION['cart']['itemtotal'] = $data["itemtotal"] = $itemtotal;
    }

    /**
     *
     * 商品加入购物车
     * 
     */
    public function toCart($itemid = 0, $productid = 0, $num = 1, $silent = false)
    {
        $inventory = $price = $theprice = 0;
        $sessionkey = "";
        $discount = $tuan = false;
        if ($productid) { //多货品
            //商品信息
            $onarr = array("on" => "itemid");
            $fields = array("a" => "price,itemid,inventory,productid");
            $where = "a.productid='$productid' AND b.status=1 AND b.isdel=0"; //搜索商品条件，库存大于0才能购买
            $item = DB::getDB()->joinrow("product", "item", $onarr, $fields, $where);
            if (!$item) {
                !$silent && $this->data['error'] = __("item_not_exist_or_instock");
                return false;
            }
            $sessionkey = "product_{$productid}";
            if (empty($_SESSION['cart'][$sessionkey])) {
                //货品spec
                $_SESSION['cart'][$sessionkey] = implode(' ', SKU::getProductSpecs($item['itemid'], $item['productid']));
            }

            //判断是否限时打折
            $discount = Promotion::itemInDiscount($item["itemid"]); //团购与折扣不能同时
            if (!$discount)
                $tuan = Promotion::itemInTuan($item["itemid"]);
            $price = $item['price'];
            $inventory = $item['inventory'];
        } elseif ($itemid) { //单商品
            $item = DB::getDB()->selectrow("item", "price,inventory", "itemid='$itemid' AND status=1 AND isdel=0");
            if (!$item) {
                !$silent && $this->data['error'] = __("item_not_exist_or_instock");
                return false;
            }
            $price = $item['price'];
            $inventory = $item['inventory'];
            $sessionkey = "item_$itemid";
            $discount = Promotion::itemInDiscount($itemid); //团购与折扣不能同时
            if (!$discount)
                $tuan = Promotion::itemInTuan($itemid);
        }
        !$num && ($num = 1);
        if ($num > $inventory) {
            !$silent && $this->data['error'] = __("inventory_not_enough");
            return false;
        }
        if (!$price) {
            !$silent && $this->data['error'] = __("price_error");
            return false;
        }
        if ($discount) {
            $theprice = getPrice($price * $discount['discount'], -5, 'float');
        } else if ($tuan) {
            $theprice = getPrice($tuan['price'], -2, 'float');
        } else {
            $theprice = getPrice($price, -2, 'float');
        }

        //商品列表
        $cart = !empty($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        //某一个商品的数目
        if (!isset($cart[$sessionkey])) {
            $cart[$sessionkey] = array("num" => $num, "price" => $theprice);
        } else {
            $cart[$sessionkey] = array("num" => $num + intval($cart[$sessionkey]["num"]), "price" => $theprice);
        }
        //如果是限时打折商品
        if ($discount) {
            $cart[$sessionkey]['discount'] = array("oldprice" => getPrice($price, -2, 'float'),
                "discount" => $discount['discount'],
                "newprice" => $theprice,
                "discountid" => $discount['discountid']);
        } else if ($tuan) {
            $cart[$sessionkey]['tuan'] = array("oldprice" => getPrice($price, -2, 'float'),
                "subject" => $tuan['subject'],
                "newprice" => $theprice,
                "price" => $tuan['price'],
                "tuanid" => $tuan['tuanid']);
        }

        //计算商品总数目，总价格
        $num = $itemtotal = 0;
        foreach ($cart as $k => $v) {
            $num += $v['num'];
            $itemtotal += $v['price'] * $v['num'];
        }

        $_SESSION["cart"]["list"] = $cart;
        $_SESSION['cart']['nums'] = $this->data["nums"] = $num;
        $_SESSION['cart']['itemtotal'] = $this->data["itemtotal"] = $itemtotal;
        return true;
    }

    /**
     *
     * 初始化前台公用
     * 
     */
    private function init()
    {
        //导航
        $this->buildNavi();

        //帮助
        $this->buildHelp();

        //热门关键词
        $this->buildAdWord();

        //搜索框关键词
        $this->data["inputword"] = getconfig("inputword");

        //网页标题
        $this->data["malltitle"] = getconfig("malltitle");
        $this->data["mallkeywords"] = getconfig("mallkeywords");
        $this->data["malldesc"] = getconfig("malldesc");
        $this->data["mallname"] = getconfig("mallname");

        //logo
        $this->data['logo'] = getconfig("malllogo");

        //cnzz
        $this->data['cnzz_siteid'] = getconfig("cnzz_siteid");

        //客服，
        $this->data["imset"] = DB::getDB()->selectkv("config", "key", "val", "type='imset'");
        $this->data["imusers"] = !empty($this->data["imset"]["imusers"]) ? @unserialize($this->data["imset"]["imusers"]) : array();

        $this->data["cartnum"] = isset($_SESSION['cart']['nums']) ? $_SESSION['cart']['nums'] : 0;
        $this->data['pagefoot'] = getConfig("pagefoot");
    }

    /**
     *
     * 热门关键词
     * 
     */
    private function buildAdWord()
    {
        $this->data["adwords"] = DB::getDB()->select("adword", "*", "isdel=0", "order");
    }

    /**
     *
     * 导航
     * 
     */
    private function buildNavi()
    {
        $this->data["navis"] = DB::getDB()->select("navi", "*", "isdel=0", "order");
    }

    /**
     *
     * 帮助
     * 
     */
    private function buildHelp()
    {
        $jtype = array("on" => "sortid");
        $where = array("a" => array("isdel" => 0, "type" => 1), "b" => array("ispublish" => 1, "isdel" => 0));
        $order = array("a" => "order", "b" => "order");
        $field = array("a" => "sortid,sortname", "b" => "contentid,subject,contenttype,link");
        $temp = DB::getDB()->join("content_sort", "content", $jtype, $field, $where, $order);
        $this->data["helpsorts"] = array();
        if ($temp) {
            foreach ($temp as $k => $v) {
                if (!isset($this->data["helpsorts"][$v["sortid"]])) {
                    $this->data["helpsorts"][$v["sortid"]] = array(
                        "sortid" => $v['sortid'],
                        "sortname" => $v['sortname'],
                    );
                }
                $this->data["helpsorts"][$v["sortid"]]["children"][] = array(
                    "contentid" => $v['contentid'],
                    "subject" => $v["subject"],
                    "contenttype" => $v["contenttype"],
                    "link" => $v["link"]
                );
            }
        }
    }

    /**
     *
     * 获取所有类别
     *
     */
    protected function getCats($return = 'tree')
    {
        static $cats;
        !$cats && $cats = DB::getDB()->select("cat", "catid,catname,pid,order,typeid", "isdel=0", "order", null, "catid");

        if ($return == 'tree') {   //返回数组
            $tree = array();
            foreach ($cats as $cat) { //循环cat
                if (isset($cats[$cat['pid']])) {//非第一级
                    $cats[$cat['pid']]['children'][$cat['catid']] = &$cats[$cat['catid']];
                } else {//第一级
                    $tree[$cat['catid']] = &$cats[$cat['catid']];
                }
            }
            return $tree;
        } else if ($return == 'source') {//返回source
            return $cats;
        }
    }

    /**
     *
     * 分页
     * 
     */
    public function getRequestPage($defpagesize = 10)
    {
        $page = isset($_REQUEST["page"]) ? abs(intval($_REQUEST["page"])) : 1;
        $pagesize = isset($_REQUEST["pagesize"]) ? abs(intval($_REQUEST["pagesize"])) : $defpagesize;

        $page > 100000 && $page = 100000;
        $pagesize > 100 && $page = 100;
        return array($page, $pagesize);
    }

    public function getDistrictopt($province, $city = 0, $district = 0)
    {
        $this->data['provinceopt'] = Dis::getDistrict(0, $province, "option");
        $province && $this->data['cityopt'] = Dis::getDistrict($province, $city, "option");
        $city && $this->data['districtopt'] = Dis::getDistrict($city, $district, "option");
        return true;
    }

    /**
     *
     * 获取某一个cat的上等级
     *
     */
    protected function getCatLevelUp($catid)
    {
        static $cats;
        !$cats && $cats = $this->getCats("source");
        $ret = array();
        if (isset($cats[$catid])) {
            $ret[$catid] = $cats[$catid];
            if (isset($cats[$catid]['pid'])) {
                $ret = $this->getCatLevelUp($cats[$catid]['pid']) + $ret;
            }
        }
        return $ret;
    }

    /**
     *
     * 获取某一个cat的下等级
     *
     */
    protected function getCatLevelDown($catid, $cats = array())
    {
        !$cats && $cats = $this->getCats();
        $ret = array();
        if (isset($cats[$catid])) { //如果第一级存在
            $ret[$catid] = $cats[$catid];
            return $ret;
        }
        foreach ($cats as $cat) {
            if (isset($cat['children']) && ($ret = $this->getCatLevelDown($catid, $cat['children']))) {
                break;
            }
        }
        return $ret;
    }

    /**
     *
     * 获取某一个cat的下等级list
     *
     */
    protected function getCatList($cats = array())
    {
        $ret = array();
        foreach ($cats as $k => $cat) {
            $ret[$cat['catid']] = &$cats[$k];
            if (isset($cat['children'])) {
                $ret += $this->getCatList($cat['children']);
                unset($cats[$k]['children']);
            }
        }
        return $ret;
    }

    /**
     *
     * 检查用户是否输出
     * 
     */
    protected function checklogin()
    {
        $this->data['islogin'] = isset($_SESSION['uid']) ? true : false;
        if ($this->data['islogin']) {
            $this->data['uname'] = $_SESSION['uname'];
            $this->data['uid'] = $_SESSION['uid'];
        }
    }

    /**
     *
     * 页面输出
     * 
     */
    protected function output($filename, $common = true)
    {
        $common && $this->Init();
        $this->checklogin();
        require_once COMMONPATH . "/dwoo.php";
        $dwoo = new Dwoo(DWOOCOMPILED, DWOOCACHE);
        $compiler = Dwoo_Compiler::compilerFactory();
        $compiler->setDelimiters("<!--{", "}-->");
        $dwoo->setCacheTime(0);
        $this->data['tpl'] = getConfig("tpl", "default");
        $dwoo->output(TPL . "/" . $this->data['tpl'] . "/{$filename}.html", $this->data, $compiler, getConfig("rewrite"));
        exit();
    }

    /**
     *
     * 获取订单产品详情
     *
     */
    protected function getCartItems()
    {
        //购物车列表
        $carts = !empty($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        if (!$carts) {//清空
            if (isset($_SESSION['cart']))
                unset($_SESSION['cart']);
            return array();
        }

        $items = array();
        //购物车商品数量
        $_SESSION['cart']['nums'] = 0;
        //商品费用，总费用
        $_SESSION['cart']['itemfee'] = $_SESSION['cart']['totalfee'] = 0;

        $sessionkeys = array_keys($carts);
        $itemids = $productids = array();
        $mealid = 0; //一次只能购买一个搭配套餐
        foreach ($carts as $key => $cart) {//解析购物车
            if (!cstrpos($key, "_")) {
                unset($carts[$key]);
                continue;
            }
            list($type, $id) = explode("_", $key);
            if ($type == "meal") { //套餐
                $mealid = $id;
            } elseif ($type == "product") { //货品
                $productids[] = $id;
            } elseif ($type == "item") { //商品
                $itemids[] = $id;
            }
            $carts[$key]['type'] = $type;
            $carts[$key]['id'] = $id;
            if ($type == "meal") { //如果是套餐，直接重新赋值
                $carts = array($key => $carts[$key]);
                break;
            }
        }

        //商品信息
        if ($itemids) {
            $items = DB::getDB()->select("item", "itemid,itemname,itemimg,price,point", "itemid in " . cimplode($itemids), "", "", "itemid");
        }
        //货品信息
        if ($productids) {
            $products = DB::getDB()->join("product", "item", array("on" => "itemid"), array("a" => "productid,price",
                "b" => "itemid,itemname,itemimg,point"), array("a" => "productid in " . cimplode($productids)), null, null, "productid");
        }
        //套餐信息
        if ($mealid) {
            $meal = DB::getDB()->selectrow("meal", "*", "mealid='$mealid'");
            $mealitems = DB::getDB()->select("meal_item", "*", "mealid='$mealid'", "order");
            foreach ($mealitems as $k => $mealitem) {
                $itemid = $mealitem['itemid'];
                $mealitem['price'] = getPrice($mealitem['price'], -2, 'float');
                $meal['mealitems'][$itemid] = $mealitem;
            }
        }
        foreach ($carts as $key => $cart) { //计算购物车价格
            $type = $cart['type'];
            $id = $cart['id'];
            $discount = $tuan = false;
            $price = 0;

            if ($type == "product") { //货品
                $itemid = $products[$id]['itemid'];

                if (isset($cart['discount'])) {//如果是折扣
                    $discount = $cart['discount'];
                    $price = getPrice($products[$id]['price'] * $discount['discount'], -5, 'float');
                } else if (isset($cart["tuan"])) {//如果是团购
                    $tuan = $cart['tuan'];
                    $price = getPrice($cart["tuan"]['price'], -2, "float");
                } else {//普通
                    $price = getPrice($products[$id]['price'], -2, 'float');
                }

                $carts[$key] = $cart + $products[$id];
                if (!$discount && !$tuan) {//团购和折扣不允许赠品
                    $carts[$key]['gifts'] = Promotion::getGifts($itemid);
                }
                $carts[$key]['itemname'] .= ' ' . $_SESSION['cart'][$key];
            } elseif ($type == "item") { //商品
                if (isset($cart['discount'])) {//如果是折扣
                    $discount = $cart['discount'];
                    $price = getPrice($items[$id]['price'] * $discount['discount'], -5, 'float');
                } else if (isset($cart["tuan"])) {//如果是团购
                    $tuan = $cart['tuan'];
                    $price = getPrice($cart["tuan"]['price'], -2, "float");
                } else {
                    $price = getPrice($items[$id]['price'], -2, 'float');
                }
                $carts[$key] = $cart + $items[$id];
                if (!$discount && !$tuan) {//团购和折扣不允许赠品
                    $carts[$key]['gifts'] = Promotion::getGifts($id); //赠品促销
                }
            } elseif ($type == "meal") { //套餐不考虑商品赠品
                $price = getPrice($meal['price'], -2, 'float');
                $products = isset($_SESSION['cart'][$key]) ? $_SESSION['cart'][$key] : array();
                foreach ($products as $itemid => $product) {
                    $meal['mealitems'][$itemid] += $product;
                    $meal['mealitems'][$itemid]['itemname'] .= ' ' . $product['spectext'];
                }
                $carts[$key] = $cart + $meal;
            }
            //计算商品价格
            $carts[$key]['itemfee'] = $carts[$key]['num'] * $price;
            //数量
            $_SESSION['cart']['nums'] += $carts[$key]['num'];
            $_SESSION['cart']['itemfee'] += $carts[$key]['itemfee'];
        }
        return $carts;
    }

    /**
     *
     * 获取提示
     *
     */
    public function getHint()
    {
        if (!isset($_SESSION['userhint']))
            return;
        $this->data['hint'] = $_SESSION['userhint'];
        unset($_SESSION['userhint']);
    }

    /**
     *
     * 设置提示
     *
     */
    protected function setHint($hint, $type, $url = '')
    {
        if (!$url) {
            $url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        }
        $_SESSION['userhint'] = array("hint" => __($hint), "type" => $type);
        redirect($url);
        exit();
    }

    protected function setLastPost()
    {
        if (empty($_SESSION['uid']))
            return;

        $time = time();
        //更新用户最后操作时间
        DB::getDB()->update("user", "lastpost='$time'", "uid='" . $_SESSION['uid'] . "'");

        //更新session的最后操作时间
        $_SESSION['lastpost'] = $time;
    }

    /**
     *
     * 错误
     *
     */
    protected function error()
    {
        if (isset($_SESSION['fronterror'])) {
            $this->data['error'] = $_SESSION['fronterror'];
            unset($_SESSION['fronterror']);
        } else {
            redirect(url("index", 'index'));
        }
    }

}
