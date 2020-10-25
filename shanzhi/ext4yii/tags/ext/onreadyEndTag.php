
<?php foreach (self::$elementVOs as $elementVO){ ?>
	<?php // >>界面组件权限授权?>
	<?php if($elementVO['grant_type']=='2'){?>
		<?php ##显示?>
		Ext.getCmp('<?php echo $elementVO['dom_id']?>').show();
	<?php }else if($elementVO['grant_type']=='3'){?>
		<?php ##隐藏?>
		Ext.getCmp('<?php echo $elementVO['dom_id']?>').hide();
	<?php }else if($elementVO['grant_type']=='4'){?>
		<?php ##只读?>
		<?php if($elementVO['type']=='3'){ //##表单元素?>
			App.read(Ext.getCmp('${elementVO.dom_id_}'));
		<?php }?>
	<?php }else if($elementVO['grant_type']=='5'){?>
		<?php ##编辑?>
		<?php if($elementVO['type']=='3'){?>
			App.edit(Ext.getCmp('${elementVO.dom_id_}'));
		<?php }?>
	<?php }else if($elementVO['grant_type']=='6'){?>
		<?php ##禁用?>
		Ext.getCmp('<?php echo $elementVO['dom_id']?>').disable();
	<?php }else if($elementVO['grant_type']=='7'){?>
		<?php ##激活?>
		Ext.getCmp('<?php echo $elementVO['dom_id']?>').enable();
	<?php }?>
	<?php // <<界面组件权限授权?>
<?php }?>
});
</script>

