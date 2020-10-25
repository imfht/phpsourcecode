<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: misc.php,v 1.22 2010/07/27 09:24:17 alex Exp $
 *
 */

function utf8_strlen($str)
{
	return preg_match_all('/./u', $str, $dummy);
}

function utf8_substr($str,$start) 
{ 
   preg_match_all("/./u", $str, $ar); 

   if(func_num_args() >= 3) { 
       $end = func_get_arg(2); 
       return join("",array_slice($ar[0],$start,$end)); 
   } else { 
       return join("",array_slice($ar[0],$start)); 
   } 
}

/* 
 * This function is intended to replace the ord() for UTF-8
 */
function utf8_ord($c)
{
	$ud = 0;
	if (ord($c{0})>=0 && ord($c{0})<=127)
		$ud = ord($c{0});
	if (ord($c{0})>=192 && ord($c{0})<=223)
		$ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
	if (ord($c{0})>=224 && ord($c{0})<=239)
		$ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
	if (ord($c{0})>=240 && ord($c{0})<=247)
		$ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
	if (ord($c{0})>=248 && ord($c{0})<=251)
		$ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
	if (ord($c{0})>=252 && ord($c{0})<=253)
		$ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
	if (ord($c{0})>=254 && ord($c{0})<=255) //error
		$ud = false;
	return $ud;
}

function IsEmailAddress($email)
{
	if (preg_match("/^[_\.0-9A-Za-z-]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,4}$/", $email)) {
		return true;
	} else {
		return false;
	}	
}

function IsInArray($myarray, $value) {
	for ($i=0; $i<sizeof($myarray); $i++) {
		if ($value == $myarray[$i]) {return $i;}
	}
	return -1;
}

/* Because the PHP's array_unique will NOT remove the element in the array
 * but just set the element to empty. If we have {1, 2, 1, 3}, it would
 * become {1, 2, , 3}.... So I write this one to make it become {1, 2, 3}
 */
function ArrayUnique($myarray)
{
	$return_array = array();
	for ($i = 0; $i < sizeof($myarray); $i++) {
		if (IsInArray($return_array, $myarray[$i]) == -1) {
			array_push($return_array, $myarray[$i]);
		}
	}
	return $return_array;
}

function GetExtraParams($param_array, $keys)
{
	$extra_params = "";

	$keys_array = explode(",", $keys);
	for ($i = 0; $i < sizeof($keys_array); $i++) {
		$keys_array[$i] = trim($keys_array[$i]);
		if ($param_array[$keys_array[$i]] != "") {
			$extra_params .= "&".$keys_array[$i]."=".rawurlencode(stripslashes($param_array[$keys_array[$i]]));
		}
	}
	return $extra_params;
}

function SetAllowHTMLChars($str)
{
	$str = str_replace("<b>", "[b]", $str);
	$str = str_replace("</b>", "[/b]", $str);
	$str = str_replace("<i>", "[i]", $str);
	$str = str_replace("</i>", "[/i]", $str);
	$str = str_replace("<u>", "[u]", $str);
	$str = str_replace("</u>", "[/u]", $str);
	$str = str_replace("<p>", "[p]", $str);
	$str = str_replace("</p>", "[/p]", $str);

	// 去除html的標籤,讓標籤在網頁中無作用
	$str = htmlspecialchars($str);
     
	// 回復訊息中我們許可的html標籤
	$str = str_replace("[b]", "<b>", $str);
	$str = str_replace("[/b]", "</b>", $str);
	$str = str_replace("[i]", "<i>", $str);
	$str = str_replace("[/i]", "</i>", $str);
	$str = str_replace("[u]", "<u>", $str);
	$str = str_replace("[/u]", "</u>", $str);
	$str = str_replace("\n", "<br>", $str);
	$str = str_replace("[p]", "<p>", $str);
	$str = str_replace("[/p]", "</p>", $str);

	return $str;
}

function PrintSelectOptions($value_array, $option_array, $selected_item)
{
	for ($i = 0; $i < sizeof($value_array); $i++) {
		if ($selected_item == $value_array[$i]) {
			$selected = "selected";
		} else {
			$selected = '';
		}
		echo '<option value="'.$value_array[$i].'" '.$selected.'>'.$option_array[$i].'</option>';
	}
}

function PrintTip($title, $mesg, $method = "")
{
	$title = addslashes($title);
	$mesg = addslashes($mesg);

	//$value = '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/tooltip.gif" border="0" onmouseover="return TooltipShow(\''.$mesg.'\',250);" onmouseout="TooltipHide();">';
	$value = '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/tooltip.gif" border="0" onmouseover="return ALEXWANG.Tooltip.Show({title:\''.$title.'\', msg:\''.$mesg.'\', width:250});" onmouseout="ALEXWANG.Tooltip.Hide();">';

	if ($method == "return") {
		return $value;
	} else {
		echo $value;
	}
}


