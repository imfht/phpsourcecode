<?php
/**
 * ALIPAY API: alipay.asset.account.unbind request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:17:01
 */
class AlipayAssetAccountUnbindRequest
{
	/** 
	 * 业务参数 使用该app提供用户信息的商户在支付宝签约时的支付宝账户userID，可以和app相同。
	 **/
	private $providerId;
	
	/** 
	 * 用户在商户网站的会员标识。商户需确保其唯一性，不可变更。
	 **/
	private $providerUserId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setProviderId($providerId)
	{
		$this->providerId = $providerId;
		$this->apiParas["provider_id"] = $providerId;
	}

	public function getProviderId()
	{
		return $this->providerId;
	}

	public function setProviderUserId($providerUserId)
	{
		$this->providerUserId = $providerUserId;
		$this->apiParas["provider_user_id"] = $providerUserId;
	}

	public function getProviderUserId()
	{
		return $this->providerUserId;
	}

	public function getApiMethodName()
	{
		return "alipay.asset.account.unbind";
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}
}
