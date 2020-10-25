<?php 
$tag=self::getPairTag(self::TABPANEL);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::TABPANEL;
if(!isset($atts['tabBarHeight'])){
	if(self::getExtSkin()=="neptune"){
		$atts['tabBarHeight']=30;
	}else{
		$atts['tabBarHeight']=25;
	}
}
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##TabPanel定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/panelTagSupport.php';?>
<?php if(isset($atts['plain'])){?>
plain: <?php echo $atts['plain']?>,
<?php }?>
<?php if(isset($atts['tabPosition'])){?>
tabPosition: <?php echo $atts['tabPosition']?>,
<?php }?>
<?php if(isset($atts['tabBarHeight'])){?>
    tabBar : {
    	height : <?php echo $atts['tabBarHeight']?>,
    	defaults : {
    		height : <?php echo $atts['tabBarHeight']?> - 2
    	}
    },
<?php }?>
	app: 169
};
<?php ##TabPanel实例化?>
var <?php echo $id?> = Ext.create('Ext.tab.Panel',<?php echo $id?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
<?php ##当前活动页在此用函数方式处理，规避配置项处理初始化时的一点显示瑕疵?>
<?php if(isset($atts['activeTab'])){?>
    <?php echo $id?>.setActiveTab(<?php echo $atts['activeTab']?>);
<?php }?>
<?php ##活动页的高亮下边框样式?>
<?php if(isset($atts['activeTabBarColor'])){?>
    Ext.util.CSS.createStyleSheet('.x-tab-default-<?php echo $atts['tabPosition']?>-active {border-<?php echo $atts['tabPosition']?>: 2px solid <?php echo $atts['activeTabBarColor']?>;}','<?php echo $id?>_style');
<?php }?>
<?php ##处理Border?>
<?php require dirname(__FILE__).'/subvm/borders.php';?>

