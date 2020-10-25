<?php
class Pager
{
	private $cPage;
	private $total;
	private $pageSize;
	private $totalPageNo;
	private $rootpath;
	private $anchor;
	
	function Pager($CPage,$Total,$PageSize=9,$rootpath,$anchor)
	{
		$CPage 			= $CPage<1?1:$CPage;
		$PageSize 		= $PageSize<1?1:$PageSize;

		$this->cPage 	= $CPage;
		$this->total 	= $Total;
		$this->pageSize = $PageSize;
		$this->accountTotalPageNo();
		$this->rootpath = $rootpath;
		$this->anchor = $anchor;
		 
	}
	private function nextNo()
	{
		return $this->cPage+1;
	}
	private function prvNo()
	{
		return $this->cPage-1;
	}
	private function lastNo()
	{
		return $this->totalPageNo;
	}
	private function fristNo()
	{
		return 1;
	}
	private function accountTotalPageNo()
	{
		$this->totalPageNo = $this->total%$this->pageSize>0?(int)($this->total/$this->pageSize)+1:(int)($this->total/$this->pageSize);
	}
	public function totalPage()
	{
		return $this->totalPageNo;
	}
	public function Show($url,$style=0)
	{
		global $request;
		if($style==0)
		{
			if(URLREWRITE 
			&& ($request['m'] != 'system' && substr($_SERVER['REQUEST_URI'],0,8) != '/admini/') 
			|| ($request['m'] = 'system' &&  substr($_SERVER['REQUEST_URI'],0,44) == '/admini/index.php?m=system&s=html&a=contorl&')
			||($request['m'] = 'system' && substr($_SERVER['REQUEST_URI'],0,48) == '/admini/index.php?m=system&s=html&a=aKeyContorl&') )
			{
				$tempStr = 	$this->cPage.'/'.$this->totalPageNo.'页 共'.$this->total.'条 ';
				$tempStr .= '<a href="'.$this->rootpath.$url.$this->fristNo().'/'.$this->anchor.'">首页</a> ';
				$tempStr .=	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().'/'.$this->anchor.'">前一页</a> ';
				$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$this->rootpath.$url.$this->nextNo().'/'.$this->anchor.'">下一页</a> ';
				$tempStr .= '<a href="'.$this->rootpath.$url.$this->lastNo().'/'.$this->anchor.'">尾页</a> ';
				$tempStr .= '跳转至<select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.'/'.$this->anchor.'\'";>';
				for($i=1;$i<$this->totalPageNo+1;$i++)
				{
					$tempStr .= '<option value="'.$i.'"';
					$tempStr .= $i==$this->cPage?' selected="selected"':'';
					$tempStr .= '>'.$i.'</option>';
				}
				$tempStr .= '</select>页';
			}
			else
			{
				$tempStr = 	$this->cPage.'/'.$this->totalPageNo.'页 共'.$this->total.'条 ';
				$tempStr .= '<a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'">首页</a> ';
				$tempStr .=	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">前一页</a> ';
				$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">下一页</a> ';
				$tempStr .= '<a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'">尾页</a> ';
				$tempStr .= '跳转至<select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.$this->anchor.'\'";>';
				for($i=1;$i<$this->totalPageNo+1;$i++)
				{
					$tempStr .= '<option value="'.$i.'"';
					$tempStr .= $i==$this->cPage?' selected="selected"':'';
					$tempStr .= '>'.$i.'</option>';
				}
				$tempStr .= '</select>页';
			}
		}
		elseif($style==1)
		{
			$tpageNum=8;
			$tempStr ='<ul id="apartPage">';
			$tempStr .=	$this->prvNo()<1?'':'<li><a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">前一页</a></li>';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?"<li class='pagebarCurrent'>$i</li>":'<li><a href="'.$this->rootpath.$url.$i.'">'.$i.$this->anchor.'</a></li>';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<li><a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">下一页</a></li>';
			$tempStr .='</ul>';
		}
		elseif($style==2)
		{
			$tpageNum=8;
			$tempStr ='';
			$tempStr .=	$this->prvNo()<1?'':' <a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'">首页</a> ';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?" $i ":' <a href="'.$this->rootpath.$url.$i.'">'.$i.$this->anchor.'</a> ';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':' <a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'">末页</a> ';
			$tempStr .=' 共'.$this->totalPageNo.'页 共'.$this->total.'条记录 ';
		}
		else
		{
		$tempStr =	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'"><< 前一页</a> ';
		$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$url.$this->nextNo().$this->anchor.'">下一页 >></a>';
		}
		
		return $tempStr;
		
	}
	public function enShow($url,$style=0)
	{
		if($style==0)
		{
			$tempStr =	$this->prvNo()<1?'Previous Page':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">Previous Page</a>';
			$tempStr .= ' [ Page <select name="pagerMenu" onChange="location=\''.$this->rootpath.$url.'\'+this.options[this.selectedIndex].value+\''.$this->anchor.'\'";>';
			for($i=1;$i<$this->totalPageNo+1;$i++)
			{
				$tempStr .= '<option value="'.$i.'"';
				$tempStr .= $i==$this->cPage?' selected="selected"':'';
				$tempStr .= '>'.$i.'</option>';
			}
			$tempStr .= '</select> of '.$this->totalPageNo.' ]  ';
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'Next Page':'<a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">Next Page</a> ';
		}
		elseif($style==1)
		{
			$tpageNum=8;
			$tempStr ='<ul id="apartPage">';
			$tempStr .=	$this->prvNo()<1?'':'<li><a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'">Previous Page</a></li>';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?"<li class='pagebarCurrent'>$i</li>":'<li><a href="'.$this->rootpath.$url.$i.$this->anchor.'">'.$i.'</a></li>';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<li><a href="'.$this->rootpath.$url.$this->nextNo().$this->anchor.'">Next Page</a></li>';
			$tempStr .='</ul>';
		}
		elseif($style==2)
		{
			$tpageNum=8;
			$tempStr ='';
			$tempStr .=	$this->prvNo()<1?'':' <a href="'.$this->rootpath.$url.$this->fristNo().$this->anchor.'">First</a> ';
			if($this->cPage<$tpageNum/2)
			{
				$tstart=1;
				$tend=$tpageNum+1;
			}
			else 
			{
				$tstart=$this->cPage-$tpageNum/2;
				$tend=$this->cPage+$tpageNum/2;	
			}
			$tstart=$tstart<1?1:$tstart;
			$tend=$tend>$this->totalPageNo?$this->totalPageNo:$tend;
			
			for($i=$tstart;$i<$tend+1;$i++)
			{
				$tempStr .= $this->cPage==$i?" $i ":' <a href="'.$this->rootpath.$url.$i.$this->anchor.'">'.$i.'</a> ';
			}
			$tempStr .=	$this->nextNo()>$this->totalPageNo?'':' <a href="'.$this->rootpath.$url.$this->lastNo().$this->anchor.'">Last</a> ';
		}
		else
		{
		$tempStr =	$this->prvNo()<1?'':'<a href="'.$this->rootpath.$url.$this->prvNo().$this->anchor.'"><< Previous Page</a> ';
		$tempStr .=	$this->nextNo()>$this->totalPageNo?'':'<a href="'.$url.$this->nextNo().$this->anchor.'">Next Page >></a>';
		}
		
		return $tempStr;
		
	}
	public function RecordStart()
	{
		return ($this->cPage-1)*$this->pageSize;
	}
	public function RecordSize()
	{
		return $this->pageSize;
	}
}
/**
 * 建立一个MySql的数据源，内置分页需要 Pager 类支持
 * @author doccms<service@doccms.com>
 * @version 1.120925
 * @copyright doccms
 */
