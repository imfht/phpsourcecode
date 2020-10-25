<?php

/**
 * Created by PhpStorm.
 * User: wolf
 * Date: 15/12/23
 * Time: 上午10:14
 */


$salesSer=new SalesService();
$salesSer->batch();
class SalesService
{


    static $pdo;


    public function  __construct()
    {

        $config = require '../config/database.php';
        $connect = $config ['type'] . ":host=" . $config ['host'] . ";port=" . $config ['port'] . ";dbname=" . $config ['dbname'];
        try {
            self::$pdo = new PDO ($connect, $config ['username'], $config ['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit ();
        }

    }

    public function batch()
    {

        $rs = $this->getAllGoods();
        $format="sku:%s,salesnum:%s,sales:%s\r\n";
        foreach ($rs as $k => $v) {
            $goods = $this->getSalesNumBySKU($v['sku']);
            if (empty($goods)) {
              //  echo $v['goods_name'] . " not find!\r\n";
                continue;
            }



            if ($v['sales'] != $goods['total_num']&&$v['sales']>0) {
              echo sprintf($format,$v['sku'],$goods['total_num'],$v['sales']);
                continue;
            }


        }

    }

    private function getSalesNumBySKU($sku)
    {

        $sql = "select a.goods_id,a.goods_name,sum(a.num) total_num from w_order_info a left join w_order_list b on a.orderno=b.orderno where status>0 and a.goods_id='$sku'";
        return self::$pdo->query($sql)->fetch();
    }

    private function getAllGoods()
    {
        $sql = "SELECT sales,sku,goods_name FROM w_news_goods";
        return self::$pdo->query($sql)->fetchAll();
    }
}