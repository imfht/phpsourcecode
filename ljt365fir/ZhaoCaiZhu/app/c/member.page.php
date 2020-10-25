<?php
/**
 * 会员管理
 * @author 齐迹  email:smpss2012@gmail.com
 *
 */
class c_member extends base_c {
	
	function __construct($inPath) {
		parent::__construct ();
		if (self::isLogin () === false) {
			$this->ShowMsg ( "请先登录！", $this->createUrl ( "/main/index" ) );
		}
		if (self::checkRights ( $inPath ) === false) {
			//$this->ShowMsg("您无权操作！",$this->createUrl("/system/index"));
		}
		$this->params ['inpath'] = $inPath;
		$this->params ['head_title'] = "会员管理-" . $this->params ['head_title'];
	}
	
	function pageindex($inPath) {
		$url = $this->getUrlParams ( $inPath );
		$page = $url ['page'] ? ( int ) $url ['page'] : 1;
		$memberObj = new m_member ();
		$condition = '';
		$key = base_Utils::shtmlspecialchars ($_POST ['key'] );
		if ($key) {
			$condition = "membercardid like '%{$key}%' or realname like '%{$key}%' or mobile like '%{$key}%' or phone like '%{$key}%'";
			$this->params ['key'] = $key;
			$getPath='?key='.$key;
		}
		$memberObj->setCount ( true );
		$memberObj->setPage ( $page );
		$memberObj->setLimit ( base_Constant::PAGE_SIZE );
		$member = $memberObj->select ( $condition, '', '', "order by credit desc" );
		$this->params ['member'] = $member->items;
		$this->params ['pagebar'] = $this->PageBar ( $member->totalSize, base_Constant::PAGE_SIZE, $page, $inPath , 'style1',$getPath );
		return $this->render ( 'member/index.html', $this->params );
	}
	
	function pageajaxcard($inPath){
	$url = $this->getUrlParams ( $inPath );
		$memberObj = new m_member ();
		$condition = '';
		$key =$url['key'];
		if ($key) {
			$condition = "membercardid like '%{$key}%' or realname like '%{$key}%' or mobile like '%{$key}%' or cardid like '%{$key}%'";
		}
		$memberObj->setLimit (6);
		$member = $memberObj->select ( $condition, '', '', "order by credit desc" );
		$list=$member->items;
		$temp = array_filter($list) ;
         if(!empty($temp)){
			$error='';
		foreach ($list as $v){			
         $error.='<li><p>'.$v['realname'].' <small>'.$v['mobile'].' </small></p><span>'.$v['membercardid'].' </span></li>';
			}
		}else{
			$error='<li class="red">没有找到数据！</li>';
			}
			echo $error;
	}
	
	function pagegroup($inPath){
		$url = $this->getUrlParams ( $inPath );
		$mbgroupObj = new m_mbgroup ($url['mgid']);
		if($_POST){
			$data = base_Utils::shtmlspecialchars ( $_POST );
			$rs = $mbgroupObj -> creat($data);
			if($rs){
				$this->showMsg("操作成功",$this->createUrl ( "/member/group" ),2,1);
			}else{
				$this->showMsg("操作失败！".$mbgroupObj->getError());
			}
		}
		$this -> params ['groupone'] = $mbgroupObj->get();
		$this -> params ['group'] = $mbgroupObj->select()->items;
		return $this->render ( 'member/group.html', $this->params );
	}
	
    function pagememberinfo($inPath) {
		$url = $this->getUrlParams ( $inPath );
		$mid = ( int ) $url ['mid'] > 0 ? ( int ) $url ['mid'] : ( int ) $_POST ['mid'];
		$memberObj = new m_member ( $mid );
		$this -> params ['member'] = $memberObj->get ();
		$page = $url ['page'] ? ( int ) $url ['page'] : 1;
		$ymd = date ( "Y-m-d", time () );
		$condi = " mid ='{$mid}' ";
		$ac=isset($url['ac'])?$url['ac']:'';
		if($ac=='rog'){$condi.=" and sales_type='1' ";}else{$condi.=" and sales_type='0' ";}
		if ($_POST) {
			$post = base_Utils::shtmlspecialchars ( $_POST );
			$key = base_Utils::getStr ( $_POST ['key'] );
			$stime = base_Utils::getStr ( $_POST ['stime'] );
			$etime = base_Utils::getStr ( $_POST ['etime'] );
			if ($key) {
				$condi .= "and order_id like '%{$key}%' or goods_name like '%{$key}%' or realname like '%{$key}%'";
			}
			if ($stime) {
				$etime = $etime ? $etime : $ymd;
				$condi = $condi ? $condi .' and ': "";
				$condi .= " dateymd between '{$stime}' and '{$etime}'";
			}
			$getPath='?key='.$key.'&stime='.$stime.'&etime='.$etime;
		}
		$saleObj = new m_sales ();
		$saleObj->setCount ( true );
		$saleObj->setPage ( $page );
		$saleObj->setLimit ( base_Constant::PAGE_SIZE );
		$rs = $saleObj->select ( $condi, "", "", " order by sid desc" );
		$m_cacheObj = new m_membercache($mid);
		$this -> params ['mid']=$mid ;
		$this -> params ['ac']=$ac ;
		$this->params ['sales'] = $rs->items;
		$this->params ['key'] = $key;
		$this->params ['stime'] = $stime;
		$this->params ['etime'] = $etime;
		$this->params ['tormb'] = base_Constant::TORMB;
		$this->params ['m_cache'] =$m_cacheObj->get();
		$this->params ['pagebar'] = $this->PageBar ( $rs->totalSize, base_Constant::PAGE_SIZE, $page, $inPath  , 'style1',$getPath );
		return $this->render ( 'member/memberinfo.html', $this->params );

    }
	function pageexlog($inPath) {
		$url = $this->getUrlParams ( $inPath );
		$mid = ( int ) $url ['mid'] > 0 ? ( int ) $url ['mid'] : ( int ) $_POST ['mid'];
		$memberObj = new m_member ( $mid );
		$this -> params ['member'] = $memberObj->get ();
		$page = $url ['page'] ? ( int ) $url ['page'] : 1;
		$condi = " mid ='{$mid}' ";
		if ($_POST) {
			$post = base_Utils::shtmlspecialchars ( $_POST );
			$stime = strtotime(base_Utils::getStr ( $_POST ['stime'] ));
			$etime = strtotime(base_Utils::getStr ( $_POST ['etime'] ));
			if ($stime) {
				$etime = $etime ? $etime : $ymd;
				$condi = $condi ? $condi .' and ': "";
				$condi .= " extime between '{$stime}' and '{$etime}'";
			}
		}
		$m_excredit = new m_excredit ();
		$m_excredit->setCount ( true );
		$m_excredit->setPage ( $page );
		$m_excredit->setLimit ( base_Constant::PAGE_SIZE );
		$rs = $m_excredit->select ( $condi, "", "", " order by exid desc" );
		$m_cacheObj = new m_membercache($mid);
		$this -> params ['mid']=$mid ;
		$this -> params ['ac']='ex' ;
		$this->params ['sales'] = $rs->items;
		$this->params ['key'] = $key;
		$this->params ['stime'] = $stime;
		$this->params ['etime'] = $etime;
		$this->params ['tormb'] = base_Constant::TORMB;
		$this->params ['m_cache'] =$m_cacheObj->get();
		$this->params ['pagebar'] = $this->PageBar ( $rs->totalSize, base_Constant::PAGE_SIZE, $page, $inPath );
		return $this->render ( 'member/memberinfo.html', $this->params );
	}
	
