<?php

function blackbox($Text) {  //UBB
	$Text = trim($Text);
	$Text = htmlspecialchars($Text);
	$Text = ereg_replace("\n","<br />",$Text);
	$Text = ereg_replace("\|\|","ll",$Text); 
	$Text = preg_replace("/\\t/is","  ",$Text); 
	$Text = preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text); 
	$Text = preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text); 
	$Text = preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text); 
	$Text = preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text); 
	$Text = preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text); 
	$Text = preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text); 
	$Text = preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text); 
	$Text = preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<a href=\"\\1\">\\1</a>",$Text); 
	$Text = preg_replace("/\[url\](.+?)\[\/url\]/is","<a href=\"http://\\1\">http://\\1</a>",$Text); 
	$Text = preg_replace("/\[url=(http:\/\/.+?)\](.*)\[\/url\]/is","<a href=\"\\1\">\\2</a>",$Text); 
	$Text = preg_replace("/\[url=(.+?)\](.*)\[\/url\]/is","<a href=\"http://\\1\">\\2</a>",$Text); 
	$Text = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\"\\1\" />",$Text); 
	$Text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<span style=\"color:\\1;\">\\2</span>",$Text); 
	$Text = preg_replace("/\[px=(.+?)\](.+?)\[\/pt\]/is","<span style=\"\\1px;\">\\2</span>",$Text); 
	$Text = preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text); 
	$Text = preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text); 
	$Text = preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text); 
	$Text = preg_replace("/\[email\](.+?)\[\/email\]/is","<a href=\"\\1\">\\1</a>",$Text); 
	$Text = preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text); 
	$Text = preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text); 
	return $Text; 
}

?>