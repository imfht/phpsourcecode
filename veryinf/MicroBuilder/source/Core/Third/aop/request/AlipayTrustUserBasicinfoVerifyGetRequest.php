<?php
/**
 * ALIPAY API: alipay.trust.user.basicinfo.verify.get request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:15:52
 */
class AlipayTrustUserBasicinfoVerifyGetRequest
{
	/** 
	 * 入参json串,  其中*号为encryp_code。
确保每个字段的值的总长度必须与没加密之前的字段长度要一致
	 **/
	private $aliTrustUserInfo;
	
	/** 
	 * 只能为单个字符，不传默认为*
	 **/
	private $encrypCode;

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

	public function setEncrypCode($encrypCode)
	{
		$this->encrypCode = $encrypCode;
		$this->apiParas["encryp_code"] = $encrypCode;
	}

	public function getEncrypCode()
	{
		return $this->encrypCode;
	}

	public function getApiMethodName()
	{
		return "alipay.trust.user.basicinfo.verify.get";
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
