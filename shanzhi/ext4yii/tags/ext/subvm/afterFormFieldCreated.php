<?php if(isset($atts['readOnly'])=="true"){?>
	App.read(<?php echo $id?>);
<?php }?>
<?php if(isset($atts['disable'])=="true"){?>
	App.disable(<?php echo $id?>);
<?php }?>