function PrintPageLink($TotalItems, $CurrentPage, $PerPage, $Link, $ExtraParams, $OnClick = "")
{
	global $STRING;

	if (!$CurrentPage) {
		$CurrentPage = 1;
	}
	echo $STRING['total'].$STRING['colon'].'<font color="#0000c0">'.$TotalItems.'</font> '.
		$STRING['items'].'&nbsp;&nbsp;&nbsp;'.$STRING['page'].$STRING['colon'].'&nbsp;';

	if ($TotalItems <= $PerPage) {
		//return;
	}
	if ($ExtraParams != "") {
		$ExtraParams = "&".$ExtraParams;
	}
	if ($CurrentPage > 1) {
		$PrevPage = $CurrentPage - 1;
		if ($Link) {
			echo '<a href="'.$Link.'?page='.$PrevPage.$ExtraParams.'">';
		} else {
			echo '<a href="JavaScript:'.$OnClick.'('.$PrevPage.');">';
		}
		echo '<font class="page">'.$STRING['prevpage'].'</font></a>&nbsp;';
	} 

	if (!$Link && $OnClick && (($TotalItems / $PerPage) > 25)) {
		echo '<select name="pagedropdown" onChange="'.$OnClick.'(this.options[this.selectedIndex].value)">';
		for($i = 1; $i-1 < $TotalItems / $PerPage; $i++){
			if ($i == $CurrentPage){
				$selected = "selected";
			} else {
				$selected = "";
			}
			echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		echo '</select>';
	} else {
		for($i = 1; $i-1 < $TotalItems / $PerPage; $i++){
			if ($i == $CurrentPage){
				echo '<font class="current_page">'.$i.'</font>&nbsp;';
			}else {
				if ($Link) {
					echo '<a href="'.$Link.'?page='.$i.$ExtraParams.'">';
				} else {
					echo '<a href="JavaScript:'.$OnClick.'('.$i.');">';
				}
				echo '<font class="page">'.$i.'</font></a>&nbsp;';
			}
			if (($i%30) == 0) {echo "<br>";}
		}
	}
	if ($CurrentPage < $TotalItems / $PerPage){
		$NextPage = $CurrentPage + 1;
		if ($Link) {
			echo '<a href="'.$Link.'?page='.$NextPage.$ExtraParams.'">';
		} else {
			echo '<a href="JavaScript:'.$OnClick.'('.$NextPage.');">';
		}
		echo '<font class="page">'.$STRING['nextpage'].'</font></a>';
	}
}

function PrintGotoTop()
{
	global $STRING;
?>
 
<script language="JavaScript" type="text/javascript">
GotoTop = function() {
	var W = 15; // width of image
	var H = 19; // the length of image
	var X = 95; // the position of X on the screen
	var Y = 95; // the position of Y on the screen
	var RefreshTime = 20; // re-postition every 1/1000 second
	var div = null;
	var posX, posY;

	var DivGenerate = function() {
		if (div) {
			return;
		}
		div = document.createElement('DIV');
		div.id = 'WaterMark';
		document.body.appendChild(div);
		div.innerHTML = '<a href="#top"><IMG src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/top.gif" title="<?php echo $STRING['top']?>" border="1"></a>';
		div.style.width = W;
		div.style.height = H;
		div.style.position = 'absolute';
	};
	return {
		WindowResize : function() {
			posX = (document.body.clientWidth - W) * (X/100);
			posY = (document.body.clientHeight - H) * (X/100);
		},
		Refresh : function() {
			div.style.left = posX + document.body.scrollLeft;
			div.style.top = posY + document.body.scrollTop;

		},
		Show: function() {
			DivGenerate();
			this.WindowResize();
			window.onresize = this.WindowResize;
			setInterval ("GotoTop.Refresh()",RefreshTime)
		}
	}
}();

GotoTop.Show();

</script>

<?php
}

function LoadingTimerShow()
{
	global $STRING;

	echo '<div id="div_loading" class="div_loading">'."\n";
	echo '<center>'."\n";
	echo '<p>&nbsp</p>'; 
	echo '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/loading.gif" width="16" height="16" border="0">';
	echo $STRING['loading']."\n";
	echo '</center>'."\n";
	echo '</div>'."\n";

	flush();
}

function LoadingTimerHide()  
{                      
	echo '<script language="JavaScript" type="text/javascript">'."\n";
	echo 'var div = document.getElementById("div_loading");'."\n";
	echo "div.style.display='none';\n";
	echo '</script>'."\n";
}

?>
