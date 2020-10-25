<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta content="telephone=no" name="format-detection" />
<title><?php echo $title; ?></title>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<script>
var sname = "<?php echo SNAME; ?>";
</script>
<script type="text/javascript" src="catalog/view/javascript/fyun/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/fyun/effects.jquery-ui.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/fyun/jquery.nivo-slider.min.js"></script>

<script src="catalog/view/javascript/jquery/jquery.cookie.js"></script>
<script src="catalog/view/javascript/quo/quo.js"></script>
<script src="catalog/view/javascript/fyun/spin.js"></script>
<script type="text/javascript" src="catalog/view/javascript/fyun/custom.js"></script>
  
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>"></script>
<?php } ?>

<link href="catalog/view/theme/diancan/stylesheet/framework.css" rel="stylesheet" type="text/css" media="all" />
<link href="catalog/view/theme/diancan/stylesheet/elements.css" rel="stylesheet" type="text/css" media="all" />
<link href="catalog/view/theme/diancan/stylesheet/style.css" rel="stylesheet" type="text/css" media="all" />
<link href="catalog/view/theme/diancan/stylesheet/responsive.css" rel="stylesheet" type="text/css" media="screen" />
<link href="catalog/view/theme/diancan/stylesheet/hidpi.css" rel="stylesheet" type="text/css" media="screen" />
<link href="catalog/view/theme/diancan/stylesheet/skins/<?php echo $skins; ?>" rel="stylesheet" type="text/css" media="all" />

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<link href="catalog/view/theme/diancan/stylesheet/custom.css" rel="stylesheet" type="text/css" media="all" />


<script>
 $$(document).ready(function(){
var loading = function (text){
		
		$$("#progressBar").html(text);
		$$("#progressBar").show();
		
		}
	$$('.jsurl').tap(function (e)
	 {
		 var url = this.rel;
		loading('加载中...')
		$$(".websiteWrapper").style("opacity","0.3");
		   setTimeout(function(){
			  window.location.href=url;
			},300);
		});

		$$('body').append('<div id="progressBar" class="progressBar" style="display:none">加载中...</div>');
 });
function round2(number,fractionDigits){  
					with(Math){  
						return round(number*pow(10,fractionDigits))/pow(10,fractionDigits);  
					}  
				} 
var refreshTotal = function () {
        var ckcart = $.cookie('cart') || '';
	    var total=0;
	    $.each(ckcart.split(',') , function (i , v) {
		    var p = v.indexOf('-') + 1;
		    if (p != 0)
			    total += parseInt(v.substr(p));
	    });
		if(total == 0){
			$$('.dibu').hide();
			}else{
				$$('.dibu').show();
				}
		
		//#carttotal对应的是显示总数的标签的Id,移植时改动下面这个Id即可
		  $('.count').html(total);
}; 

if(navigator.userAgent.match(/(i[^;]+\;(U;)? CPU.+Mac OS X)/)){  
            $("html").css("cursor","pointer");
} 

   
		
	</script>
</head>