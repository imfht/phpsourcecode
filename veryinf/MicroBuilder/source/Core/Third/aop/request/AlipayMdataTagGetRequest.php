<?php
/**
 * ALIPAY API: alipay.mdata.tag.get request
 *
 * @author auto create
 * @since 1.0, 2014-07-17 17:44:19
 */
class AlipayMdataTagGetRequest
{
	/** 
	 * 所需标签列表, 以","分割; 如果列表为空, 则返回值为空.
	 **/
	private $requiredTags;
	
	/** 
	 * 用户的支付宝Id
	 **/
	private $userId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setRequiredTags($requiredTags)
	{
		$this->requiredTags = $requiredTags;
		$this->apiParas["required_tags"] = $requiredTags;
	}

	public function getRequiredTags()
	{
		return $this->requiredTags;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "alipay.mdata.tag.get";
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
