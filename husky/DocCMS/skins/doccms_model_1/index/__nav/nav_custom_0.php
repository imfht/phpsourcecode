<?php
/*初始化参数
*  by grysoft (狗头巫师)
*  QQ:767912290
*  nav_call_custom 自定义菜单标签
*  样式文件存于 index文件夹下 nav_call_custom_style.php 中。
*  
*  此标签内置一递增变量 $i ,以方便制作各种样式的菜单, 此变量可在此文件中任意地方调用。;
* 
*/
$select ='class="sideBarHover"';  //选中状态的样式，若无选中状态，可不添
$target ='target="_blank"'; //外链则弹出新窗口，若不需弹出新窗口可清空此变量。
$ico = ispic($data['originalPic'])?'<img src="'.$data['originalPic'].'" />':''; //栏目图标，可在后台栏目缩略图处上传
$target = $data['isTarget']?$target:'';
?>
<li <?php echo $select?>><a href="<?php echo $url?>" <?php echo $target?>><?php echo $data['title'];?></a></li>
