<?php
/**
 * @name 网站管理
 */
class SiteAction extends CommonAction
{
	/**
	 * @name 查看站点
	 */
	private function index()
	{
		$this->display ();
	}
	/**
	 * @name 查看站点
	 */
	public function lists()
	{
		import ( 'ORG.Util.Page' );
		$where = array('webstatus'=>1);
		
		$weblist = $this->getWebList($where);
		for ($i=0; $i < count($weblist[0]); $i++) 
		{
			$weblist[0][$i]['costToday'] = $this->getCost($weblist[0][$i]['webid']);
			$weblist[0][$i]['costLink'] = U('Cost/lists?webid='.$weblist[0][$i]['webid']);
		}

		$this->assign ( 'list', $weblist[0] );
		$this->assign ( 'page', $weblist[1] );
		$this->display ();
	}
	/**
	 * @name 查看站点
	 */
	public function recycle()
	{
		import ( 'ORG.Util.Page' );
		$where = array('webstatus'=>0);
		
		$weblist = $this->getWebList($where);
		$this->assign ( 'list', $weblist[0] );
		$this->assign ( 'page', $weblist[1] );
		$this->display ();
	}
	/**
	 * @name 添加站点
	 */
	public function add()
	{
		if (isset ( $_POST ['submit'] ))
		{
			$webinfo = M ( 'Webinfo' );
			$cominfo = M ( 'cominfo' );
			$where = array (
					'weburl' => strval ( $_POST ['webinfo'] ['weburl'] ) 
			);
			$count = $webinfo->where ( $where )->count ();
			$_POST ['cominfo'] ['snapshot'] = strtotime ( $_POST ['cominfo'] ['snapshot'] );
			$_POST ['webinfo'] ['timeadd'] = time ();
			
			if (intval ( $count ) > 0)
			{
				$this->error ( '网站已存在！' );
			}
			else
			{
				$webinfo->create ( $_POST ['webinfo'] );
				if ($webinfo->add ())
				{
					$_POST ['mid'] = $webinfo->getLastInsID ();
					$_POST['cominfo']['webid'] = $webinfo->getLastInsID();
					$cominfo->create ( $_POST ['cominfo'] );
					$cominfo->add ();
					$this->success ( '添加网站成功！' );
				}
				else
				{
					$this->error ( '添加网站成功！' );
				}
			}
		}
		else
		{
			$this->assign ( 'url', U ( 'Site/add' ) );
			$this->display ();
		}
	}
	/**
	 * @name 编辑站点
	 */
	public function edit()
	{
		$webid = isset ( $_GET ['id'] ) ? $_GET ['id'] : 0;
		$webinfo = M ( 'webinfo' );
		$join = 'sc_cominfo c on sc_webinfo.webid = c.webid';
		$where = array (
				'sc_webinfo.webid' => intval ( $webid ) 
		);
		$rs = $webinfo->join ( $join )->field ( 'sc_webinfo.*,c.snapshot,c.unlinks,c.pages' )->where ( $where )->select ();
		
		$this->assign ( 'info', $rs [0] );
		$this->display ();
	}
	
