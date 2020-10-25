<?php
/**
 * ALIPAY API: alipay.trust.user.nontoken.admission.get request
 *
 * @author auto create
 * @since 1.0, 2014-09-11 14:25:30
 */
class AlipayTrustUserNontokenAdmissionGetRequest
{
	/** 
	 * {"openId":"xxxxxx", "name":"张钊", "certNo":"430302198712160770", "certType":"IDENTITY_CARD" //可选参数，默认为IDENTITY_CARD }
	 **/
	private $bizContent;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trust.user.nontoken.admission.get";
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
