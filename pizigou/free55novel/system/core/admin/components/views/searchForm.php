<style>
.portlet{display:inline;}
.portlet-content{display:inline;}
</style>
<form action="<?php echo Yii::app()->createUrl(Yii::app()->controller->id.'/index',array('menupanel'=>$_GET['menupanel']));?>" name="searchForm" style="display:inline;margin-left:10px;">
<?php 
if(Yii::app()->controller->id==='user')
	echo '<b>用户名</b>';
else 
	echo '<b>标题</b>';
echo '&nbsp;'.CHtml::textField('title',$_GET['title']).'&nbsp;';
echo CHtml::dropDownList('cid',$_GET['cid'],$categorys).'&nbsp;&nbsp;';?>
<button class="sexybutton sexysimple sexylarge" onclick="this.form.submit();">查询</button>
</form>