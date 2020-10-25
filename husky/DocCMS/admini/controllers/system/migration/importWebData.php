<?php
global $request,$db;
	$filename=$request['filename'];
	$curFile=ABSPATH.'/temp/data/'.$filename;
	if(is_file($curFile))
	{
		//验证 并获取 相应表的数据
		require(ABSPATH.'config/doc-config-tables.php');
		$temptables=$temptablefileds=array();
		foreach($tablesArr as $k=>$v)
		{
			$temptables[]=$v['name'];
			$temptablefileds[$v['name']]=$v['fields'];
		}
		$doc = new DOMDocument(null,'utf-8');
		@$doc->load($curFile);
		$e=$doc->documentElement;
		//检验xml节点格式符合要求
		if(is_object($e))
		{
			foreach ($e->childNodes as $tables)
			{
				if($tables->nodeName=='tables')//元素之间的文本节点  计入非法节点
				{
					foreach ($tables->childNodes as $table)
					{
						if(in_array($table->nodeName,$temptables))
						{
							foreach ($table->childNodes as $item)
							{
								if($item->nodeName=='item')
								{
									foreach ($item->childNodes as $field)
									{
										if(in_array($field->nodeName,$temptablefileds[$table->nodeName]))
										{
											foreach ($field->childNodes as $none)// 检验所有的字段节点下若均不存在子节点 通过
											{
												if($none->hasChildNodes())//不应存在六级节点
												exit($filename.'文件root>'.$tables->nodeName.'>'.$table->nodeName.'>'.$item->nodeName.'>'.$field->nodeName.'下存在多余'.$none->nodeName.'节点');
											}
										}
										else
										{
											exit($filename.'文件root>'.$tables->nodeName.'>'.$table->nodeName.'>'.$item->nodeName.'下存在意外'.$field->nodeName.'节点格式');//五级节点<字段/>校验失败
										}
									}
								}
								else
								{
									exit($filename.'文件root>'.$tables->nodeName.'>'.$table->nodeName.'下存在意外'.$item->nodeName.'节点格式');//四级节点<item/>校验失败
								}
							}
						}
						else
						{
							exit($filename.'文件root>'.$tables->nodeName.'下存在意外'.$table->nodeName.'节点格式');//三级节点<表名/>校验失败
						}
					}
				}
				else
				{
					exit($filename.'文件root>下存在意外'.$tables->nodeName.'节点格式');//二级节点<tables/>校验失败
				}
			}
		}
		else
		{
			exit($filename.'文件xml格式有误');
		}
		foreach ($e->childNodes as $tables)
		{
			foreach ($tables->childNodes as $table)
			{
				$i=0;//设置初始值
				$hasfiled=false;
				$hasfiled1=false;
				foreach ($table->childNodes as $item)
				{
					$tempfiled=$tempvalue='';
					foreach ($item->childNodes as $k=>$field)
					{
						if(!$i)
						{
							if($k)$tempfiled.=",";
							$tempfiled.="`".$field->nodeName."`";
							$hasfiled=true;
						}
						$field->nodeValue=mysql_real_escape_string($field->nodeValue);//转化字符
						if($k)$tempvalue.=",";
						$tempvalue.="'".$field->nodeValue."'";
					}
					if($hasfiled)
					{//无字段 匹配到  不生成sql
						if(!$i)
						{
							$tempsql="INSERT INTO `".TB_PREFIX.$table->nodeName."` ( ".$tempfiled.")VALUES \r\n( ".$tempvalue.")";	
						}
						else
						{
							$tempsql.="\r\n,( ".$tempvalue.")";	
						}
					}
					$i++;
				}
				$sqlarr[$table->nodeName]= $tempsql;
			}
		}
		$doc=null;
		if(!empty($sqlarr))
		{
			$db->hide_errors();
			//清理原有数据
			foreach($tablesArr as $k=>$v)//从配置文件中获取表信息
			{
				$deletesql="TRUNCATE TABLE `".TB_PREFIX.$v['name'];
				$db->query($deletesql);
			}
			//导入数据
			foreach($sqlarr as $k=>$v)
			{
				$db->query($v);
			}
			exit("<script>alert('恭喜您，数据导入成功 !现有管理员账号为:admin');window.history.go(-1);</script>");
		}
		else 
		{
			exit("<script>alert('无数据导入!');window.history.go(-1);</script>");
		}
	}
	else
	{
		exit("<script>alert('".$filename."数据文件件不存在!');window.history.go(-1);</script>");
	}
?>