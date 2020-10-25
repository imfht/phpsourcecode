<?php 
// skill post type
function skill_post_type(){
	$skill_labels = array(
		'name'=>'技术',
		// 'singular_name' => '一篇技术文章',
		'name_admin_bar'=> '写篇新文章',
		'all_items'		=> '所有技术文章',
		// 'add_new'		=> '新建文章',
		// 'add_new_item'	=> '新建文章',
		// 'edit_item'		=> '编辑该项目',
		// 'new_item'		=> '新建文章',
		'view_item'		=> '查看这篇技术',
		// 'search_items'	=> '搜索项目',
		// 'not_found'		=> '未找到',
		// 'not_found_in_trash'=> '结果未找到',
		// 'parent_item_colon'=> '',

		);
	$skill_args = array(
		'label' 		=> '技术',
		'labels'		=> $skill_labels,
		'description'	=> 'my skill post',
		'public'		=> true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'menu_position' => 5,
		'menu_icon'	=>'dashicons-lightbulb',
		'hierarchical' => false,
		'supports'	=> array('title','editor','thumbnail'),
		);
	register_post_type( 'skill', $skill_args );
}
add_action( 'init', 'skill_post_type' );