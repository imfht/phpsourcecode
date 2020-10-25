<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::HTMLEDITOR);

$xtype=self::HTMLEDITOR;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##HtmlEditor定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['enableAlignments'])){?>
enableAlignments:<?php echo $atts['enableAlignments']?>,
<?php }?>
<?php if(isset($atts['enableColors'])){?>
enableColors:<?php echo $atts['enableColors']?>,
<?php }?>
<?php if(isset($atts['enableFont'])){?>
enableFont:<?php echo $atts['enableFont']?>,
<?php }?>
<?php if(isset($atts['enableFontSize'])){?>
enableFontSize:<?php echo $atts['enableFontSize']?>,
<?php }?>
<?php if(isset($atts['enableFormat'])){?>
enableFormat:<?php echo $atts['enableFormat']?>,
<?php }?>
<?php if(isset($atts['enableLinks'])){?>
enableLinks:<?php echo $atts['enableLinks']?>,
<?php }?>
<?php if(isset($atts['enableSourceEdit'])){?>
enableSourceEdit:<?php echo $atts['enableSourceEdit']?>,
<?php }?>
    fontFamilies:["微软雅黑", "宋体", "黑体","Arial", "Times New Roman"],
    app:169	
};
<?php ##HtmlEditor实例化?>
var <?php echo $id?> = Ext.create('Ext.form.field.HtmlEditor',<?php echo $id?>_cfg);

<?php ## 编辑器里的工具栏边框缺省情况下会和外层容器边框重叠，这里做特殊处理?>
<?php if(isset($atts['hideToolbarBorder']) && $atts['hideToolbarBorder']=="true"){?>
Ext.util.CSS.createStyleSheet('.x-html-editor-tb {border-style: none;padding: 5px 0 2px 5px;}');
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>

