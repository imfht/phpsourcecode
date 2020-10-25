<?php

/**
 *
 * 获取规格
 *
 */
class SKU
{

    /**
     *
     * 获取商品自定义spec
     *
     */
    public static function getItemSpecs($itemid)
    {
        //商品spec
        $itemspecs = DB::getDB()->selectkv("item_spec", "specid", "self", "itemid='$itemid'");
        foreach ($itemspecs as $k => $v) {
            $itemspecs[$k] = unserialize($v);
        }
        return $itemspecs;
    }

    /**
     *
     * 获取货品spec
     *
     */
    public static function getProductSpecs($itemid, $productid = 0)
    {
        $where = $productid ? "productid='$productid'" : "itemid='$itemid'";

        //获取货品spec
        $productspecs = DB::getDB()->join("type_spec", "product_spec", array("on" => "typeid"), array("b" => "specid,specvalid,productid"), array("b" => $where), array("a" => "order"), '', 'specid');
        $itemspecs = self::getItemSpecs($itemid);

        //文本替换
        $ret = array();
        foreach ($productspecs as $specid => $productspec) {
            $specvalid = $productspec['specvalid'];
            $ret[$productspec['productid']][] = isset($itemspecs[$specid]['text'][$productspec['specvalid']]) ? $itemspecs[$specid]['text'][$productspec['specvalid']] : "";
        }
        if ($productid)
            return $ret[$productid];
        return $ret;
    }

    /**
     *  
     * 获取货品
     *
     *
     * */
    public static function getProduct($itemid)
    {
        //库存大于零的货品
        $products = DB::getDB()->select("product", "*", "itemid='$itemid' AND inventory>0", "", "", "productid");
        if (!$products)
            return null;
        //货品的spec
        $productspecs = DB::getDB()->select("product_spec", "*", "itemid='$itemid'");

        //商品的spec
        $itemspecs = DB::getDB()->selectkv("item_spec", "specid", "self", "itemid='$itemid'");
        foreach ($itemspecs as $k => $itemspec) {
            $itemspecs[$k] = @unserialize($itemspec);
        }
        //替换specid,specvalid
        foreach ($productspecs as $k => $productspec) {
            $productid = $productspec['productid'];
            if (isset($products[$productid])) {
                $specid = $productspec['specid'];
                $specvalid = $productspec['specvalid'];
                $products[$productid]['spec'][] = $itemspecs[$specid]['text'][$specvalid]; //赋值products数组
            }
        }

        //循环products
        $ret = array();
        foreach ($products as $product) {
            $ret[$product['productid']]['productid'] = $product['productid'];
            $ret[$product['productid']]['spec'] = implode(" ", $product['spec']);
        }
        return array2select($ret, "productid", "spec");
    }

}
