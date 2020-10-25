<?php
/**
 * ALIPAY API: alipay.trust.user.status.get request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:15:45
 */
class AlipayTrustUserStatusGetRequest
{
	/** 
	 * 入参json串
	 **/
	private $aliTrustUserInfo;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setAliTrustUserInfo($aliTrustUserInfo)
	{
		$this->aliTrustUserInfo = $aliTrustUserInfo;
		$this->apiParas["ali_trust_user_info"] = $aliTrustUserInfo;
	}

	public function getAliTrustUserInfo()
	{
		return $this->aliTrustUserInfo;
	}

	public function getApiMethodName()
	{
		return "alipay.trust.user.status.get";
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
