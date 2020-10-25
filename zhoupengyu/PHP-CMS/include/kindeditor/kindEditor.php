<?php
class kindEditor
{
	public $InstanceName;//编辑器的名称
	public $BasePath;//编辑器文件夹所在的位置
	public $Width;//编辑器在页面上的宽度
	public $Height;//编辑器在页面上的高度
	public $Value ;//编辑器内显示的内容
	public $ToolbarSet ;//工具栏的设定（default默认--全部工具；simple简单--常用的工具; word文本--纯文本的工具及项目符号、超链接；text纯文本--文本相关工具）
	public $uploadJson;//图片与文件上传的处理程序
	function __construct( $instanceName )
	{
		$this->InstanceName	= $instanceName ;
		$this->BasePath		= '/kindeditor/' ;
		$this->Width		= '100%' ;
		$this->Height		= '200' ;
		$this->ToolbarSet	= 'default' ;
		$this->Value		= '' ;
		$this->uploadJson		= '' ;
	}
	//显示编辑器
	public function Create()
	{
		echo $this->CreateHtml() ;
	}
	//返回编辑器所需的html代码
	public function CreateHtml()
	{

		$HtmlValue = htmlspecialchars( $this->Value ) ;
		$html = "<link rel='stylesheet' href='.".$this->BasePath."themes/default/default.css' />";
		$html .= "<script charset='utf-8' src='.".$this->BasePath."kindeditor-min.js' ></script>";
		$html .= "<script charset='utf-8' src='.".$this->BasePath."lang/zh_CN.js' ></script>";
		$html .= "<script type='text/javascript' >var editor;";
// 		if($this->ToolbarSet=="default")
// 		{
// 			$html .= "$(function(){editor = KindEditor.create('textarea[name=\"".$this->InstanceName."\"]',{id:'".$this->InstanceName."',uploadJson:'".$this->uploadJson."',allowFileManager:true,filterMode: false});});";
// 		}else{
// 			$html .= "$(function(){editor = KindEditor.create('textarea[name=\"".$this->InstanceName."\"]',{";
// 			$html .= "id:'".$this->InstanceName."',uploadJson:'".$this->uploadJson."',resizeType:1,allowPreviewEmoticons:false,items:[";
// 			if($this->ToolbarSet=="simple")
// 			{
// 				$html .= "'source', '|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',	'insertunorderedlist', '|', 'emoticons', 'image', 'link'";
// 			}elseif($this->ToolbarSet=="word"){
// 				$html .= "'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',	'insertunorderedlist', '|','link'";
// 			}elseif($this->ToolbarSet=="text"){
// 				$html .= "'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat'";
// 			}	
// 			$html .= "]});});";
// 		}

		$html .= "$(function(){editor = KindEditor.create('textarea[name=\"".$this->InstanceName."\"]',{";
		$html .= "id:'".$this->InstanceName."',filterMode: false,uploadJson:'".$this->uploadJson."'";
		//$html .= "'source', '|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',	'insertunorderedlist', '|', 'emoticons', 'image', 'link'";
		$html .= "});});";
		$html .= "</script>";
		if ( strpos( $this->Width, '%' ) === false )
				$WidthCSS = $this->Width . 'px' ;
			else
				$WidthCSS = $this->Width ;

			if ( strpos( $this->Height, '%' ) === false )
				$HeightCSS = $this->Height . 'px' ;
			else
				$HeightCSS = $this->Height ;
		$html .= "<textarea name='".$this->InstanceName."' style='width:$WidthCSS;height:$HeightCSS;visibility:hidden;' >$HtmlValue</textarea>";
		return $html;
	}
}
?>