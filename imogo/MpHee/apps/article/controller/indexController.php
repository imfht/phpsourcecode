<?php
class indexController extends adminController{
	protected $layout = 'layout';
	
	//通用部分
	public function com_article_select()
	{
		$ppid = $this->ppid;
		
		$start = $_GET["start"];
		$limit = $_GET["limit"];
		if (empty($start))
		{
			$start = 0;
		}
		if (empty($limit))
		{
			$limit = 10;
		}
		
		$list = model("article")->getlist($ppid, 0, $start, $limit);
		
		// 分离偶数行和奇数行
		$oddlist = array();	// 奇数列表
		$evenlist = array();	// 偶数列表
		$index = 1;
		if (is_array($list))
		{
			foreach ($list as $row)
			{
				$tmpRow = $row;
				
				// 检测当前项是否是多图文
				$type = $row["type"];
				if ($type == 2)
				{
					$tmpRow["sublist"] = model("article")->getsublist($ppid, $row["id"]);
				}
				
				if ($index %2 ==1)
				{
					$oddlist[] = $tmpRow;
				}
				else 
				{
					$evenlist[] = $tmpRow;
				}
				
				$index++;
			}
		}
		
		$this->oddlist = $oddlist;
		$this->evenlist = $evenlist;
		
		$this->articlecount = model("article")->articlecount($ppid);
		$this->currentpage = $start / $limit + 1 ;
		$this->totalpage = ceil($this->articlecount / $limit);
		$this->start = $start;
		$this->limit= $limit;
		
		$this->display();
	}
	
	public function com_article_showone()
	{
	    $ppid = $this->ppid;
		$id = $_GET['id'];
		
		$list = model("article")->info('id', $id);
		
		$evenlist = array();
		$tmpRow = $list;
		$type = $list["type"];
		
		if ($type == 2){
		    $tmpRow["sublist"] = model("article")->getsublist($ppid, $id);
		}
		
		$evenlist[] = $tmpRow;
		
		$this->evenlist = $evenlist;
			
		$this->display();
	}
	
	//图文管理使用
	public function article()
	{
		$start = $_GET["start"];
		$limit = $_GET["limit"];
		if (empty($start))
		{
			$start = 0;
		}
		if (empty($limit))
		{
			$limit = 10;
		}
		
		$list = model("article")->getlist($this->ppid, 0, $start, $limit);
		
		// 分离偶数行和奇数行
		$oddlist = array();	// 奇数列表
		$evenlist = array();	// 偶数列表
		$index = 1;
		if (is_array($list))
		{
			foreach ($list as $row)
			{
				$tmpRow = $row;
				
				// 检测当前项是否是多图文
				$type = $row["type"];
				if ($type == 2)
				{
					$tmpRow["sublist"] = model("article")->getsublist($this->ppid, $row["id"]);
				}
				
				if ($index %2 ==1)
				{
					$oddlist[] = $tmpRow;
				}
				else 
				{
					$evenlist[] = $tmpRow;
				}
				
				$index++;
			}
		}
		
		$this->oddlist = $oddlist;
		$this->evenlist = $evenlist;
		
		$this->articlecount = model("article")->articlecount($this->ppid);
		$this->currentpage = $start / $limit + 1 ;
		$this->totalpage = ceil($this->articlecount / $limit);
		$this->start = $start;
		$this->limit= $limit;
		
		$this->display();
	}

	public function article_detail_addedit()
	{
		$ppid = $this->ppid;
		$action = $_GET["action"];
		if ($action == "edit")
		{
			$id = $_GET["id"];
			$this->info = model("article")->info("id", $id);
		}
		
		$this->display();
	}
	
	public function article_mul_detail_edit()
	{
		$ppid = $this->ppid;
		$action = $_GET["action"];
		if ($action == "edit")
		{
		$data = array();
		
		$id = $_GET["id"];
		$list = model("article")->getlistById($ppid, $id, 0);
		
		if (is_array($list))
		{
			$info = $list[0];
			$item = array();
			$item["id"] = $info["id"];
			$item["tit"] = $info["tit"];
			$item["pic"] = $info["pic"];
			$item["desc"] = $info["desc"];
			$item["con"] = $info["con"];
			$item["url"] = $info["url"];
			$item["type"] = $info["type"];
			$item["ppid"] = $ppid;
			$item["pid"] = 0;
			$item["createtime"] = $info["createtime"];
			$data[] = $item;
			
			if ($info["type"] == 2)
			{
				$sublist = model("article")->getsublistById($ppid, $info["id"]);
				if (is_array($sublist))
				{
					foreach ($sublist as $subinfo)
					{
						$item = array();
						$item["id"] = $subinfo["id"];
						$item["tit"] = $subinfo["tit"];
						$item["pic"] = $subinfo["pic"];
						$item["desc"] = $subinfo["desc"];
						$item["con"] = $subinfo["con"];
						$item["url"] = $subinfo["url"];
						$item["type"] = $subinfo["type"];
						$item["ppid"] = $ppid;
						$item["parent_id"] = $info["id"];
						$item["createtime"] = $subinfo["createtime"];
						$data[] = $item;
					}
				}
			}
		}
				
		$this->ysdata =  json_encode($data);
		}
		
		$this->display();
	}
	
