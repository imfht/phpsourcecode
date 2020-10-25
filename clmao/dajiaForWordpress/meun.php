<div  class="nav clear "  cur="0">
        <ul>
         <?php  $str=wp_nav_menu(
				array(
				'theme_location'  => '', //指定显示的导航名，如果没有设置，则显示第一个
				'menu'            => '',
				'container'       => '', //最外层容器标签名
				'container_class' => 'primary', //最外层容器class名
				'container_id'    => '',//最外层容器id值
				'menu_class'      => 'sf-menu', //ul标签class
				'menu_id'         => '',//ul标签id
				'echo'            => false,//是否打印，默认是true，如果想将导航的代码作为赋值使用，可设置为false
				'fallback_cb'     => 'wp_page_menu',//备用的导航菜单函数，用于没有在后台设置导航时调用
				'before'          => '',//显示在导航a标签之前
				'after'           => '',//显示在导航a标签之后
				'link_before'     => '',//显示在导航链接名之后
				'link_after'      => '',//显示在导航链接名之前
				'items_wrap'      => '<ul id="%1$s">%3$s</ul>',
				'depth'           => 1,////显示的菜单层数，默认0，0是显示所有层
				'walker'          => '')); 

				$str=preg_replace("/<ul[^>]*>/", "", $str,1);
				$str=str_replace("</ul>", "", $str);
				echo $str;

		?>
        </ul>
       
      </div>
    <div style="max-width:635px;overflow:hidden;margin-top:5px;">
	<?php  echo get_option('mytheme_meun_bottom');?>
	</div>