<?php
	include_once "../init.php";
	
	$chapter_dir = WEB_ROOT."data/chapters";
	$sortedarray=sortfilesbydate($chapter_dir);
	$curr_count = sizeof($sortedarray);
	echo "curr_count:".$curr_count."<br>";
	/**
		max_count：目录保留最大文件数，超过此个数的文件将按照时间顺序删除
	**/
	if(@$_GET["max_count"]){
		$max_count = $_GET["max_count"];
		echo " max_count:".$max_count."<br>";
		if($curr_count>$max_count){
			$dif_count = $curr_count - $max_count;
			for ($i=0; $i<$dif_count; $i++)
			{
			  $element = each($sortedarray);
			  $unlink = unlink($chapter_dir."/".$element['key']);
			  $msg ="fail";
			  if($unlink){
				$msg = "success";
			  }
			  echo "File:".$element['key']." time:".date("Y-m-d h:i:s",$element['value'])." delete:".$msg."<br>";
			}
		}else{
			echo "no file need delete";
		}
	}
	
	function sortfilesbydate($thedir){
    if (is_dir($thedir)){
      $scanarray=scandir($thedir);
      $finalarray=array();
      for($i=0;$i<count($scanarray);$i++){
        if ($scanarray[$i] != "." && $scanarray[$i] != ".."){
          if (is_file($thedir."/".$scanarray[$i])){
            $finalarray[$scanarray[$i]]=filemtime($thedir."/".$scanarray[$i]);
            }
          }
        
        }
        asort($finalarray);
        return ($finalarray);
      } else {
        echo "Sorry,".$thedir."is not dir";  
      }
	}
?>