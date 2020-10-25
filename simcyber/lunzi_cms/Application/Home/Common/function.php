<?php
function set_session($in){
	foreach($in as $k=>$v){
		$_SESSION[$k]=$v;
	}
}

function bitian(){
	$str='<span style="color:#F00">*</span>';
	return $str;
}

function show_post_info($body){
	$body=htmlspecialchars_decode(htmlspecialchars_decode($body));
	$body=strip_tags($body);
	$body=mb_substr($body,0,160,'utf-8');
	return $body."...";
}

function get_previous($bid){
	$sql="`bid`<".$bid;
	$rs=M('lz_infos')->where($sql)->order("bid desc")->find();
	if($rs){
		$url='<li class="previous"><a href="'.U('Index/view_info',array('bid'=>$rs['bid'])).'">'.$rs['title'].'</a></li>';
	}else{
		$url='<li class="previous disabled"><a href="#">已经是第一篇</a></li>';
	}
	return $url;
}

function get_next($bid){
	$sql="`bid`>".$bid;
	$rs=M('lz_infos')->where($sql)->order("bid asc")->find();
	if($rs){
		$url='<li class="next"><a href="'.U('Index/view_info',array('bid'=>$rs['bid'])).'">'.$rs['title'].'</a></li>';
	}else{
		$url='<li class="next disabled"><a href="#">已经是最后一篇</a></li>';
	}
	return $url;
}

function show_hit($hit){
	if($hit<1000){
		return $hit;
	}else{
		$hit=round($hit/1000,2);
		return $hit."k";
	}
}

function set_tag_info($bid,$tid,$title){
	$con['tid']=$tid;
	$rs=M('lz_tags')->where($con)->find();
	if($rs['infos']){
		$tmp=json_decode($rs['infos'],true);
		if(count($tmp)>=3){
			unset($tmp[0]);
			$tmp[]['bid']=$bid."@@@".$title;
		}else{
			$tmp[]['bid']=$bid."@@@".$title;
		}
	}else{
		$tmp[]['bid']=$bid."@@@".$title;
	}
	
	$r['infos']=json_encode($tmp);
	M('lz_tags')->where($con)->save($r);
}

function set_active($type1,$type2,$default){
	if($type2){
		if($type1==$type2){
			return "btn-info";
		}
	}elseif($default){
		return "btn-info";
	}
	return "";
}

function get_url($bid,$url){
	if($url){
		return U('Index/goto_url',array('bid'=>$bid));
	}else{
		return U('Index/view_info',array('bid'=>$bid));
	}
}

function is_selected($in1,$in2){
	if($in1==$in2){
		return ' selected="selected"';
	}else{
		return '';
	}
}
