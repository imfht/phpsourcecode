<?php 
if(isset($atts['text'])){
	$textString = "<span class=\"app-normal\">{$atts['text']}</span>";
	if(isset($atts['iconVec'])){
		$iconVec=$atts['iconVec'];
		$iconVecSize=isset($atts['iconVecSize'])?$atts['iconVecSize']:"13";
		$iconVec="<span style=\"font-size:{$iconVecSize}px\"><i  class=\"fa {$iconVec}\"></i></span>";
		if(isset($atts['iconVecAlign']) && strcasecmp("right",$atts['iconVecAlign'])==0){
			$titleString = $titleString + " " + $iconVec;
		}else{
			$titleString = $iconVec + " " + $titleString;
		}
	}
	$atts['text']=$textString;
}
?>
<?php require dirname(__FILE__).'/componentTagSupport.php';?>
<?php if(isset($atts['text'])){?>
	text:'<?php echo $atts['text']?>',
<?php }?>
<?php if(isset($atts['menuid'])){?>
	menu:'<?php echo $atts['menuid']?>',
<?php }?>
<?php if(isset($atts['tooltip'])){?>
	tooltip:'<?php echo $atts['tooltip']?>',
<?php }?>
<?php if(isset($atts['scale'])){?>
	scale:'<?php echo $atts['scale']?>',
<?php }?>
<?php if(isset($atts['onclick'])){?>
	handler:<?php echo self::getMyOnclick($atts['onclick'])?>,
<?php }?>