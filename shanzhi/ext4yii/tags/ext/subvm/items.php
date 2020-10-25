<?php $items=isset($tag['items'])?$tag['items']:array();?>
<?php ##注册Items子组件?>
<?php if(isset($items) && is_array($items) && count($items)){?>
<?php foreach ($items as $item){?>
Ext.getCmp('<?php echo $id?>').add(<?php echo $item['tagId']?>);
<?php }?>
<?php }else{?>
<?php }?>