<?php 
if(isset($atts['title'])){
	$titleString=$atts['title'];
	if(substr($titleString, 0,1)=="!"){
		$titleString=substr($titleString, 1);
		$titleString = "<span class=\"app-container-title-bold\">{$titleString}</span>";
	}else{
		$titleString = "<span class=\"app-container-title-normal\">{$titleString}</span>";
	}
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
	$atts['title']=$titleString;
}
?>
<?php require 'containerTagSupport.php';?>
<?php if(isset($atts['title'])){?>
	title: '<?php echo $atts['title']?>',
<?php }?>
<?php if(isset($atts['collapsible'])){?>
	collapsible: <?php echo $atts['collapsible']?>,
<?php }?>
<?php if(isset($atts['collapsed'])){?>
	collapsed: <?php echo $atts['collapsed']?>,
<?php }?>
<?php if(isset($atts['titleCollapse'])){?>
	titleCollapse: <?php echo $atts['titleCollapse']?>,
<?php }?>
<?php if(isset($atts['bodyStyle'])){?>
	bodyStyle: {<?php echo $atts['bodyStyle']?>},
<?php }?>
<?php if(isset($atts['bodyPadding'])){?>
	bodyPadding: '<?php echo $atts['bodyPadding']?>',
<?php }?>
<?php if(isset($atts['closable'])){?>
	closable: <?php echo $atts['closable']?>,
<?php }?>
<?php if(isset($atts['closeAction'])){?>
	closeAction: '<?php echo $atts['closeAction']?>',
<?php }?>
<?php if(isset($atts['collapseMode'])){?>
	collapseMode: '<?php echo $atts['collapseMode']?>',
<?php }?>
<?php if(isset($atts['header'])){?>
	header: <?php echo $atts['header']?>,
<?php }?>