	/**
	 * @name 修改站点
	 */
	public function update()
	{
	}
	/**
	 * @name 删除站点
	 */
	public function delete()
	{
		$id = I('id',0,int);
		if($id < 1)
		{
			$this->error('参数丢失');
		}
		else
		{
			$where = array('webid'=>$id);
			$data = array('webstatus'=>0);
			$webinfo = M('webinfo');
			$id = $webinfo->where($where)->save($data);
			echo $id;
		}
	}
	/**
	 * @name 站点ajax操作
	 */
	public function ajaxCheck()
	{
		import ( 'ORG.SEO.Krasen' );
	
		$action = I('REQUEST.action','checkurl');
		$weburl = I('REQUEST.weburl','');
		$field = I('REQUEST.field','');
		
		$where = array (
				'weburl' => strval ( $weburl ) 
		);
		if (strlen ( $weburl ) < 1 && $action != 'savemeta')
		{
			$this->ajaxReturn ( false );
		}
		$webinfo = M ( 'webinfo' );
		$count = $webinfo->where ( $where )->count ();

		if ($action == 'checkurl')
		{
			if ($count > 0)
			{
				$this->ajaxReturn ( 0,'网站已经存在',0 );
			}
			else
			{
				$this->ajaxReturn (  1,'网站可以添加',1 );
			}
		}
		else if ($action == 'metainfo' && $count < 1)
		{
			
			$krasen = new Krasen ( $weburl );
			$krasen->getSnapshot ();
			$krasen->getPages ();
			$krasen->getUnlinks ();
			$krasen->getReal();
			//$krasen->getReal ();
			if(empty($krasen->error))
			{
				$data = array ();
				$data ['snapshot'] = trim ( $krasen->snapshot );
				$data ['pages'] = str_replace ( ',', '', $krasen->pages );
				$data ['unlinks'] = str_replace ( ',', '', $krasen->unlinks );
				$data ['title'] = $krasen->title;
				$data ['keywords'] = $krasen->keywords;
				$data ['description'] = $krasen->description;
				$this->ajaxReturn ( $data ,'操作成功',1);
			}
			else
			{
				$this->ajaxReturn ( $krasen->error ,'操作失败',0);
			}
			
			
		}
		elseif ($action == 'updatemeta')
		{
			$webid = isset ( $_POST ['webid'] ) ? $_POST ['webid'] : 0;
			$seid = isset ( $_POST ['seid'] ) ? $_POST ['seid'] : 0;
			$krasen = new Krasen ( $weburl );
			$krasen->getSnapshot ();
			$krasen->getPages ();
			$krasen->getUnlinks ();
			
			$data = array ();
			$data ['snapshot'] = trim ( $krasen->snapshot );
			$data ['pages'] = str_replace ( ',', '', $krasen->pages );
			$data ['unlinks'] = str_replace ( ',', '', $krasen->unlinks );
			
			
			
			$this->ajaxReturn ( $data );
		}
		elseif ($action == 'savemeta')
		{
			$webid = isset ( $_POST ['webid'] ) ? $_POST ['webid'] : 0;
			$seid = isset ( $_POST ['seid'] ) ? $_POST ['seid'] : 0;
			$field = isset ( $_POST ['field'] ) ? $_POST ['field'] : 0;
			$value = isset ( $_POST ['value'] ) ? $_POST ['value'] : 0;
			
			$where = array (
					'webid' => intval ( $webid ),
					'seid' => intval ( $seid ) 
			);
			if ($field == 'snapshot') {
				$value = strtotime($value);
			}
			$data = array (
					$field => intval ( $value ) 
			);
			
			$cominfo = M ( 'cominfo' );
			$cominfo->save($data,array('where'=>$where));
			echo $cominfo->getLastSql();
		}
		else
		{
			$this->ajaxReturn ( 0,'网站已经存在',0 );
		}
	}
	
	private function getWebList($where=array())
	{
		$pageSize = 15;
		$seid = I('seid',1);
		$page = I('p',1);
		$obj = M ( 'Webinfo w' );
		$rs = $obj->join ( 'sc_cominfo C on w.webid = C.webid and c.seid = '.$seid )->where($where)->page ( $page, $pageSize )->select ();
		//echo $obj->getLastSql();
		$rowCount = $obj->where($where)->count ();
		$page_ = new Page ( $rowCount, $pageSize );
		// $url URL表达式，格式：'[分组/模块/操作#锚点@域名]?参数1=值1&参数2=值2...'
		for($i = 0; $i < count ( $rs ); $i ++)
		{
			$rs [$i] ['edit'] = U ( 'Site/edit?id=' . $rs [$i] ['webid'] );
			$rs [$i] ['del'] = U ( 'Site/delete?id=' . $rs [$i] ['webid'] );
			$rs [$i] ['list'] = U ( 'Keyword/lists?id=' . $rs [$i] ['webid'] );
		}
		$pageinfo = $page_->show ();
		return array($rs,$pageinfo);
	}

	private function getCost($webid,$date='now')
	{
		if ($date == 'now') 
		{
			$date = intval(strtotime(date('Y-m-d')));
		}
		else
		{
			$date = intval($date);
		}

		$webinfo = M('webinfo');

		$sql = "SELECT IFNULL(SUM(c.cost),0) as cost FROM sc_webinfo AS w,sc_keywords AS k,sc_costlog as c WHERE w.webid = k.webid AND k.keyid = c.keyid AND c.costdate = $date AND w.webid = $webid";
		$rs = $webinfo->query($sql);
		return $rs[0]['cost'];
	}

	private function getMeta()
	{
	}
}