<?php
$tag=self::getPairTag(self::ACTIONCOLUMN);
$id=$tag['tagId'];
$atts=$tag['atts'];
$atts['id']=$id;

$parentTag=self::getParentTag($id);

$atts['xtype']='actioncolumn';
$atts['align']="left";

if(isset($tag['actionDtos'])){
	$atts['actionDtos']=$tag['actionDtos'];
}
self::addTagParams($parentTag['tagId'],'columns',$atts,true);
self::addTagParams($parentTag['tagId'],'fields',$atts,true);