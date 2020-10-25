<?php
/**
 * Created by PhpStorm.
 * User: wolf
 * Date: 15/12/22
 * Time: 上午11:27
 */
$bankSer = new BankOrderService();
$bankSer->batch();

class BankOrderService
{

    private $_merID = '301330459999501';
    static $pdo;
    private $_totalnum = 0;
    private $_start = 0;
    private $_add_time;
    private $_num = 20;//每次查询到的条数
    private $_table='w_coupons_history';

    public function  __construct()
    {

        $config = require '/opt/www/xingfu10086/config/database.php';
        $connect = $config ['type'] . ":host=" . $config ['host'] . ";port=" . $config ['port'] . ";dbname=" . $config ['dbname'];
        try {
            self::$pdo = new PDO ($connect, $config ['username'], $config ['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit ();
        }
        $this->_add_time = strtotime("-5 days");
        $this->getNum();
    }


    public function batch()
    {

        $page = ceil($this->_totalnum / $this->_num);
        echo "totalnum:" . $this->_totalnum . "\r\n";
        for ($i = 0; $i < $page; $i++) {
            echo "start...." . $i . "\r\n";
            $this->getOrder($i);
        }
    }


    private function getNum()
    {
         $sql = "select id from $this->_table where chargetypes=5 and status=1 and payment=0 and date>$this->_add_time";
        $this->_totalnum = self::$pdo->query($sql)->rowCount();
    }


    private function getOrder($p)
    {

        $num = $this->_num;
        $start = $p * $num;
        $sql = "select orderno from $this->_table where chargetypes=5 and status=1 and date>$this->_add_time LIMIT $start,$num";
        $rs = self::$pdo->query($sql)->fetchAll();
        if (empty($rs)) {
          //  echo "no db data";
            return;
        }

        $str = '';
        foreach ($rs as $k => $v) {
            $str .= $v['orderno'] . '|';
        }
        $str = substr($str, 0, strlen($str) - 1);
        $this->valid($str);
    }


    private function valid($orders)
    {

        $merID = $this->_merID; //商户号为固定

        $tranCode = "cb2202_queryOrderOp";
        //连接地址
        $socketUrl = "tcp://127.0.0.1:8891";
        $fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
        $retMsg = "";

        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $in = "<?xml version='1.0' encoding='UTF-8'?>";
            $in .= "<Message>";
            $in .= "<TranCode>" . $tranCode . "</TranCode>";
            $in .= "<MsgContent><BOCOMB2C><opName>" . $tranCode . "</opName><reqParam>";
            $in .= "<merchantID>" . $merID . "</merchantID><orders>" . $orders . "</orders><detail>" . "1" . "</detail></reqParam></BOCOMB2C></MsgContent>";
            $in .= "</Message>";
            fwrite($fp, $in);
            while (!feof($fp)) {
                $retMsg = $retMsg . fgets($fp, 1024);
            }
            fclose($fp);

            //解析返回xml
            $dom = new DOMDocument;
            $dom->loadXML($retMsg);

            $retCode = $dom->getElementsByTagName('retCode');
            $retCode_value = $retCode->item(0)->nodeValue;

            $errMsg = $dom->getElementsByTagName('errMsg');
            $errMsg_value = $errMsg->item(0)->nodeValue;

            $num = count(explode("//|", $orders)) + 1;
            if ($num > 20) {
                return;
                // $retCode_value = "EBLN5000";
                // $errMsg_value = "一次查询定单信息数请不要超过20笔";
            }

              // echo "交易返回码：".$retCode_value."<br>";
             //echo "交易错误信息：" .$errMsg_value."<br>";

            if ($retCode_value == "0") {
                $BOCOMB2C = new SimpleXMLElement($retMsg);
                foreach ($BOCOMB2C->opRep->opResultSet->opResult as $opResult) {

                    if ($opResult->orderState == 1) {
                        echo ' orderno:' . $opResult->order . "\r\n";
                        $this->saveAccountByOrderno($opResult->order, $opResult->bankSerialNo);
                    }

                    //   echo ' orderno:'.$opResult->order;
                    // echo ' state:'.$opResult->orderState;
                    //echo ' No:'.$opResult->bankSerialNo."\r\n";

                }
            }
        }
    }

    private function saveAccountByOrderno($orderno, $remark)
    {
        $paytime=time();

        $sql2="SELECT coupons,uid,remark FROM $this->_table WHERE orderno='$orderno'";
        $rs=self::$pdo->query($sql2)->fetch(PDO::FETCH_ASSOC);

        $sql3="UPDATE w_member_list SET coupons=coupons+$rs[coupons] WHERE uid=$rs[uid]";
        self::$pdo->exec($sql3);

        $remark=$rs['remark'].$remark;

        $sql1= "UPDATE $this->_table SET status=8,remark='$remark',pay_time=$paytime WHERE orderno='$orderno'";
        self::$pdo->exec($sql1);




    }


}