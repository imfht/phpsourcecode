<?php

/**
 *  
 * 搭配套餐
 *
 *
 * */
class Meal extends Base
{

    /**
     *  
     * 搭配套餐首页
     *
     *
     * */
    public function index()
    {
        $mealid = intval($_GET["mealid"]);
        $where = "mealid='$mealid'";

        //类别
        $this->data["cats"] = $this->getCats();

        //套餐
        $this->data['meal'] = Promotion::getMeal($mealid, "mealid");
        if (!$this->data['meal'])
            cerror(__("meal_not_exist"));
        $this->data['mealid'] = $mealid;

        //商品规格
        foreach ($this->data['meal']['items'] as $k => $item) {
            $this->data['products'][$item['itemid']] = SKU::getProduct($item['itemid']);
        }
        $this->output("meal");
    }

    /**
     *  
     * 购买搭配套餐
     *
     *
     * */
    public function buymeal()
    {
        //商品列表
        $cart = !empty($_SESSION["cart"]["list"]) ? $_SESSION["cart"]["list"] : array();
        if ($cart)
            cerror(__("meal_buy_single"));

        //搭配套餐
        $mealid = intval($_POST["mealid"]);
        $itemids = isset($_POST["itemid"]) ? $_POST["itemid"] : array();
        $num = intval($_POST["num"]);
        !$num && ($num = 1);

        $this->mealToCart($mealid, $num, $itemids);
        redirect(url('index', 'cart'));
    }

}
