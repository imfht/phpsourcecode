<?php require 'baseTagSupport.php';?>
<?php if(isset($xtype)){?>
	xtype : '<?php echo $xtype?>',
<?php }?>
<?php if(isset($aboutme)){?>
	aboutme : '<?php echo $aboutme?>',
<?php }?>
<?php if(self::haveListeners($id)){?>
	listeners : <?php echo $id?>_listeners,
<?php }?>
