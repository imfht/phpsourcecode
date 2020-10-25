<?php
$src=$_GET['id'];
$img = file_get_contents('https://img.youtube.com/vi/'.$src.'/0.jpg');
$name = "img/".$src.".jpg";
file_put_contents($name,$img); 
echo '<img src="',$name,'" width="480" height="360" />';
?>