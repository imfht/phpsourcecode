<?php
/**
 * @name 关键词管理
 */
class KeywordAction extends CommonAction
{
	/**
	 * @name 查看关键词
	 */
	public function lists()
	{
		import('ORG.Util.Page');
		$typeinfo = M ( 'Searchengine' );
		$keywords = M ( 'Keywords k' );
		$webinfo = M ( 'webinfo w' );
		$pageNow = I('p',1,int);
		$seid = I('seid',1,int);
		$webid = I('id',0,int);
		$sortdate = I ( 'date', strtotime ( date ( 'Y-m-d' ) ) ,int);
		$pageSize = 18;
		
		$cnname = $typeinfo->where(array('seid' => intval($seid)))->getField ( 'cnname' );
		$weburl = $webinfo->where(array('webid' =>intval($webid)))->getField ( 'weburl' );

		$field = "w.webid,w.webname,w.weburl,k.keyname,k.keyid,s.sortid,s.keysort,s.sortdate,s.keyindex,s.keyabout,se.cnname,kp.seid,kp.keyprice,cl.cost";
		$join1 = "sc_webinfo as w on w.webid = k.webid";
		$join2 = "sc_keyprice AS kp on k.keyid = kp.keyid and kp.seid = {$seid} ";
		$join3 = "sc_searchengine AS se ON kp.seid = se.seid";
		if($webid != 0)$where = array('w.webid'=>$webid);
		if(isset($_REQUEST['keyid']))
		{
			$join4 = "sc_sort as s on s.keyid = k.keyid ";
			$where['k.keyid'] = $_REQUEST['keyid'];
		}
		else
		{
			$join4 = "sc_sort as s on s.keyid = k.keyid and s.sortdate = UNIX_TIMESTAMP(CURDATE())";
		}
		
		$join5 = "sc_costlog AS cl ON s.sortid = cl.sortid AND cl.keyid = s.keyid";
		
		$count = $keywords ->join($join1) ->join($join2)->join($join3)->join($join4)->join($join5)->where($where)->count();
		
		$page = new Page($count,$pageSize);
		$page->setConfig('header','个关键词');
		$this->keylist = $keywords->field($field)->join($join1)->join($join2)->join($join3)->join($join4)->join($join5)->where($where)->page($pageNow,$pageSize)->order('w.webid DESC,s.sortdate DESC')->select();
		// echo $keywords->getLastSql();
		//echo $webinfo->getDbError();
		//print_r($this->keylist);
		$this->page = $page->show();

		$this->assign ( 'weburl', $weburl );
		$this->assign ( 'webid', $webid );
		$this->assign ( 'selist', $selist );
		
		$this->display ();
	}
	
	/**
	 * @name 添加关键词
	 */
	public function add()
	{
		$webinfo = M ( 'webinfo' );
		$keyword = M ( 'keywords' );
		$rs = $webinfo->field ( 'webid,weburl,webname' )->select ();
		if (isset ( $_POST ['submit'] ))
		{
			$webid = isset ( $_POST ['webid'] ) ? $_POST ['webid'] : 0;
			$seid = isset ( $_POST ['seid'] ) ? $_POST ['seid'] : 0;
			
			$key_arr = $_POST ['key'];
			for($i = 0; $i < count ( $key_arr ); $i ++)
			{
				$data = $where = array (
						'webid' => intval ( $webid ),
						'keyname' => strval ( $key_arr [$i] ) 
				);
				$count = intval ( $keyword->where ( $where )->count () );
				
				if ($count > 0)
				{
					$exist [] = $key_arr [$i];
				}
				else
				{
					if ($keyword->add ( $data ))
					{
						$success [] = $key_arr [$i];
					}
					else
					{
						$error [] = $key_arr [$i];
					}
				}
			}

			$this->success ( "关键词添加成功" . count ( $success ) . "个，添加失败" . count ( $error ) + count ( $exist ) . "个，其中已存在" . count ( $exist ) . "个，其他原因失败" . count ( $error ) );
		}
		else
		{
			$se = M ( 'searchengine' );
			$rs_se = $se->field ( 'seid,cnname' )->select ();
			
			$this->assign ( 'url', U ( 'Keyword/add' ) );
			$this->assign ( 'list_se', $rs_se );
			$this->assign ( 'list', $rs );
			$this->display ();
		}
	}
	/**
	 * @name 删除关键词
	 */
	public function del()
	{
	}
	
