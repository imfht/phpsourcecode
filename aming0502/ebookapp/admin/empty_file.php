<?php
include_once "../init.php";

function empty_dir($thedir){
	if(is_dir($thedir)){
		$scan_array = scandir($thedir);
		foreach($scan_array as $file){
			if($file != "." && $file != ".."){
				$unlink = unlink($thedir."/".$file);
				$msg ="fail";
				if($unlink){
					$msg = "success";
				}
				echo "File:".$file." delete:".$msg."<br>";
			}
		}
	}
}

$chapter_dir = WEB_ROOT."data/chapters";
$artile_dir = WEB_ROOT."data/artiles";
empty_dir($chapter_dir);
empty_dir($artile_dir);
?>