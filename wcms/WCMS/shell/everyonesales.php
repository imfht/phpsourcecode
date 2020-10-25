<?php
/**
 * 本地生成文件  2015年度物料兑换情况
 * User: wolf
 * Date: 15/12/23
 * Time: 上午10:14
 */


$salesSer=new SalesService('2015-01-01', '2015-12-31');
$salesSer->batch();
class SalesService
{


    static $pdo;
    private $_start;
    private $_end;


    public function  __construct($start, $end)
    {
        $config = require '../config/database.local.php';
        $connect = $config ['type'] . ":host=" . $config ['host'] . ";port=" . $config ['port'] . ";dbname=".$config['dbname'];
        try {
            self::$pdo = new PDO ($connect, $config ['username'], $config ['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit ();
        }
        $this->_start = strtotime($start);
        $this->_end = strtotime($end);
        
    }


    private function getData($start, $end){
    	$sql = "select ol.uid, m.real_name, oi.goods_id, oi.goods_name, sum(oi.num), sum(oi.coupons_total) 
				from w_order_list as ol
				left join w_member_list as m on ol.uid=m.uid
				left join w_order_info as oi on ol.orderno=oi.orderno
				where ol.status=8 and ol.addtime>=$this->_start and ol.addtime<=$this->_end
    			group by m.real_name, oi.goods_id, oi.goods_name
				order by ol.uid";
    	try {
    		$rs = self::$pdo->query($sql)->fetchAll();
    		return $rs;
    	}catch (PDOException $e){
    		            echo $e->getMessage();
            exit ();
    	}
    }

    public function batch()
    {

        $file=getcwd()."sale.csv";
        $handle=fopen("./sale.csv","w") or die("open filed");


        $data=$this->getData($this->_start, $this->_end);

        $format="%s,%s,%s,%s,%s,%s\r\n";
        $title = sprintf($format, "用户Id","姓名","产品ID","产品名称","数量","总积分");
        fwrite($handle, $title);
        foreach($data as $v) {

            $line=sprintf($format,$v[0],$v[1],$v[2],$v[3],$v[4],$v[5]);
            fwrite($handle, $line);
        }
        fclose($handle);

    }


}