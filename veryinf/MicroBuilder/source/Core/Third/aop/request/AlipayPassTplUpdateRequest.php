<?php
/**
 * ALIPAY API: alipay.pass.tpl.update request
 *
 * @author auto create
 * @since 1.0, 2014-06-12 17:16:03
 */
class AlipayPassTplUpdateRequest
{
	/** 
	 * 模版内容
	 **/
	private $tplContent;
	
	/** 
	 * 模版ID
	 **/
	private $tplId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	
	public function setTplContent($tplContent)
	{
		$this->tplContent = $tplContent;
		$this->apiParas["tpl_content"] = $tplContent;
	}

	public function getTplContent()
	{
		return $this->tplContent;
	}

	public function setTplId($tplId)
	{
		$this->tplId = $tplId;
		$this->apiParas["tpl_id"] = $tplId;
	}

	public function getTplId()
	{
		return $this->tplId;
	}

	public function getApiMethodName()
	{
		return "alipay.pass.tpl.update";
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