	/**
	 * @name ajax更新排名
	 */
	public function ajax()
	{
		$time = time ();
		$date = strtotime ( date ( 'Y-m-d' ) );
		// 格式化参数
		$webid = I ( 'REQUEST.webid', 0 );
		$seid = I ( 'REQUEST.seid', 1 );
		$keyid = I ( 'REQUEST.keyid', 0 );
		$action = I ( 'REQUEST.action', '' );
		$keyname = I ( 'REQUEST.keyname', '' );
		$weburl = I ( 'REQUEST.weburl', '' );
		$keyprice = I ( 'REQUEST.keyprice', 0 );
		$sortid = I ( 'REQUEST.sortid', 1 );
		// 必传参数
		if (empty ( $action ))
		{
			$this->ajaxReturn ( 0,'参数丢失',0 );
		}
		// 获取关键词
		if ($action == 'getkeys')
		{
			$where = array (
					'webid' => intval ( $webid ) 
			);
			
			$webinfo = M ( 'webinfo' );
			$rs = $webinfo->where ( $where )->getField ( 'webmetakeys' );
			$this->ajaxReturn ( $rs );
		}
		// 查询更新排名
		elseif ($action == 'getsort')
		{
			if (empty ( $keyname ) || empty ( $weburl ) || empty ( $keyid ) )
			{
				$this->ajaxReturn ( 0,'参数丢失',0 );
			}
			
			import ( 'ORG.SEO.GetRank' );
			$gr = new GetRank ( $weburl );
			$sort = $gr->getSort ( $keyname );
			$st = M('sort');
			//$sort = 1;
			//var_dump($this->error);
			if($gr->error) 
			{
				$this->ajaxReturn ( 0, $gr->error,0 );
			}
			else
			{
				// 要添加的数据
				$data ['keyid'] = $keyid;
				$data ['webid'] = $webid;
				$data ['seid'] = $seid;
				$data ['keyname'] = $keyname;
				$data ['sortdate'] = $date;
				$data ['sorttime'] = $time;
				$data ['keysort'] = $sort;
				// 要更新的数据
				$update = array (
						'keysort' => $sort,
						'sorttime' => time () 
				);
				// 关键词排名唯一条件
				$where = array (
						'webid' => intval ( $webid ),
						'seid' => intval ( $seid ),
						'keyid' => intval ( $keyid ),
						'sortdate' => intval ( $date ) 
				);

				if($st->where($where)->count() == 1)
				{
					//update
					if($st->where($where)->save($update))
					{
						$this->ajaxReturn ( $sort ,'操作成功', 1);
					}
					else
					{
						$this->ajaxReturn ( 0 ,$st->getLastSql(), 0);
					}
				}
				else
				{
					//insert
					if($st->add($data))
					{
						$this->ajaxReturn ( $sort ,'操作成功', 1);
					}
					else
					{
						$this->ajaxReturn ( 0 ,'系统错误', 0);
					}
				}

				
				
			}
		}
		elseif($action == 'updateprice')
		{
			if($keyid == 0)
			{
				$this->ajaxReturn(0,'参数丢失！',0);
			}
			$data = array('keyprice'=>$keyprice);
			$where = array('keyid'=>$keyid,'seid'=>$seid);
			$price = M('keyprice');
			$count = $price->where($where)->count();
			if($count > 0)
			{
				$price->where($where)->save($data);
				$this->ajaxReturn($keyprice,'更新成功！',1);
			}
			else
			{
				$price->add(array_merge($data,$where));
				$this->ajaxReturn($keyprice,'添加成功！',1);
			}
		}
		elseif($action == "cost")
		{
			$keysort = I ( 'keysort', 0);
			$costlog = M('costlog');
			$where = array('keyid'=>$keyid,'seid'=>$seid,'costdate'=>$date);
			if($keysort > 0 && $keysort < 11)
				$cost = $keyprice;
			else
				$cost = 0;
			$data = array('seid'=>$seid,'keyid'=>$keyid,'costdate'=>$date,'sortid'=>$sortid,'cost'=>$cost);
			$count = $costlog->where($where)->count();
			//echo $costlog->getLastSql();
			if($count > 0)
			{
				$this->ajaxReturn(0,'已经扣款，请勿重复操作'.$costlog->getLastSql(),0);
			}
			else
			{
				$sortlog = M ( 'sort' );
				$_where =  array('keyid'=>$keyid,'seid'=>$seid,'sortdate'=>$date);
				$sortid = $sortlog->where($_where)->getField('sortid');
				//$count = $sortlog->where ($_where)->count ();
				if (empty($sortid))
				{
					$this->ajaxReturn(0,date('Y-m-d',$date).' 排名不存在，扣款失败',0);
				}
				else
				{
					$data['sortid'] = $sortid;
					$insertid = $costlog->add($data);
					$this->ajaxReturn(sprintf("%01.2f",$cost),'扣款成功',1);
				}
				//if($costlog->add($data))
				//{
				//	$this->ajaxReturn(sprintf("%01.2f",$cost),'扣款成功',1);
				//}
				//else
				//	$this->ajaxReturn(0,'系统错误，扣款失败'.$costlog->getDbError(),0);
			}
		}
	}
}
?>