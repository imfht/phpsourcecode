<?php
checkme(9);
function index()
{
}
function create()
{
	//语言创建的方法
}

function createTags()
{
	//语言包标签创建的方法
	global $request;
	if($_POST)
	{
		if(filesize(ABSPATH.'/admini/config/qd-config.php')>0)
		{
			$langList     = explode('@',QD_lang);
			$langTags     = explode('@',QD_lang_tags);
			for($i=0;$i<count($langList)-1;$i++)
			{
				$s = $s.$request['lang_'.$langList[$i]].'@';					
			}
				
			//生成要插入的代码
			$count = count($langTags)-1;
			$s = 'define(\'QD_lang_tags_'.$count.'\',\''.$s.'\');';
			
			//将代码插入config 文件中的指定位置
			$arrInsert = insertContent(ABSPATH.'/admini/config/qd-config.php', $s, 14);
			unlink(ABSPATH.'/admini/config/qd-config.php');
			foreach($arrInsert as $value)
			{
				file_put_contents(ABSPATH.'/admini/config/qd-config.php', $value, FILE_APPEND);
			}	
			
			//写入config 文件
			$langTags = QD_lang_tags.$request['lang_cn'].'@';
			$tempStr = file2String(ABSPATH.'/admini/config/qd-config.php');
			$tempStr = preg_replace("/'QD_lang_tags','.*?'/i","'QD_lang_tags','".$langTags."'",$tempStr);
			string2file($tempStr,ABSPATH.'/admini/config/qd-config.php');
			chmod(ABSPATH.'/admini/config/qd-config.php', 0666);
			redirect('./index.php?m=system&s=lang');	
		}
		else
		{
			echo "文件不存在!";
		}
	}
}

