<?php
class rechargeModel extends Model {
	
	function getData($map='',$limit='',$order='`id` desc'){
		if(empty($order))$order = '`id` desc';
		return $this->where($map)->limit($limit)->order($order)->select();
	}
	//生成订单号
	public function produceSn(){
		do {
			$string = rand_string(8);
			$sn = 'RE'.$string.date('Ymd');
			$sn = str_replace('.', '', $sn);
			$map['sn'] = array('eq',$sn);
			$tmp = $this->isExist($map);
		}while ($tmp);
		return $sn;
	}
//判断条件是否存在 直接输入ID也行
	function isExist($where){
		if(!is_array($where)){
			$map[$this->getPk()] = array('eq',$where);
		}else{
			$map = $where;
		}
		if(!empty($map)){
			$data = $this->where($map)->find();
			if(!empty($data)){
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function succPay($info){
		if(is_array($info)){
			$map = array(
				'sn'=>array('eq',$info['sn']),
				'status'=>array('eq',0),
			);
			$rechargedata = $this->where($map)->find();
			//商品存在
			if($rechargedata){
				//应付金额也正确
				if($rechargedata['cash'] == $info['cash']){
					$updata = array(
						'id'=>$rechargedata['id'],
						'have_pay' => 1,
						'status'=>1,
					);
					$this->save($updata);
					$member = D('user_scoresum');
					opuserscore($rechargedata['uid'], 1, 'score', $rechargedata['score']);
					opuserscore($rechargedata['uid'], 1, 'pay', $rechargedata['score']);
		 			//$member->setInc('cash',"id={$rechargedata['uid']}",toPrice($rechargedata['cash']));
		 		
				}
			}
		}
	}
	
}
?>