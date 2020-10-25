<?php ##处理Border?>
<?php 
$borders=array();
$align=['top','right','bottom','left'];
if(isset($atts['headerBorder'])){
	$headerBorders = explode(" ",$atts['headerBorder']);
	if(count($headerBorders)==4){
		foreach ($headerBorders as $i=> $borderSize){
			$borders[]=array(
					"id"=>self::getUUID4Tag(),
					"element"=>'header',
					"size"=>$borderSize,
					"align"=>$align[$i]
			);
		}
	}	
}
if(isset($atts['bodyBorder'])){
	$bodyBorder = explode(" ",$atts['bodyBorder']);
	if(count($bodyBorder)==4){
		foreach ($bodyBorder as $i=> $borderSize){
			$borders[]=array(
					"id"=>self::getUUID4Tag(),
					"element"=>'body',
					"size"=>$borderSize,
					"align"=>$align[$i]
			);
		}
	}
}
if(isset($atts['splitterBorder'])){
	$splitterBorder = explode(" ",$atts['splitterBorder']);
	if(count($splitterBorder)==5){
		foreach ($splitterBorder as $i=> $borderSize){
			$borders[]=array(
					"id"=>self::getUUID4Tag(),
					"element"=>'body',
					"size"=>$borderSize,
					"align"=>$align[$i],
					"color"=>$splitterBorder[4]
			);
		}
	}
}
?>


<?php if(count($borders)){?>
<?php foreach ($borders as $border){?>
	<?php if($border['element']=="header"){?>
		Ext.util.CSS.createStyleSheet('#<?php echo $id?>_header {border-<?php echo $border['align']?>-width: <?php echo $border['size']?>px !important;}','<?php echo $border['id']?>');
	<?php }?>
	<?php if($border['element']=="body"){?>
		Ext.util.CSS.createStyleSheet('#<?php echo $id?>_body {border-<?php echo $border['align']?>-width: <?php echo $border['size']?>px !important;}','<?php echo $border['id']?>');
	<?php }?>
		<?php if($border['element']=="splitter"){?>
		Ext.util.CSS.createStyleSheet('#<?php echo $id?>_splitter {border-<?php echo $border['align']?>-width: <?php echo $border['size']?>px solid <?php echo $border['color']?>; !important;}','<?php echo $border['id']?>');
	<?php }?>
<?php }?>
<?php }?>