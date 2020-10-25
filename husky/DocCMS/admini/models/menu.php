<?php
require_once(ABSPATH.'/inc/models/menu.php');
require_once(ABSPATH.'/inc/class.paint.php');
require_once(ABSPATH.'/inc/class.upload.php');
class menu extends c_menu{
	function menu_power_list_select($name,$select=null,$style=0)
	{
		global $request;
		$part_common_path=ABSPATH.'/skins/'.STYLENAME.'/common/';
		$temp_arr=rec_listFiles($part_common_path);
		if(!$style){
			echo '<select name="'.$name.'">';
			echo '<option value="common.php">默认样式</option>';
		}
		else{
			echo '<li><a href="javascript:;" onclick="changesel(1,\'common.php\',this)" >默认样式</a></li>';
		}
		if($temp_arr)
		{
			foreach ($temp_arr as $v)
			{
				$selected=($select==$v)?'selected="selected"':'';
				if(!$style){
					echo "<option value='".$v."' ".$selected." >".$v."</option>";
				}
				else
				{
					echo '<li><a href="javascript:;" onclick="changesel(1,\''.$v.'\',this)" >'.$v.'</a></li>';
				}
			}
		}
		if(!$style){
			echo '</select>';
		}
	}
	function menu_list_radio($name,$state=0)
	{
	
		$isHidden =($state==0)?'checked="checked"':'';
		$isShow   =($state==1)?'checked="checked"':'';
		?>
	 <span class="ms2"> 
      显示 <input type="radio" name="radio[<?php echo $name ?>]" value="0" <?php echo $isHidden ?> >
      隐藏 <input type="radio" name="radio[<?php echo $name ?>]" value="1" <?php echo $isShow ?> >
     </span>
		<?php

	}
	function menu_list_select($id,$name,$tempmenus)
	{
	   global $db;	   		 
	   if(!empty($tempmenus))
	   {
		   echo '<select class="msl" name="move_menu['.$name.']" onChange="menu_move(this.options[this.options.selectedIndex].value,\'./index.php?m=system&s=managechannel&a=move\','.$id.')">';
		  echo '<option >移动到...</option>';
		  echo '<option value="0">移动为顶级频道</option>';
		  foreach($tempmenus as $menu){
			 if($menu['id']!=$id)
			  $menus[$menu['id']]=$menu;
		  }
		  $stack = array(0);
		  while($stack) {
			  $parentid = array_pop($stack); 
			  if($parentid) {
				  $v=(object)$menus[$parentid];	
				  echo '<option value="'.$v->id.'" '.$selected.'>'.$menus[$parentid]['prefix'].$v->title.'</option>';											
			  }
			  $substack = array();      
			  foreach($menus as $id=>$menu) {            
				  if($menu['parentId']==$parentid) {                 
					  $menus[$id]['prefix'] = $menus[$parentid]['prefix'].'----';             
					  array_push($substack,$id);   
				  }      
			  }      
			  $substack = array_reverse($substack);      
			  $stack = array_merge($stack,$substack);
		  }
		  echo '</select>';
		}
	}
}
?>