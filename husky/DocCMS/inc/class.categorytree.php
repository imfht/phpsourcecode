<?php 
class adminCategoryTree
{
	var $menuRoot = null;
	//用在显示目录HTML的时候
	var $deepSign = 0;
	var $selectId = 0;
	
	function adminCategoryTree($product_category,$selectId=0,$ordering='')
	{
		if(!empty($product_category))
		{
			$this->menuRoot = $product_category;
			$this->selectId = $selectId;
			$this->ordering = $ordering;
			$aRoot = $this->getDeep($this->menuRoot,0);
			if(!empty($aRoot))
			{
				foreach($aRoot as $o)
				{
					$this->findChild($o);
				}
			}
		}			
	}
	function findChild($inputMenu)
	{
		$tempArr=array();
		if(!empty($this->menuRoot))
		{
			foreach($this->menuRoot as $o)
			{
				if($o->parentId == $inputMenu->id)
				{
					$tempArr[]=$o;
				}
			}
		}
		$printDeep=$this->printDeep($inputMenu->deep);
		if($this->ordering){
			echo '<tr style="background-color:#f2f2f2;height:20px"><td><b>'.$printDeep.$inputMenu->title.'</b></td>';
			echo '<td>'.$printDeep.'<input name="ordering['.$inputMenu->id.']"  type="text" value="'.$inputMenu->ordering.'" size="5" /></td></tr>';
		}else{
			if($this->selectId==$inputMenu->id)
			echo '<tr style="background-color:#C5EAF5;height:20px;"><td><b><span>'.$printDeep.'<a href="./index.php?p='.$inputMenu->channelId.'&c='.$inputMenu->id.'&d='.strval(intval($inputMenu->deep)+1).'">('.$inputMenu->id.')'.$inputMenu->title.'</a></span></b></td></tr>';
			else
			echo '<tr style="background-color:#f2f2f2;height:20px"><td><b><span>'.$printDeep.'<a href="./index.php?p='.$inputMenu->channelId.'&c='.$inputMenu->id.'&d='.strval(intval($inputMenu->deep)+1).'">('.$inputMenu->id.')'.$inputMenu->title.'</a></span></b></td></tr>';
		}
		
		if(count($tempArr)>0)
		{
			foreach($tempArr as $o)
			{
				$this->findChild($o);
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	function printDeep($deep)
	{
		$tempStr="";
		for($i=-1;$i<$deep;$i++)
		{
			if($i==($deep-1))
			$tempStr.='⊕';
			else
			$tempStr.='&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		return $tempStr;
	}
	function getDeep($arr,$deep)
	{
		$tempArr = array();
		foreach($arr as $key=>$o)
		{
			if($o->deep == $deep)
			{
				$tempArr[] = $o;
			}
		}
		return $tempArr;
	}
}