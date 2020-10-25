<a class="list-group-item top">产品分类</a>
<?php
	$sql="select * from ".DB_PREFIX."product order by sort desc";
	$query=$db->query($sql);
	$num=$query->rowCount();
	if($num>0){
	while($arr=$query->fetch()){
?>
        <a style="<?php if($_GET['cat']==$arr['id']){$cat_url=URL.'/list/'.$id_tmp.'/cat/'.$arr['id'];echo 'background:#f5f5f5';} ?>" class="list-group-item list" href="
        	<?php
        	if(!empty($id_tmp)){
        		echo URL.'/list/'.$id_tmp.'/cat/'.$arr['id'];	
        	}
        	?>
        	">
          <?php echo $arr['name']; ?> <span class="pull-right small glyphicon glyphicon-chevron-right leftSideBar"></span>
        </a>
<?php	}}	?>
<div style="clear:both;margin-bottom:10px"></div>