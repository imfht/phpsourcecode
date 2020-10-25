<?php
function product_category($id=0){
  global $menus,$subs;  
  if(!isset($subs[$id])) return ; //没有子类,返回空;
  		   
   foreach($subs[$id] as $sid){  
	  $result.=','.$menus[$sid]['id'].product_category($sid);  //递归	
   } 
   return $result; 
}
function index()
{
	global $db;
	global $request;
	global $params;
	global $tag;	// 标签数组
	global $menus,$subs;
	$sql="SELECT * FROM `".TB_PREFIX."product` WHERE `channelId` IN (".$params['id'].product_category($params['id']).") OR INSTR(REPLACE(CONCAT(\"'\",categoryId,\"'\"),\",\",\"','\"),\"'{$params['id']}'\")>0";
    $sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,productCount,true,URLREWRITE?'/':'./');
    if(!empty($sb->results))
    {
		foreach($sb->results as $k =>$v)
	    {
	    	if(URLREWRITE)$sb->results[$k]['menuName'] = sys_menu_info('menuName',false,$v['channelId']);//当前数据隶属栏目的menuName
	    	$sb->results[$k]['indexPic']= ispic($v['indexPic']);
			$sb->results[$k]['originalPic'] = ispic($v['originalPic']);
			$sb->results[$k]['middlePic']	= ispic($v['middlePic']);
			$sb->results[$k]['smallPic']	= ispic($v['smallPic']);
	    }
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
function getcontent($p,$r,$page,$data){
   if($data->hassplitpages==1){
   	   if($page==0)$page=1;
	   $article=explode('{#page#}',$data->content);
	   $pagenum=count($article);
	   if($pagenum<$page){
	   	 $result['navbar']='';
         $result['content']='超出分页范围,非法请求！';
	   }else{
	       $result['content']=$article[$page-1];
	       $result['navbar']=pages_nav($pagenum,$p,$r,$page);	//分页导航
	   }
   }else{
        $result['navbar']='';
        $result['content']=$data->content;
   }
    return $result;
} 
function view()
{
	global $db;
	global $request;
	global $params;
	global $tag;
	$sql='UPDATE '.TB_PREFIX.'product SET counts=counts+1 WHERE id='.$params['args'];
	$db->query($sql);
	
	$sql='SELECT * FROM '.TB_PREFIX.'product WHERE id='.$params['args'];
	$product = $db->get_row($sql);

	$result=getcontent($params['id'],$params['args'],$params['cid'],$product);
	$product->content = $result['content']; 
	$product->navbar = $result['navbar'];

	$tag['data.row']=(array)$product;
}
function intobasket(){//加入购物车
	//验证参数
	//检测cookie 显示已加入购物车的产品 和 是否补全用户的信息
	global $db,$request,$tag;
	$id=intval($request['r']);
	if(!isset($_SESSION[TB_PREFIX.'user_ID'])){
		$basket=array('customer'=>'',
					'm_tel'=>'',
					'address'=>'',
					'user_type'=>'0',
					'user_id'=>'0',
					'user_role'=>'0',
					'productinfo'=>array()
				);
	}else{
		$rs=$db->get_row("SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID']);
		$basket=array('customer'=>$rs->name,
					'm_tel'=>$rs->mtel,
					'address'=>$rs->address,
					'user_type'=>'1',
					'user_id'=>$_SESSION[TB_PREFIX.'user_ID'],
					'user_role'=>$_SESSION[TB_PREFIX.'user_roleId'],
					'productinfo'=>array()
				);
	}		 
	if(empty($_COOKIE['doc_basket'])){			
		
	}else{
		$basket=parseCookie($_COOKIE['doc_basket']);	
	}
	if(empty($basket['productinfo'][$id])){//cookie无此数据
		$rs=$db->get_row("SELECT * FROM ".TB_PREFIX."product WHERE id=".$id);
		$basket['productinfo'][$rs->id]=array('id'=>$rs->id, 'channelId'=>$rs->channelId, 'title'=>$rs->title, 'smallPic'=>$rs->smallPic, 'num'=>1, 'preferPrice'=>$rs->preferPrice );
	}else{
		$basket['productinfo'][$id]['num']+=1;
	}
	encryCookie($basket);

	redirect(sys_href($request['p'],'product_basket'));
}
function basket(){
	global $basket;
	$basket=parseCookie($_COOKIE['doc_basket']);
}

/*update cookie start*/
function updatebasketfordel(){
	global $request,$basket;
	$id=intval($request['r']);
	$basket=parseCookie($_COOKIE['doc_basket']);
	unset( $basket['productinfo'][$id] );
	if(empty($basket['productinfo'])){
		echo '{"flag":"0","num":"0","price":"0"}';
	}else{
		foreach($basket['productinfo'] as $k=>$v){	
		$num+=$v['num'];
		$price+=$v['preferPrice']*$v['num'];
		}
		echo '{"flag":"1", "num":"'.$num.'","price":"'.$price.'"}';
	}
	encryCookie($basket);
	exit;
}
function updatebasketfornum(){
	global $request,$basket;
	$id=intval($request['r']);
	$num=intval($request['num']);
	$basket=parseCookie($_COOKIE['doc_basket']);
	$basket['productinfo'][$id]['num']=$num;
	
	$curprice+=$basket['productinfo'][$id]['preferPrice']*$num;
	$num=0;
	foreach($basket['productinfo'] as $k=>$v){	
		$num+=$v['num'];
		$price+=$v['preferPrice']*$v['num'];
	}
	encryCookie($basket);
	echo '{"curprice":"'.$curprice.'","num":"'.$num.'","price":"'.$price.'"}';
	exit;
}
function updatebasketforcustomer(){
	global $request,$basket;
	$customer=$request['customer'];//验证
	$basket=updatebasket('customer',$customer);
	exit;
}
function updatebasketformtel(){
	global $request,$basket;
	$m_tel=$request['m_tel'];//验证
	$basket=updatebasket('m_tel',$m_tel);
	exit;
}
function updatebasketforaddress(){
	global $request,$basket;
	$address=$request['address'];//验证
	$basket=updatebasket('address',$address);
	exit;
}
	//......
/*update cookie end*/
function basketqr(){
	global $basket;
	$basket=parseCookie($_COOKIE['doc_basket']);
}
function submitbasket(){
	//到数据库
	global $db,$request;
	$basket=parseCookie($_COOKIE['doc_basket']);
	if(empty($basket['customer'])&& empty($_SESSION[TB_PREFIX.'product'])){
		exit('<script>alert("请填写您的个人信息！");location.href="javascript:history.go(-1)";</script>');
	}
	
	foreach($basket['productinfo'] as $k=>$v){
		$subject[] =$v['title'];
		$price[]   =$v['preferPrice'];
		$body[]    =$v['title'].' * '.$v['num'];
	}
	$subject = @implode('<@>',$subject);
	$price   = @implode('<@>',$price);
	$body    = @implode('<@>',$body);
	
	//构造支付数据
	if((PAY_ISPAY || PAY_ISPAY_TEN) && (empty($_SESSION[TB_PREFIX.'pay_orderId']) || $_SESSION[TB_PREFIX.'pay_subject'] != $subject))
	{
		$_SESSION[TB_PREFIX.'pay_orderId'] = date("Ymd").date("His").rand(1000, 9999);
		$_SESSION[TB_PREFIX.'pay_subject'] = $subject;
		$_SESSION[TB_PREFIX.'pay_body']    = $body;
		$_SESSION[TB_PREFIX.'pay_price']   = $price;
	}
	//入库信息
	if(!empty($basket['productinfo'])){
		$sql="INSERT INTO `".TB_PREFIX."product_order` ( `orderId` ,`usertype` ,`userid` , `customer` , `m_tel` , `address` , `orederinfo`, `dtTime` )
			VALUES ('".$_SESSION[TB_PREFIX.'pay_orderId']."' ,'".$basket['user_type']."', '".$_SESSION[TB_PREFIX.'user_ID']."', '".$basket['customer']."', '".$basket['m_tel']."', '".$basket['address']."', '".serialize($basket['productinfo'])."', '".date('Y-m-d H:i:s')."');";
		$db->query($sql);
	
		if(orderISON)
		{
			sys_mail(' 订单提醒','最新订单提醒：您的网站：<a href="http://'.WEBURL.'">'.WEBURL.'</a> 有最新订单，订单内容——'.strtr($body,'<@>','   ').'，请及时前往查看！');
		}
		//清空
		$basket['productinfo']=array();
		encryCookie($basket);
	}
}

function updatebasket($k,$v){
	$cookie=parseCookie($_COOKIE['doc_basket']);
	$cookie[$k]=$v;
	encryCookie($cookie);
	return $cookie;
}
function pages_nav($pagenum,$p,$r,$cpage)
{
	global $tag;	
	if($cpage==0)$cpage=1;
	
	if(URLREWRITE)
	{
		if($cpage==1)
		$navbar.='<span class="s1 s3">上一页</span>';
		else
		$navbar.='<a href="/'.$tag['channel.menuname'].'/product_'.$r.'.html/'.(intval($cpage)-1).'" target="_self" class="s1">上一页</a>';
	}
	else
	{
		if($cpage==1)
		$navbar.='<span class="s1 s3">上一页</span>';
		else
		$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.(intval($cpage)-1).'" target="_self" class="s1">上一页</a>';
	}
	for($c=1;$c<=$pagenum;$c++)
	{
		if(URLREWRITE)
		{
			if($c == $cpage)
			$navbar.='<a href="/'.$tag['channel.menuname'].'/product_'.$r.'.html/'.$c.'" target="_self" class="s2">'.$c.'</a>';
			else
			$navbar.='<a href="/'.$tag['channel.menuname'].'/product_'.$r.'.html/'.$c.'">'.$c.'</a>';
		}
		 else
		{
			if($c == $cpage)
			$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.$c.'" target="_self" class="s2">'.$c.'</a>';
			else
			$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.$c.'">'.$c.'</a>';
		}
	}
	if(URLREWRITE)
	{
		if($cpage==$pagenum)
		$navbar.='<span class="s1 s3">下一页</span>';
		else
		$navbar.='<a href="/'.$tag['channel.menuname'].'/product_'.$r.'.html/'.(intval($cpage)+1).'" target="_self" class="s1">下一页</a>';
	}
	else
	{
		if($cpage==$pagenum)
		$navbar.='<span class="s1 s3">下一页</span>';
		else
		$navbar.='<a href="./?p='.$p.'&a=view&r='.$r.'&c='.(intval($cpage)+1).'" target="_self" class="s1">下一页</a>';
	}
	return $navbar;
}
?>