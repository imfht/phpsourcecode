<?php
function tags($tag=''){
	if(empty($tag)) return '';
	$home_tag=F('HomeTag');
	if(!$home_tag){
		$home_tag=M('Tag')->getField('id,title',true);
		F('HomeTag',$home_tag);
	}

	$result='';
	if(strpos($tag,',') !== false){
		$tags=explode(',', $tag);
		foreach ($tags as $v) {
			isset($home_tag[$v]) && $result.='<a href="'.U('Search/tag/'.$v).'">'.$home_tag[$v].'</a>';
		}
	}else{
		isset($home_tag[$tag]) && $result.='<a href="'.U('Search/tag/'.$tag).'">'.$home_tag[$tag].'</a>';
	}
	return $result;
}