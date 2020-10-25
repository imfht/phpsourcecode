<?php 
/*
 *自定义内容类型
 *产品
 */
function add_product_post_type(){
	//标签文本
	$labels = array(
		'name'               => __( 'Product', 'ets' ),
		'singular_name'      => __( 'Product', 'ets' ),
		'menu_name'          => __( 'Product', 'ets' ),
		'name_admin_bar'     => __( 'Product', 'ets' ),
		'add_new'            => __( 'Add New Product', 'ets' ),
		'add_new_item'       => __( 'Add New Product', 'ets' ),
		'new_item'           => __( 'New Product', 'ets' ),
		'edit_item'          => __( 'Edit Product', 'ets' ),
		'view_item'          => __( 'View Product', 'ets' ),
		'all_items'          => __( 'All Products', 'ets' ),
		'search_items'       => __( 'Search Products', 'ets' ),
		'parent_item_colon'  => __( 'Parent Product:', 'ets' ),
		'not_found'          => __( 'No Products found.', 'ets' ),
		'not_found_in_trash' => __( 'No Products found in Trash.', 'ets' )
	);
	//内容类型设置
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'product' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		// 'taxonomy'			 => array('product_cate','post_tag'),
	);
	register_post_type( 'product',$args);
}
add_action('init','add_product_post_type');

/*
 *自定义分类法：产品分类
 */
function add_product_tax(){
	// 标签
	$labels = array(
		'name'                       => __( 'Product Category', 'ets' ),
		'singular_name'              => __( 'Product Category', 'ets' ),
		'menu_name'                  => __( 'Product Category', 'ets' ),
		'all_items'                  => __( 'All Items', 'ets' ),
		'parent_item'                => __( 'Parent Item', 'ets' ),
		'parent_item_colon'          => __( 'Parent Item:', 'ets' ),
		'new_item_name'              => __( 'New Item Name', 'ets' ),
		'add_new_item'               => __( 'Add New Item', 'ets' ),
		'edit_item'                  => __( 'Edit Item', 'ets' ),
		'update_item'                => __( 'Update Item', 'ets' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'ets' ),
		'search_items'               => __( 'Search Items', 'ets' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'ets' ),
		'choose_from_most_used'      => __( 'Choose from the most used items', 'ets' ),
		'not_found'                  => __( 'Not Found', 'ets' ),
	);
	// 参数
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'product_cate', array( 'product' ), $args );
}
add_action('init', 'add_product_tax' );

/*
 *自定义分类法：产品标签
 */
function add_product_tag(){
	// 标签
	$labels = array(
		'name'                       => __( 'Product Tag', 'ets' ),
		'singular_name'              => __( 'Product Tag', 'ets' ),
		'menu_name'                  => __( 'Product Tag', 'ets' ),
		'all_items'                  => __( 'All Tags', 'ets' ),
		'parent_item'                => __( 'Parent Tag', 'ets' ),
		'parent_item_colon'          => __( 'Parent Tag:', 'ets' ),
		'new_item_name'              => __( 'New Tag Name', 'ets' ),
		'add_new_item'               => __( 'Add New Tag', 'ets' ),
		'edit_item'                  => __( 'Edit Tag', 'ets' ),
		'update_item'                => __( 'Update Tag', 'ets' ),
		'separate_items_with_commas' => __( 'Separate tags with commas', 'ets' ),
		'search_items'               => __( 'Search Tags', 'ets' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'ets' ),
		'choose_from_most_used'      => __( 'Choose from the most used tags', 'ets' ),
		'not_found'                  => __( 'Not Found', 'ets' ),
	);
	// 参数
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'product_tag', array( 'product' ), $args );
}
add_action('init', 'add_product_tag' );