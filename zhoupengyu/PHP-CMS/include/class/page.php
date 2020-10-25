<?php
class zPage{
	var $config;
	var $GVar;
	var $this_lots_per_page; //目前選擇每頁顯示筆數
	var $page;  //目前分頁
	var $total_record;
	var $total_page;
	var $element;
	var $page_html_schema;
	var $base_url;
	
	function __construct($sql = null,$count_field = "*",$db,$use_ado_recordcount = false,$page_num=null)
	{
		global $Config, $GVar;
		$this->config = $Config;
		$this->GVar=$GVar;
		
		//如果有選擇每頁幾條就回傳選擇的否則就是預設的
		if ($this->GVar->request["lots_per_page"])
		{
			$this->this_lots_per_page = $this->GVar->request["lots_per_page"];
		}
		
		if ($page_num) {
		    $this->this_lots_per_page=$page_num;
		}else{
		    $this->this_lots_per_page = empty($this->this_lots_per_page)?10:$this->this_lots_per_page;
		}
		
		$this->element["first_page"] = "首页";
		$this->element["previous_page"] = "上一页";
		$this->element["next_page"] = "下一页";
		$this->element["last_page"] = "尾页";
		$this->element["previous_pages"] = "<span>...</span>";
		$this->element["next_pages"] = "<span>...</span>";
		$this->element["per_page_select_number"] = array(9999999=>_lang_all,5=>"5",10=>"10",20=>"20",50=>"50",100=>"100");
		
		$this->page_html_schema["body"] = '<div class="layui-box layui-laypage layui-laypage-default" id="pages">%s</div>';
		$this->page_html_schema["count_all"] = '<a>%s</a>';
		$this->page_html_schema["page_button"] = '<a href="%s">%s</a>';
		$this->page_html_schema["edge_button_on"] = '<a class="disabled_1" href="%s">%s</a>';
		$this->page_html_schema["edge_button_off"] = '<a class="disabled" href="%s">%s</a>';
		$this->page_html_schema["on_this_page"] = '<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>%d</em></span>';

		if ($db && $sql)
		{
			$this->setTotalPageBySql($db,$sql,$count_field,$use_ado_recordcount);
		}
	}

	function setTotalPageBySql($db,$sql,$count_field = "*",$use_ado_recordcount = false)
	{
		//透過語句
		$sql_lower = strtolower($sql);
		if ($use_ado_recordcount)
		{
			if ($table_name) {
				//可解决前后都带有子查询的sql语句
				$filter = "from ".$table_name;	
			}else{
				$filter = "from";
			}
			$new_sql = "SELECT $count_field " . substr($sql,strpos($sql_lower,$filter));
		
			$data = $db->Execute($new_sql);				
			$this->total_record = $data->RecordCount();
		}else{
			$new_sql = "select count($count_field) " . substr($sql,strpos($sql_lower,"from")) ;
			$this->total_record = $db->GetOne($new_sql);
		}
		$this->setPage();
	}
	function setTotalPageByParam($total_record)
	{
		$this->total_record = $total_record;
		$this->setPage();
	}
	
	//計算頁數值
	function setPage()
	{
		$this->total_page = ceil($this->total_record / $this->this_lots_per_page);
		if ($this->total_page == 0)
			$this->total_page =1;
		//定位目前頁數
		$this->page = empty($this->page)?$this->GVar->request["page"]:$this->page;
		$this->page = empty($this->page)?1:$this->page;
		if ($this->page > $this->total_page)
			$this->page = $this->total_page;
		if ($this->page<1)
			$this->page=1;
	}
	
