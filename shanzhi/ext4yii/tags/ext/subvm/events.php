<?php ##on属性注册组件的事件监听?>
<?php ##下面这组主要是表单元素系列的常用事件?>
<?php if(isset($atts['onchang'])){?>
	<?php echo $id?>.on('change', <?php echo $atts['onchang']?>); 
<?php }?>
<?php if(isset($atts['onkeydown'])){?>
	enableKeyEvents : true,
	<?php echo $id?>.on('keydown', <?php echo $atts['onkeydown']?>); 
<?php }?>
<?php if(isset($atts['onkeyup'])){?>
	enableKeyEvents : true,
	<?php echo $id?>.on('keyup', <?php echo $atts['onkeyup']?>); 
<?php }?>
<?php if(isset($atts['focus'])){?>
	<?php echo $id?>.on('focus', <?php echo $atts['focus']?>); 
<?php }?>
<?php if(isset($atts['onselect'])){?>
	<?php echo $id?>.on('select', <?php echo $atts['onselect']?>); 
<?php }?>
<?php if(isset($atts['onenterkey'])){?>
	enableKeyEvents : true,
	<?php echo $id?>.on('specialkey', function(obj,e){
	if (e.getKey() == Ext.EventObject.ENTER) {
	  <?php echo $atts['onenterkey']?>(obj, e);
	}
	}); 
<?php }?>
<?php if(isset($atts['onspecialkey'])){?>
	enableKeyEvents : true,
	<?php echo $id?>.on('specialkey', <?php echo $atts['onspecialkey']?>); 
<?php }?>
<?php ##下面这组主要是容器面板系列的常用事件?>
<?php if(isset($atts['onactivate'])){?>
	<?php echo $id?>.on('activate', <?php echo $atts['onactivate']?>); 
<?php }?>
<?php if(isset($atts['onclose'])){?>
	<?php echo $id?>.on('close', <?php echo $atts['onclose']?>); 
<?php }?>
<?php if(isset($atts['oncollapse'])){?>
	<?php echo $id?>.on('collapse', <?php echo $atts['oncollapse']?>); 
<?php }?>
<?php if(isset($atts['onexpand'])){?>
	<?php echo $id?>.on('expand', <?php echo $atts['onexpand']?>); 
<?php }?>
<?php if(isset($atts['onhide'])){?>
	<?php echo $id?>.on('hide', <?php echo $atts['onhide']?>); 
<?php }?>
<?php if(isset($atts['onrender'])){?>
	<?php echo $id?>.on('render', <?php echo $atts['onrender']?>); 
<?php }?>
<?php if(isset($atts['onshow'])){?>
	<?php echo $id?>.on('show', <?php echo $atts['onshow']?>); 
<?php }?>
<?php ##表格事件 树事件?>
<?php if(isset($atts['onitemclick'])){?>
	<?php echo $id?>.on('itemclick', <?php echo $atts['onitemclick']?>); 
<?php }?>
<?php if(isset($atts['onitemdblclick'])){?>
	<?php echo $id?>.on('itemdblclick', <?php echo $atts['onitemdblclick']?>); 
<?php }?>
<?php if(isset($atts['contextMenuID'])){?>
    <?php echo $id?>.on('itemcontextmenu', function(view, record, item, index, event, options) {
    	event.preventDefault();
    	<?php echo $atts['contextMenuID']?>.showAt(event.getXY());
    });
<?php }?>
<?php ##具有选择功能的组件?>
<?php if(isset($atts['onselectionchange'])){?>
	<?php echo $id?>.on('selectionchange', <?php echo $atts['onselectionchange']?>); 
<?php }?>
<?php ##这个不是事件注册，放在这算特例?>
<?php if(isset($atts['center']) && $atts['center']=="true"){?>
	<?php echo $id?>.center();
<?php }?>