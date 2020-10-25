<?
/**
PHP按日期对文件排序  
**/
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
        arsort($finalarray);
        return ($finalarray);
      } else {
        echo "Sorry,";  
        }
    }
	//C:\wamp\www\data\chapters
$sortedarray=sortfilesbydate("upload");
while ($element=each($sortedarray)){
  echo "File:".$element['key']."上传时间:".date("Y-m-d h:i:s",$element['value'])."<br>";
  }
?>