	function GetPageButtonHref($page)
	{
		if ($page >0){
			$get=$this->GVar->fget;
			unset($get['page']);
			foreach ($get as $k=>$v){
				$url[]=$k."=".$v;
			}
			$str="index.php?page=$page&".implode("&", $url);
		}
		return $str;
	}
	//取得分頁條HTML
	function getPageMenu()
	{
		$button["count_all"] = sprintf($this->page_html_schema["count_all"],ceil($this->total_record/$this->this_lots_per_page)."页/".$this->total_record."条");
		//第一頁按鈕
		if ($this->page==1)
		{
			$button["first_page"] = sprintf($this->page_html_schema["edge_button_off"],$this->GetPageButtonHref(0),$this->element["first_page"]);
		}else{
			$button["first_page"] = sprintf($this->page_html_schema["edge_button_on"],$this->GetPageButtonHref(1),$this->element["first_page"]);
		}
		//前一頁按鈕
		if ($this->page > 1){
			$button["previous_page"] = sprintf($this->page_html_schema["edge_button_on"],$this->GetPageButtonHref($this->page-1),$this->element["previous_page"]);
		}else{
			$button["previous_page"] = sprintf($this->page_html_schema["edge_button_off"],$this->GetPageButtonHref($this->page-1),$this->element["previous_page"]);
		
		}
		//下一頁按鈕
		if ($this->page +1 <= $this->total_page)
			$button["next_page"] = sprintf($this->page_html_schema["edge_button_on"],$this->GetPageButtonHref($this->page +1),$this->element["next_page"]);
		else 
			$button["next_page"] = sprintf($this->page_html_schema["edge_button_off"],$this->GetPageButtonHref(0),$this->element["next_page"]);
		//最末頁按鈕
		if ($this->page==$this->total_page)
		{
			$button["last_page"] = sprintf($this->page_html_schema["edge_button_off"],$this->GetPageButtonHref(0),$this->element["last_page"]);
		}else{
			$button["last_page"] = sprintf($this->page_html_schema["edge_button_on"],$this->GetPageButtonHref($this->total_page),$this->element["last_page"]);
		}
		//輸出第一頁及前一頁按鈕
		
		$obj_element .= $button["first_page"] . $button["previous_page"];
		
		//輸出數字的部份設定數字的開始與結束
		if ($this->total_page > 10)
		{
			//當總頁數超過指定頁數時採用分段顯示頁碼
			$flag = true;
			$half = floor(10 /2);
			$start_page = $this->page - $half;  
			if ($start_page <1)
				$start_page =1;
			$end_page = $start_page + 10 -1;
			if ($end_page > $this->total_page)
			{
				$end_page = $this->total_page;
				$start_page = $this->total_page - 10+1; 
			}
		}else{
			$flag = false;
			$start_page = 1;
			$end_page = $this->total_page;	
		}
		//輸出數字的部份
		if ($flag && $start_page > 1)
		{
			$obj_element .= $this->element["previous_pages"] ;	
		}
		
		for ($i = $start_page;$i<=$end_page;$i++)
		{
			if ($i == $this->page)
			{
				$obj_element .= sprintf($this->page_html_schema["on_this_page"],$i);
			}else{
				$t = $i . "";
				$obj_element .= sprintf($this->page_html_schema["page_button"],$this->GetPageButtonHref($i),$t) ;
			}
		}

		if ($end_page < $this->total_page)
		{
			$obj_element .= $this->element["next_pages"] ;	
		}
		//輸出 下一頁及最末頁
		$obj_element .= $button["next_page"] . $button["last_page"];

		//輸出每頁顯示筆數的下拉框
		if (!$this->element["per_page_select_number"][$this->this_lots_per_page])
		{
			$option .= sprintf($this->page_html_schema["per_page_select_number_option"],$this->this_lots_per_page,"selected",$this->this_lots_per_page);
		}
		foreach ($this->element["per_page_select_number"] as $key => $value)
		{
			$option .= sprintf($this->page_html_schema["per_page_select_number_option"],$key,($key == $this->this_lots_per_page)?"selected":"",$value);
		}
		$obj_element .= sprintf($this->page_html_schema["per_page_select_number_body"],$option)."" ;
		$obj_element .= $this->page_html_schema["jump_page"] ;
		$obj_element .= $this->page_html_schema["hidden_url"];
		$obj_element .= $this->page_html_schema["page"];
		$obj_element .= $button["count_all"] ;

		return sprintf($this->page_html_schema["body"] ,$obj_element);
		
	}
	function getStartRecord()
	{
		return ($this->page-1) * $this->this_lots_per_page;
	}
}
?>