	function pageaddmember($inPath) {
		$url = $this->getUrlParams ( $inPath );
		$mid = ( int ) $url ['mid'] > 0 ? ( int ) $url ['mid'] : ( int ) $_POST ['mid'];
		$memberObj = new m_member ( $mid );
		if ($_POST) {
			$post = base_Utils::shtmlspecialchars ( $_POST );
			if ($mid) {
				if ($memberObj->create ( $post )) {
					$this->ShowMsg ( "修改成功！", $this->createUrl ( "/member/index" ), '', 1 );
				}
				$this->ShowMsg ( "修改失败" . $memberObj->getError () );
			} else {
				if ($memberObj->create ( $post )) {
					$m_mid=$memberObj->get("membercardid='{$post['membercardid']}'");
					$m_cacheObj = new m_membercache();
					$m_cacheObj->insertcache($m_mid['mid']);
					$this->ShowMsg ( "添加成功！", $this->createUrl ( "/member/index" ), 2, 1 );
				}
				$this->ShowMsg ( "添加失败，原因：" . $memberObj->getError () );
			}
		} else {
			if ($mid) {
				$this->params ['member'] = $memberObj->get ();
			}
			$mbgroupObj = new m_mbgroup ();
			$this -> params ['group'] = $mbgroupObj->select()->items;
			return $this->render ( 'member/addmember.html', $this->params );
		}
	}
	function pageeditmember($inPath){
		$url = $this->getUrlParams ( $inPath );
		$mid = ( int ) $url ['mid'] > 0 ? ( int ) $url ['mid'] : ( int ) $_POST ['mid'];
		$memberObj = new m_member ( $mid );
		if ($_POST) {
			$post = base_Utils::shtmlspecialchars ( $_POST );
			if ($mid) {
				if ($memberObj->create ( $post )) {
					$this->ShowMsg ( "修改成功！", $this->createUrl ( "/member/memberinfo-mid-$mid.html" ), '', 1 );
				}
				$this->ShowMsg ( "修改失败" . $memberObj->getError () );
			} 
		} else {
			if ($mid) {
				$this->params ['member'] = $memberObj->get ();
			}
			$mbgroupObj = new m_mbgroup ();
			$this -> params ['group'] = $mbgroupObj->select()->items;
			$this -> params ['mid'] =$mid;
			return $this->render ( 'member/editmember.html', $this->params );
		}

	}
	function pageajaxex($inPath){
		$url = $this->getUrlParams ( $inPath );
		$mid = ( int ) $url ['mid'] > 0 ? ( int ) $url ['mid'] : ( int ) $_POST ['mid'];
		if ($_POST) {$post = base_Utils::shtmlspecialchars ( $_POST );
		$memberObj = new m_member ( $mid );
		$credit=$memberObj->get ('credit');
		if($credit>$post['excredit']){
			$memberObj->setexCredit ($mid,$post['excredit']);
			$m_excredit = new m_excredit();	
			$arr_ex['excredit']=(float)$post['excredit'];	
			$arr_ex['exrmb']=$post['excredit']*base_Constant::TORMB;	
			$rs=$m_excredit->insertlog($mid,$arr_ex);
			$m_cacheObj = new m_membercache($mid);		
			$rs=$m_cacheObj ->setexCredit($mid,$post['excredit']);
			if($rs){echo('成功兑换'.$post['excredit'].'分！');}else{echo('积分兑换失败！');}
			}else{
				echo('积分值不够！');
				}
		}
	}
	
}
?>