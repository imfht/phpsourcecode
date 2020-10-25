<?php ##on标签注册组件的事件监听?>
<?php if(self::haveListeners($id)){?>
<?php 
$afterRenderRegisterList=isset(self::$Tags[$id]['afterRenderRegisterList'])?self::$Tags[$id]['afterRenderRegisterList']:array();
$listenerList=isset(self::$Tags[$id]['listenerList'])?self::$Tags[$id]['listenerList']:array();
?>
var <?php echo $id?>_listeners = {
    <?php ##afterrender监听专门用于子组件添加到父组件的操作 ?>
	<?php ##仅针对一些特殊操作，比如Tools、Docked的添加，必须afterrender之后操作。容器组件Items的add和这个没关系。?>
	<?php if(count($afterRenderRegisterList)){?>
afterrender : {
		fn : function() {
    	<?php foreach ($afterRenderRegisterList as $register){?>
    		<?php if($register['type']=="tool"){?>
Ext.getCmp('<?php echo $id?>').addTool(<?php echo $register['id']?>);
    		<?php }else{?>
Ext.getCmp('<?php echo $id?>').addDocked(<?php echo $register['id']?>);
    		<?php }?>
    	<?php }?>
}
		}
	<?php if(count($listenerList)){?>,<?php }?>
	<?php }?>
	<?php ##注册用户自己添加的事件监听?>
	<?php if(count($listenerList)){?>
	<?php foreach ($listenerList as $velocityCount=> $listener){?>
		<?php echo $listener['event']?> : {
			<?php if(isset($listener['delay'])){?>
			    delay : <?php echo $listener['delay']?>,
			<?php }?>
			<?php if(isset($listener['single'])){?>
			    single : <?php echo $listener['single']?>,
			<?php }?>
			<?php if(isset($listener['any'])){?>
                <?php echo $listener['any']?>,
            <?php }?>
				fn : <?php echo $listener['fn']?>,
			}
			<?php if($velocityCount+1 != count($listenerList)){?>,<?php }?>
	<?php }?>
	<?php }?>
};
<?php }else{?>

<?php }?>