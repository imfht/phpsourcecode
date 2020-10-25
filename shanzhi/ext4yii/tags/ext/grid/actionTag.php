<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null);

$parentTag=self::getParentTag($id);

self::addTagParams($parentTag['tagId'],'actionDtos',$atts,true);