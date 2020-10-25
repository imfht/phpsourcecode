<?php
/**
 * ALIPAY API: alipay.pass.tpl.content.update request
 *
 * @author auto create
 * @since 1.0, 2014-08-20 20:15:27
 */
class AlipayPassTplContentUpdateRequest
{
	/** 
	 * 代理商代替商户发放卡券后，再代替商户更新卡券时，此值为商户的pid/appid
	 **/
	private $channelId;
	
	/** 
	 * 支付宝pass唯一标识
	 **/
	private $serialNumber;
	
	/** 
	 * 模版动态参数信息【支付宝pass模版参数键值对JSON字符串】
	 **/
	private $tplParams;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setChannelId($channelId)
	{
		$this->channelId = $channelId;
		$this->apiParas["channel_id"] = $channelId;
	}

	public function getChannelId()
	{
		return $this->channelId;
	}

	public function setSerialNumber($serialNumber)
	{
		$this->serialNumber = $serialNumber;
		$this->apiParas["serial_number"] = $serialNumber;
	}

	public function getSerialNumber()
	{
		return $this->serialNumber;
	}

	public function setTplParams($tplParams)
	{
		$this->tplParams = $tplParams;
		$this->apiParas["tpl_params"] = $tplParams;
	}

	public function getTplParams()
	{
		return $this->tplParams;
	}

	public function getApiMethodName()
	{
		return "alipay.pass.tpl.content.update";
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
