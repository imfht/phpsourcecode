<?php
/**
 * 会员主表数据模型
 * @author 齐迹  email:smpss2012@gmail.com
 */
class m_membercache extends base_m {
	public function primarykey() {
		return 'mid';
	}
	public function tableName() {
		return base_Constant::TABLE_PREFIX . 'member_cache';
	}
	public function relations() {
		return array ();
	}
	public function insertcache($mid) {
		if (! $mid) {
			$this->setError ( 0, "缺少必要参数" );
			return false;
		}
		$data['mid']=$mid;
		 $this->insert($data);
	}
	public function updatecache($mid,$sql) {
		if (! $mid) {
			$this->setError ( 0, "缺少必要参数" );
			return false;
		}
		 $this->update("mid={$mid}",$sql);
	}
	/**
	 * 获取会员价格
	 * @param int $cardID
	 * @param int $price
	 * @return
	 */
	public function getMemberPrice($cardID, $price = 0) {
		$mTable = $this->tableName ();
		$gTable = base_Constant::TABLE_PREFIX . 'mbgroup';
		$rs = $this->selectOne ( "{$mTable}.membercardid='{$cardID}' and {$mTable}.state=1", "{$gTable}.discount as discount,{$mTable}.mid,{$mTable}.realname,{$mTable}.membercardid", '', '', array ("{$gTable}" => "{$gTable}.mgid={$mTable}.grade" ) );
		if (! $rs ['discount'])
			$rs ['discount'] = 100;
		$data ['mid'] = $rs ['mid'];
		$data ['membercardid'] = $rs ['membercardid'];
		$data ['realname'] = $rs ['realname'];
		$data ['discount'] = $rs ['discount'];
		$data ['price'] = sprintf ( "%01.2f", $price * $rs ['discount'] / 100 );
		return $data;
	}
	public function setexCredit($mid,$exchangecredit) {
		$rs = $this->update ( "mid={$mid}", "exchangecredit=exchangecredit+{$exchangecredit}" );
		if ($rs)
			return true;
		return false;
	}
}