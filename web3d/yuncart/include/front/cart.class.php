<?php

defined('IN_CART') or die;

/**
 *  
 * 购物车
 *
 *
 * */
class Cart extends Base
{

    /**
     *  
     * 购物车
     *
     *
     * */
    public function index()
    {
        $this->output("cart");
    }

    /**
     *  
     * 商品添加到购物车
     *
     *
     * */
    public function addCart()
    {
        //单独购买套餐
        $data = array();
        if (!empty($_SESSION['cart']['meal'])) {
            $this->data['error'] = __("meal_buy_single");
        } else {
            $this->_addCart(true);
        }
        $this->output("ajaxcart");
    }

    private function _addCart($return = false)
    {
        //商品信息
        $itemid = !empty($_POST["itemid"]) ? intval($_POST["itemid"]) : 0;
        $productid = !empty($_POST["productid"]) ? intval($_POST["productid"]) : 0;
        $num = intval($_POST["num"]);
        $this->toCart($itemid, $productid, $num);
        if ($return)
            return $this->data;
    }

    /**
     *  
     * 获取购物车商品
     *
     *
     * */
    public function getCart()
    {
        $this->data['cartitems'] = $this->getCartItems();
        if (isset($_SESSION['cart']['itemfee'])) {
            $this->data['man'] = Promotion::getManRule($_SESSION['cart']['itemfee']);
        }
        $this->output("getcart", false);
    }

    /**
     *  
     * 购物车商品
     *
     *
     * */
    public function opernum()
    {
        $type = trim($_POST["type"]);
        $id = trim($_POST["id"]);
        $sessionkey = "{$type}_{$id}";

        if (!isset($_SESSION["cart"]["list"][$sessionkey])) {
            $this->data['error'] = __("access_error");
            $this->getCart();
        }
        $oper = trim($_POST["oper"]);
        !in_array($oper, array("set", "add", "minus")) && ($oper = "set");

        $cartnum = $_SESSION["cart"]["list"][$sessionkey]['num'];
        if ($oper == "add") {
            $cartnum ++;
        } elseif ($oper == "minus") {
            $cartnum --;
        } elseif ($oper == "set") {
            $cartnum = intval($_POST["num"]);
        }

        //比较库存
        $inventory = 0;
        if ($type == "itemid") {
            $inventory = DB::getDB()->selectval("item", "inventory", "itemid='$id'");
        } elseif ($type == "product") {
            $inventory = DB::getDB()->selectval("product", "inventory", "productid='$id'");
        } elseif ($type == "meal") {
            $inventory = DB::getDB()->selectval("meal", "inventory", "mealid='$id'");
        }

        if ($cartnum <= 0 || $inventory <= 0 || $cartnum > $inventory) { //购物车，或库存为0 error
            $this->data['error'] = __("inventory_not_enough");
            $this->getCart();
        }

        $_SESSION["cart"]["list"][$sessionkey]['num'] = $cartnum;
        $this->getCart();
    }

    /**
     *  
     * 清空购物车
     *
     *
     * */
    public function clearcart()
    {
        $_SESSION['cart'] = array();
        $this->getCart();
    }

    /**
     *  
     * 关注某购物车商品
     *
     *
     * */
    public function addFavor()
    {
        $ret = false;
        if (empty($_SESSION['uid']))
            exit("failure");
        $type = trim($_POST["type"]);
        $id = intval($_POST["id"]);
        $itemid = 0;
        if ($type == "product") {
            $itemid = DB::getDB()->selectval("product", "itemid", "productid='$id'");
        } else if ($type == "meal") {
            $itemid = DB::getDB()->selectval("meal", "mealid", "mealid='$id'");
        } else {
            $itemid = $id;
        }
        if ($itemid) {
            DB::getDB()->replace("user_favor", array("itemid" => $itemid, "uid" => $_SESSION['uid'], "addtime" => time()));
            $ret = true;
        }
        exit($ret ? "success" : "failure");
    }

    /**
     *  
     * 删除购物车某商品
     *
     *
     * */
    public function todel()
    {
        $type = trim($_POST["type"]);
        $id = trim($_POST["id"]);
        $sessionkey = "{$type}_{$id}";
        if (cstrpos($sessionkey, "meal") !== false) { //删除套餐，清空购物车
            $_SESSION['cart'] = array();
        } else {
            if (isset($_SESSION["cart"]["list"][$sessionkey])) {//删除
                unset($_SESSION["cart"]["list"][$sessionkey]);
            }
        }
        $this->getCart();
    }

    /**
     *  
     * 重新购买
     *
     *
     * */
    public function rebuy()
    {
        if (!empty($_SESSION['cart']['meal'])) { //如果购物车有套餐，意味非法，清空购物车
            $_SESSION['cart'] = array();
        } else {
            $this->_addCart();
        }
        $this->getCart();
    }

}
