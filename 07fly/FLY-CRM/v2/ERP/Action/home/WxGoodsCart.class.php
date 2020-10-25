<?php 
class WxGoodsCart extends Action{	
	private $cacheDir='';
	//产品购买+加入购物车列表
	public function goods_card_add($ids,$num=1){
		$msg=array();
		if(empty($_SESSION["mycardlist"])){
			//如果点击的购物车是空的（第一次添加）
			//如果购物车里是空的，造二维数组，
			$arr = array(
				array($ids,$num)//一维数组，取ids，第一次点击增加一个
			);
			$_SESSION["mycardlist"]=$arr;//扔到session里面
			$msg['number']=$num;
		}else{
			//先判断购物车里是否已经有了该商品，用$ids
			$arr = $_SESSION["mycardlist"];//把购物车的状态取出来
			$chuxian = false;//定义一个变量；用来表示是否出现，默认是未出现
			foreach ($arr as $v) {
				if ($v[0] == $ids) //如果取过来的$v[0]（商品的代号）等于$ids那么就证明购物车中已经有了这一件商品
				{
					$chuxian = true;
				}
			}	
			if($chuxian){//购物车中有此商品
				for($i=0;$i<count($arr);$i++){
					if($arr[$i][0] == $ids){
						//把点到的商品编号加上个数
						$arr[$i][1] += $num;
						$msg['number']=$arr[$i][1];
					}
				}
				$_SESSION["mycardlist"] = $arr;
				
			}else{//这里就只剩下：购物车里有东西，但是并没有这件商品
				$asg = array($ids,$num);//设一个小数组
				$arr[] = $asg;
				$_SESSION["mycardlist"]=$arr;
				$msg['number']=$num;
        }	
		}
	}
	public function goods_card_list(){
		$list=$_SESSION["mycardlist"];
		$rtn =array();
		foreach($list as $key=>$row){
			$sql ="select * from fly_goods where goods_id='$row[0]'";	
			$one =$this->C($this->cacheDir)->findOne($sql);	
			if(!empty($one)){
				$one["number"]=$row[1];
				$one["money"] =$row[1]*$one["sale_price"];		
				$rtn[$key]=$one;
			}
		}
		return $rtn;
	}
	//产品购买+加入购物车列表
	public function goods_card_del(){
		$ids=$this->_REQUEST("id");
		$msg=array();
		$arr = $_SESSION["mycardlist"];
		foreach ($arr as $key=>$v) {
			if ($v[0] == $ids){
				unset($arr[$key]);
			}
		}	
		$_SESSION["mycardlist"] = $arr;
	}
}//
?>