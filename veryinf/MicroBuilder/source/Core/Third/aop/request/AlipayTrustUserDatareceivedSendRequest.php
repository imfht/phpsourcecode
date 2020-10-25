<?php
/**
 * ALIPAY API: alipay.trust.user.datareceived.send request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:15:49
 */
class AlipayTrustUserDatareceivedSendRequest
{
	/** 
	 * Json格式，具体内容根据不同的type_id而不同。详见芝麻信用的数据类型文档（线下提供）。
	 **/
	private $data;
	
	/** 
	 * 用以标识用户身份的字段，JSON格式，共包括5个属性。其中至少用包含name在内的两个字段来刻画该用户，并尽可能填写完整。
	 **/
	private $identity;
	
	/** 
	 * 数据类型ID，由芝麻信用针对不同商户而分配
	 **/
	private $typeId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setData($data)
	{
		$this->data = $data;
		$this->apiParas["data"] = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
		$this->apiParas["identity"] = $identity;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setTypeId($typeId)
	{
		$this->typeId = $typeId;
		$this->apiParas["type_id"] = $typeId;
	}

	public function getTypeId()
	{
		return $this->typeId;
	}

	public function getApiMethodName()
	{
		return "alipay.trust.user.datareceived.send";
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