	public function article_mul_detail_add()
	{
		$this->display();
	}
	
	public function article_details_add()
	{
		$ppid = $this->ppid;
		$action = $_GET["action"];
		$data = $_POST;
		
		if ($action == "add")	// 单图文增加
		{
			$data["type"] = 1;
			$data["pid"] = 0;
			
			$data["ppid"] = $ppid;
			$id = $data["id"];
			if (!empty($id))
			{
				$id = model("article")->edit($data);
				echo '修改成功';
			}
			else 
			{
				$id = model("article")->add($data);
				echo '添加成功';
			}
		}
		else if ($action == "adds")	// 多图文增加
		{
			$jsonData = $data["data"];
			
			// 删除图文资源
			$delResId = $_POST["delid"];
			if (!empty($delResId))
			{
				foreach ($delResId as $resId)
				{
					model("article")->delete($resId);
				}
			}
			$index = 0;
			foreach ($jsonData as $item)
			{
				$adata["tit"] = $item['tit'];
				$adata["pic"] = $item['pic'];
				$adata["desc"] = $item['desc'];
				$adata["con"] = $item['con'];
				$adata["url"] = $item['url'];
				if ($index == 0)
				{
					$adata["type"] = 2;
					$adata["pid"] = 0;
					$adata["ppid"] = $ppid;
					$id =$item['id'];
					if (!empty($id))
					{
						$adata["id"] = $id;
						$id = model("article")->edit($adata);
						echo '修改成功';
					}
					else 
					{
						$id = model("article")->add($adata);
						echo '添加成功';
					}
				}
				else 
				{
					$adata["type"] = 2;
					$adata["pid"] = $id;
					$adata["ppid"] = $ppid;
					$tmpId = $item['id'];
					if (!empty($tmpId))
					{
					    $adata["id"] = $tmpId;
						model("article")->edit($adata);
					}
					else
					{
						$adata["id"] = null;
						model("article")->add($adata);
					}
				}
				$index++;
			}
		}
	}
	
	public function del()
	{
		$id = $_POST["rid"];
		if (empty($id))
		{
			echo "此图文不存在";
		}
		
		if (model("article")->delete($id))
		{
			echo '删除成功';
		}
		else 
		{
			echo '删除失败';
		}
	}
	
	//keyword使用
	public function keyword(){
	    $ppid = $this->ppid;
		$this->list = model('article')->getkeywordlist($ppid);	
		$this->display();
	}
	
	public function keywordadd(){
	    $ppid = $this->ppid;
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['ppid'] = $ppid;
			$msg = Check::rule( array(
						array( Check::must($data['keyword']), '关键字不能为空'),
					));
			if(true === $msg){
			$id = model('article')->keywordadd($data);
			$this->alert('添加成功', url('index/keyword'));
			}else{
				$this->alert($msg);
			}		
		}
	}
	
	public function keywordedit(){
	    $ppid = $this->ppid;
		$id= $_GET['id'];
		$this->id = $id;
		$this->info = model('article')->getkeyword($id);
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['ppid'] = $ppid;
			$msg = Check::rule( array(
						array( Check::must($data['keyword']), '关键字不能为空'),
					));
			if(true === $msg){
			    model('article')->keywordupdate($data);
			    $this->alert('修改成功', url('index/keyword'));
			}else{
				$this->alert($msg);
			}		
		}
	}
	
	public function keyworddel()
	{
		$id = $_GET["id"];
		
		if (model("article")->keyworddelete($id))
		{
			$this->alert('删除成功',url('index/keyword'));
		}
		else 
		{
			$this->alert('删除失败',url('index/keyword'));
		}
	}
	
	//首次关注使用
	public function guanzhu(){
	    $ppid = $this->ppid;
		$info = model('article')->getguanzhu($ppid);
		$this->info = $info;
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['ppid'] = $ppid;
			if($data['type'] == 1){
			$msg = Check::rule( array(
						array( Check::must($data['content']), '文字消息不能为空'),
					));
			}else{
			$msg = Check::rule( array(
						array( Check::must($data['articleid']), '多图文还没有选择'),
					));
			}
			if(true === $msg){
			    if(empty($info['ppid'])){
				    model('article')->guanzhuadd($data);
			        $this->alert('添加成功', url('index/guanzhu'));
				}else{
				    model('article')->guanzhuupdate($ppid,$data);
			        $this->alert('修改成功', url('index/guanzhu'));
				}
			}else{
				$this->alert($msg);
			}		
		}
	}
	
}