class sqlbuilder
{
	public $sql_out;
	public $results;
	public $pager;
	public $name;
	public $rootpath;
	
	private $sql;
	private $order;
	private $anchor;
	function __construct($name,$sql,$order,$db,$pagesize=10,$paging=true,$rootpath='./index.php',$anchor='')
	{
		$this->db=$db;
		$this->sql=$sql;
		$this->name=$name;
		$this->rootpath=$rootpath;
		$this->anchor=$anchor;
		
		$torder = $this->get_str($_GET[$name.'o']);
		$tpage = intval($this->get_str($_GET[$name.'p']));//xss 
		$order=empty($torder)?$order:$torder;
		
		//print_r($_GET);
		$this->pager = new Pager($tpage,$this->get_count(),$pagesize,$this->rootpath,$this->anchor);
		
		if($paging)
		{
			$sql=$sql.' order by '.$this->prase_order($order).' limit '.$this->pager->RecordStart().','.$this->pager->RecordSize();
		}
		else {
			$sql=$sql.' order by '.$this->prase_order($order);
		}
		$this->results = $this->db->get_results($sql,ARRAY_A);	
	}
	private function prase_order($order)
	{
		$orderarr=explode('|',$order);
		if(count($orderarr)>1)
		{
			if(((int)$orderarr[1])==0)
			{
				return $orderarr[0];
			}
			else 
			{
				return $orderarr[0].' desc';
			}
		}
		else 
		return $order;
	}
	public function get_pager_show()
	{
		return $this->pager->Show($this->build_url());
	}
	public function get_en_pager_show()
	{
		return $this->pager->enShow($this->build_url());
	}
	public function totalPageNo()
	{
		return $this->pager->totalPage($this->build_url());
	}
	private function get_str($string)
	{
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		}
		return $string;
	}
	private function build_url()
	{
		global $request;
		foreach ($_GET as $k=>$v)
		{
			$_GET[$k]=RemoveXSS($v);
		}
		$urlstr='';
		//print_r($_GET);exit;
		// /admini/index.php?m=system&s=html&a=contorl&
		//$_SERVER['REQUEST_URI'];
		if(URLREWRITE && $request['m'] != 'system' && substr($_SERVER['REQUEST_URI'],0,8) != '/admini/')
		{
			foreach ($_GET as $k=>$v)
			{
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					$urlstr.=$v.'/';
				}
			}
		}elseif(URLREWRITE && substr($_SERVER['REQUEST_URI'],0,44) == '/admini/index.php?m=system&s=html&a=contorl&' )
		{//静态化生成处理带分页
			global $pfileName;
			foreach ($_GET as $k=>$v)
			{
				
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					if(strtoupper($k)==strtoupper('p')){
						$urlstr.=$pfileName.'/';	
					}
					if(strtoupper($k)==strtoupper('c')){
						$urlstr.=$v.'/';
						
					}
				}
			}
		}
		elseif(URLREWRITE && substr($_SERVER['REQUEST_URI'],0,48) == '/admini/index.php?m=system&s=html&a=aKeyContorl&' )
		{//静态化生成处理带分页
			global $pfileName;
			//print_r($_GET);
			foreach ($_GET as $k=>$v)
			{
				
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					if(strtoupper($k)==strtoupper('p')){
						$urlstr.=$pfileName.'/';	
					}
					if(strtoupper($k)==strtoupper('c')){
						$urlstr.=$v.'/';
						
					}
				}
			}
		}
		else
		{
			$urlstr='?';
			foreach ($_GET as $k=>$v)
			{
				if(strtoupper($k)!=strtoupper($this->name.'p'))
				{
					$urlstr.=$k.'='.$v.'&';
				}
			}
			$urlstr.=$this->name.'p=';
		}
		
		return $urlstr;
	}
	private function get_count()
	{
		$tempArr = explode(' union ',strtolower($this->sql));
		$count = count($tempArr);
		$result = 0;
		if($count>0)
		{
			for($i=0;$i<$count;$i++)
			{
				$result = $result + count($this->db->get_results($tempArr[$i]));
			}
		}
		else
		{
			$tempArr = null;
			$tempArr = explode(' from ',strtolower($this->sql));
			$count = count($tempArr);
			if($count>0)
			{
				$tempSqlStr = 'SELECT COUNT(*) FROM ';
				for($i=1;$i<$count;$i++)
				{
					if($i != $count-1)
					$tempSqlStr .= $tempArr[$i].' from ';
					else
					$tempSqlStr .= $tempArr[$i];
				}
			}
			$result = $this->db->get_var($tempSqlStr);	
		}
		return $result;
	}
}