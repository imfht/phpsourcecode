<?php 
$temp = explode('/',$data['filePath']); 
if($temp[0]!='http:')
{
?>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/swfobject.js"></script>
<div id="CuPlayer" >
<strong>你的Flash Player版本过低，请<a href="http://get.adobe.com/cn/flashplayer/" >点此进行播放器升级</a>！</strong>
</div>
<script type="text/javascript">
var so = new SWFObject("<?php echo $tag['path.skin']; ?>res/swf/CuPlayerMiniV20_Black_S.swf","CuPlayer","400","320","9","#000000");
so.addParam("allowfullscreen","true");
so.addParam("allowscriptaccess","always");
so.addParam("wmode","opaque");
so.addParam("quality","high");
so.addParam("salign","lt");
so.addVariable("CuPlayerFile","<?php echo $data['filePath']; ?>");
so.addVariable("CuPlayerImage","<?php echo $data['picture']; ?>");
so.addVariable("CuPlayerShowImage","true");
so.addVariable("CuPlayerWidth","400");
so.addVariable("CuPlayerHeight","320");
so.addVariable("CuPlayerAutoPlay","false");
so.addVariable("CuPlayerAutoRepeat","true");
so.addVariable("CuPlayerShowControl","false");
so.addVariable("CuPlayerAutoHideControl","true");
so.addVariable("CuPlayerAutoHideTime","1");
so.addVariable("CuPlayerVolume","80");
so.write("CuPlayer");
</script>
<?php }else{?>
<embed src="<?php echo $data['filePath']?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="opaque" width="400" height="320"></embed>
<?php }?>