function edit()
{
	//语言修改的方法
	if($_POST)
	{
		if(!empty($_POST['lang']) && !empty($_POST['langName']))
		{
			$_GET['lang'] = intval($_GET['lang']);
			
			if(filesize(ABSPATH.'/admini/config/qd-config.php')>0)
			{
	
				$langList        = explode('@',QD_lang);
				$langNameList    = explode('@',QD_lang_name);
				$langTitleList   = explode('@',QD_lang_title);
				$langSummaryList = explode('@',QD_lang_summary);
				//重命名语言相关的JS 文件
				rename(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_nav.php', ABSPATH.'/admini/js/'.$_POST['lang'].'_nav.php');
				rename(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_menu_content.js', ABSPATH.'/admini/js/'.$_POST['lang'].'_menu_content.js');
				
				//修改config 中记录的语言数据
				for($i=0;$i<count($langList)-1;$i++)
				{
					if(array_search($langList[$i],$langList)==$_GET['lang'])
					{
						$langList[$i]=$_POST['lang'];	
						$langNameList[$i]=$_POST['langName'];
						$langTitleList[$i]=$_POST['langTitle'];
						$langSummaryList[$i]=$_POST['langSummary'];
					}
								
				}
				//数据重组
				$lang = implode('@',$langList);	
				$langName = implode('@',$langNameList);	
				$langTitle = implode('@',$langTitleList);	
				$langSummary = implode('@',$langSummaryList);	
	
	            //写入config 文件
				$tempStr = file2String(ABSPATH.'/admini/config/qd-config.php');
				$tempStr = preg_replace("/'QD_lang','.*?'/i","'QD_lang','".$lang."'",$tempStr);
				$tempStr = preg_replace("/'QD_lang_name','.*?'/i","'QD_lang_name','".$langName."'",$tempStr);
				$tempStr = preg_replace("/'QD_lang_title','.*?'/i","'QD_lang_title','".$langTitle."'",$tempStr);
				$tempStr = preg_replace("/'QD_lang_summary','.*?'/i","'QD_lang_summary','".$langSummary."'",$tempStr);
				
				string2file($tempStr,ABSPATH.'/admini/config/qd-config.php');
				chmod(ABSPATH.'/admini/config/qd-config.php', 0666);
				redirect('./index.php?m=system&s=lang');				
			}
			else
			{
				echo "文件不存在!";
			}
		}
		else
		{
			echo '<script>alert("您填写的语言信息不完整，请重新确认填写");history.go(-1);</script>';
		}
	}
}


function editTags()
{
	//语言包标签修改的方法
	if($_POST)
	{
		if(!empty($_POST['lang_cn']) && !empty($_POST['lang_en']))
		{
			$_GET['tags'] = intval($_GET['tags']);
			
			if(filesize(ABSPATH.'/admini/config/qd-config.php')>0)
			{
	            $langList     = explode('@',QD_lang);
				for($i=0;$i<count($langList)-1;$i++)
				{
					$langTags = $langTags.$_POST['lang_'.$langList[$i]].'@';					
				}
				//写入config 文件
				$tempStr = file2String(ABSPATH.'/admini/config/qd-config.php');
				$tempStr = preg_replace("/'QD_lang_tags_".$_GET['tags']."','.*?'/i","'QD_lang_tags_".$_GET['tags']."','".$langTags."'",$tempStr);
				string2file($tempStr,ABSPATH.'/admini/config/qd-config.php');
				chmod(ABSPATH.'/admini/config/qd-config.php', 0666);
				redirect('./index.php?m=system&s=lang');
			}
			else
			{
				echo "文件不存在!";
			}
		}
		else
		{
			echo '<script>alert("您填写的语言信息不完整，请重新确认填写");history.go(-1);</script>';
		}
	}
}
function delete()
{
	//语言删除的方法
	if(filesize(ABSPATH.'/admini/config/qd-config.php')>0)
	{
		$_GET['lang'] = intval($_GET['lang']);
		
		$langList        = explode('@',QD_lang);
		$langNameList    = explode('@',QD_lang_name);
		$langTitleList   = explode('@',QD_lang_title);
		$langSummaryList = explode('@',QD_lang_summary);
	   //删除语言相关的JS文件
	    if(file_exists(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_nav.php') &&file_exists(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_menu_content.js'))
		{
		 unlink(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_nav.php');
		 unlink(ABSPATH.'/admini/js/'.$langList[$_GET['lang']].'_menu_content.js');
		}
	    //删除config 中记录的语言数据
		for($i=0;$i<count($langList)-1;$i++)
		{
			if(array_search($langList[$i],$langList)==$_GET['lang'])
			{
				$langList[$i]='';	
				$langNameList[$i]='';
				$langTitleList[$i]='';
				$langSummaryList[$i]='';
			}					
		}
		//数据重组
		$lang = implode('@',$langList);	
		$langName = implode('@',$langNameList);	
		$langTitle = implode('@',$langTitleList);	
		$langSummary = implode('@',$langSummaryList);	

        //写入config 文件
		$tempStr = file2String(ABSPATH.'/admini/config/qd-config.php');
		$tempStr = preg_replace("/'QD_lang','.*?'/i","'QD_lang','".$lang."'",$tempStr);
		$tempStr = preg_replace("/'QD_lang_name','.*?'/i","'QD_lang_name','".$langName."'",$tempStr);
		$tempStr = preg_replace("/'QD_lang_title','.*?'/i","'QD_lang_title','".$langTitle."'",$tempStr);
		$tempStr = preg_replace("/'QD_lang_summary','.*?'/i","'QD_lang_summary','".$langSummary."'",$tempStr);
		
		string2file($tempStr,ABSPATH.'/admini/config/qd-config.php');
		chmod(ABSPATH.'/admini/config/qd-config.php', 0666);
		redirect('./index.php?m=system&s=lang');				
	}
	else
	{
		echo "文件不存在!";
	}
}
function deleteTags()
{
	//语言包标签删除的方法
	if(filesize(ABSPATH.'/admini/config/qd-config.php')>0)
	{
		$_GET['tags'] = intval($_GET['tags']);
		$langList = explode('@',QD_lang);
		$langTags = explode('@',QD_lang_tags);
	    //删除config 中记录的语言数据
		for($i=0;$i<count($langTags)-1;$i++)
		{
			if(array_search($langTags[$i],$langTags)==$_GET['tags'])
			{
				$langTags[$i]='';	
			}					
		}
		//数据重组
		$tagsList = implode('@',$langTags);	
		
		//写入config 文件
		$tempStr = file2String(ABSPATH.'/admini/config/qd-config.php');
		$tempStr = preg_replace("/'QD_lang_tags','.*?'/i","'QD_lang_tags','".$tagsList."'",$tempStr);
		$tempStr = preg_replace("/'QD_lang_tags_".$_GET['tags']."','.*?'/i","'QD_lang_tags_".$_GET['tags']."',''",$tempStr);
		string2file($tempStr,ABSPATH.'/admini/config/qd-config.php');
		chmod(ABSPATH.'/admini/config/qd-config.php', 0666);
		redirect('./index.php?m=system&s=lang');	
	}
	else
	{
		echo "文件不存在!";
	}
}
function xCopy($source, $destination, $child)
{
	//复制文件的方法
    if(!is_dir($source)){
    echo '<script>alert("您的模板不是规范的SHL多语版模板格式，请先转换下您的模板格式再执行此操作。详情请联系起点No.1工作室。");history.go(-1);</script>';
    return 0;
    }
    if(!is_dir($destination)){
    mkdir($destination,0777);
    }
    $handle=dir($source);
    while($entry=$handle->read()) {
        if(($entry!=".")&&($entry!="..")){
            if(is_dir($source."/".$entry)){
                if($child) 	xCopy($source."/".$entry,$destination."/".$entry,$child);
            }else{
                copy($source."/".$entry,$destination."/".$entry);
            }
        }
    }
    return true;
}

function insertContent($source, $s, $iLine)
{
	//指定文件内指定行插入指定数据的方法
    $file_handle = fopen($source, "r");
    $i = 0;
    $arr = array();
    while(!feof($file_handle)) 
	{      
       $line = fgets($file_handle);
       ++$i;
       if($i == $iLine) 
	   {
		  $arr[] = substr($line, 0, strlen($line)-1) . $s . "\n";
       }
	   else
	   { 
          $arr[] = $line;
       }
    }
    fclose($file_handle);
    return $arr;
}

function get_lang_info($i=1)
{
	//获取语言信息
	global $request;
	$langList        = explode('@',QD_lang);
	$langNameList    = explode('@',QD_lang_name);
	$langTitleList   = explode('@',QD_lang_title);
	$langSummaryList = explode('@',QD_lang_summary);
	if($i==1)
	return $langList[$request['lang']];
	elseif($i==2)
	return $langNameList[$request['lang']];
	elseif($i==3)
	return $langTitleList[$request['lang']];
	elseif($i==4)
	return $langSummaryList[$request['lang']];	
	else
	return false;
}
?>