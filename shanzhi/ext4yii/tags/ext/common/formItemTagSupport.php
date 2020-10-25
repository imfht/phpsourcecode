<?php 
$parentTag=self::getParentTag($id);
$tag=self::$Tags[$id];
$atts['instance']="true";
if($parentTag['type'] == self::COLUMN){
	$atts['instance']="false";
	self::addTagAttribute($parentTag['tagId'],"editor2",$id."_cfg");
}
$atts['fieldCls'] = "app-form-fieldcls";
if($tag['type']==self::CHECKBOXFIELD || $tag['type']==self::RADIOBOXFIELD){
	unset($atts['fieldCls']);
}

?>
<?php require 'componentTagSupport.php';?>
<?php ##表单元素输入框区域样式 fieldCls属性：去掉输入框内区域的立体效果。选择框不能应用此样式。?>
<?php if(isset($atts['fieldCls'])){?>
    fieldCls : '<?php echo $atts['fieldCls']?>',
<?php }?>
<?php if(isset($atts['fieldLabel'])){?>
    fieldLabel:'<?php echo $atts['fieldLabel']?>',
<?php }?>
<?php if(isset($atts['name'])){?>
    name:'<?php echo $atts['name']?>',
<?php }?>
<?php if(isset($atts['value'])){?>
    value:'<?php echo $atts['value']?>',
<?php }?>
<?php if(isset($atts['emptyText'])){?>
    emptyText:'<?php echo $atts['emptyText']?>',
<?php }?>
<?php if(isset($atts['anchor'])){?>
    anchor:'<?php echo $atts['anchor']?>',
<?php }?>
<?php if(isset($atts['labelWidth'])){?>
    labelWidth:<?php echo $atts['labelWidth']?>,
<?php }?>
<?php if(isset($atts['labelAlign'])){?>
    labelAlign:'<?php echo $atts['labelAlign']?>',
<?php }?>
<?php if(isset($atts['labelPad'])){?>
    labelPad:<?php echo $atts['labelPad']?>,
<?php }?>
<?php if(isset($atts['allowBlank'])){?>
    allowBlank:<?php echo $atts['allowBlank']?>,
<?php }?>
<?php if(( !isset($atts['allowBlank']) || (isset($atts['allowBlank']) &&  $atts['allowBlank']=="false")) && isset($atts['star']) && $atts['star']=="true"){?>
    afterLabelTextTpl:x_field_required,
<?php }?>
<?php if(isset($atts['tabIndex'])){?>
    tabIndex:<?php echo $atts['tabIndex']?>,
<?php }?>
<?php if(isset($atts['inputType'])){?>
    inputType:<?php echo $atts['inputType']?>,
<?php }?>
<?php if(isset($atts['readOnly'])){?>
    readOnly:<?php echo $atts['readOnly']?>,
<?php }?>
<?php if(isset($atts['vtype'])){?>
    vtype:<?php echo $atts['vtype']?>,
<?php }?>
<?php if(isset($atts['regex'])){?>
    regex:<?php echo $atts['regex']?>,
<?php }?>
<?php if(isset($atts['regexText'])){?>
    regexText:<?php echo $atts['regexText']?>,
<?php }?>
<?php if(isset($atts['editable'])){?>
    editable:<?php echo $atts['editable']?>,
<?php }?>
<?php if(isset($atts['msgTarget'])){?>
    msgTarget:<?php echo $atts['msgTarget']?>,
<?php }?>
<?php if(isset($atts['hideTrigger'])){?>
    hideTrigger:<?php echo $atts['hideTrigger']?>,
<?php }?>
<?php if(isset($atts['blankText'])){?>
    blankText:<?php echo $atts['blankText']?>,
<?php }?>
<?php if(isset($atts['selectOnFocus'])){?>
    selectOnFocus:<?php echo $atts['selectOnFocus']?>,
<?php }?>
<?php if(!isset($atts['padding']) && isset($parentTag['atts']['rowSpace'])){?>
    padding : '0 0 <?php echo $parentTag['atts']['rowSpace']?> 0',
<?php }?>