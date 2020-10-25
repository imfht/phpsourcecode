<?php 
/**
* 最新系统推送1.9.7后
*/
class JPushChajian extends Chajian{

	
	//-------------最新原生app推送app是1.2.3版本 和 最新app+---------------
	public function push($title, $desc, $cont, $palias)
	{
		
		$alias		= $palias['alias'];
		$xmalias	= $palias['xmalias']; //小米的
		$newalias	= $palias['newalias']; //最新使用的
		$oldalias	= $palias['oldalias']; //一般自己编译
		$uids		= $palias['uids'];
		$alias2019	= $palias['alias2019'];
		$xmpush		= c('xmpush');
		
		//没有设置小米推送
		if(!$xmpush->sendbool()){
			$arr 	= array(
				'alias' 	=> join(',', $alias),
				'xmalias' 	=> join(',', $xmalias),
				'newalias' 	=> join(',', $newalias),
				'oldalias' 	=> join(',', $oldalias),
				'alias2019' => join(',', $alias2019),
				'uids'  => $uids,
				'title' => $this->rock->jm->base64encode($title),
				'cont'  => $this->rock->jm->base64encode($cont),
				'desc'  => $desc
			);
			$runurl = c('xinhu')->geturlstr('jpushplat', $arr);
			return  c('curl')->getcurl($runurl);
		}else{
			$desc = $this->rock->jm->base64decode($desc);
			$xmarr = array();//小米的人员
			$othar = array();//其他人用
			$iosar = array(); //IOS
			$hwarr = array(); //华为
			foreach($alias2019 as $ali1){
				$ali1aa = explode('|', $ali1);
				$regid  = $ali1aa[0];
				$sjlxx  = $ali1aa[1];
				if(contain($sjlxx,'xiaomi')){
					$xmarr[] = $regid;
				}else if(contain($sjlxx,'huawei')){
					if(isset($ali1aa[3]) && $ali1aa[3])$hwarr[] = $ali1aa[3];
				}else if(contain($sjlxx,'iphone')){	
					$iosar[] = $regid;
				}else{
					$othar[] = $regid;
				}
			}
			$msg = $msg1 = $msg2 = '';
			if($oldalias)$msg = $xmpush->androidsend($oldalias, $title, $desc, $cont);
			if($xmarr)$msg = $xmpush->androidsend($xmarr, $title, $desc);
			if($iosar)$msg1= $xmpush->iossend($iosar, $title, $desc);
			if($hwarr)$msg2= c('hwpush')->androidsend($hwarr, $title, $desc);
			$msg5 = $msg.$msg1.$msg2;
			return $msg5;
		}	
	}
	
	
	
	
}