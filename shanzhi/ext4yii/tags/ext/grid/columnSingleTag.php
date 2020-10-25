<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::COLUMN);

$atts['id']=$id;
$parentTag=self::getParentTag($id);
$type=isset($atts['type'])?$atts['type']:'text';
switch ($type){
	case "date":
		$atts['xtype']='datecolumn';break;
	case "number":
		$atts['xtype']='numbercolumn';break;
	case "template":
		$atts['xtype']='templatecolumn';break;
	case "rowno":
		$atts['xtype']='rownumberer';break;
	case "check":
		$atts['xtype']='checkcolumn';break;
	case "action":
		$atts['xtype']='actioncolumn';break;
	case "text":
		$atts['xtype']='gridcolumn';break;
	case "tree":
		$atts['xtype']='treecolumn';break;
	default:
		$atts['xtype']='gridcolumn';
}
if($type == "rowno" || $type == 'check'){
	if(!isset($atts['width'])){
		$atts['width']="30";
	}
}
if(!isset($atts['align'])){
	if($type == 'number'){
		$atts['align']="right";
	}else if($type == 'tree'){
		$atts['align']="left";
	}else{
		$atts['align']="left";
	}
}
self::addTagParams($parentTag['tagId'],'columns',$atts,true);
self::addTagParams($parentTag['tagId'],'fields',$atts,true);