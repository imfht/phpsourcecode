<?php

if( isset( myThemes_acfg::$res[ 'box' ] ) ) {
    myThemes_acfg::$res[ 'box' ] = array();
}

$box = & myThemes_acfg::$res[ 'box' ];

/// ////////////////////////////////////////////////////////////////////////
/* PAGE LAYOUT */
$box[ 'page' ][ 'settings' ] = array(
	'title' => __( 'myThem.es Settings' , 'myThemes' ),
	'context' => 'normal',
	'priority' => 'high',
	'callback' => 'my_box_post_layout',
	'args' => null,
	'onSave' => 'my_box_post_layout_save'
);

$box[ 'post' ][ 'settings' ] = array(
	'title' => __( 'myThem.es Settings' , 'myThemes' ),
	'context' => 'normal',
	'priority' => 'high',
	'callback' => 'my_box_post_layout',
	'args' => null,
	'onSave' => 'my_box_post_layout_save'
);


/* PAGE LAYOUT */
/* SAVE METHOD */
function my_box_post_layout_save( $post_id )
{
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    
    if( isset( $_POST[ 'mythemes-post-layout' ] ) && isset( $_POST[ 'mythemes-use-post-layout' ] ) && (int)$_POST[ 'mythemes-use-post-layout' ] == 1 ) {
		meta::set( $post_id , 'post-layout' , esc_attr( $_POST[ 'mythemes-post-layout' ] ) );
        meta::set( $post_id , 'use-post-layout' , esc_attr( $_POST[ 'mythemes-use-post-layout' ] ) );
		
        switch( $_POST[ 'mythemes-post-layout' ] ) {
            case 'left':
            case 'right': {
                if( isset( $_POST[ 'mythemes-post-sidebar' ] ) ) {
                    meta::set( $post_id , 'post-sidebar' , esc_attr( $_POST[ 'mythemes-post-sidebar' ] ) );
                }
                break;
            }
            default:
                break;
        }
    }
    else{
        if( isset( $_POST[ 'mythemes-use-post-layout' ] ) )
            meta::set( $post_id , 'mythemes-use-post-layout' , esc_attr( $_POST[ 'mythemes-use-post-layout' ] ) );
        
        meta::set( $post_id , 'post-layout' , null );
        meta::set( $post_id , 'post-sidebar' , null );
    }
}

/* DISPLAY BOX */
function my_box_post_layout( $post )
{
    /* SIDEBARS AND LAYOUTS */
    $sidebars = array_merge(
        myThemes::cfg( 'sidebars-list' ),
        (array)myThemes::get( 'sidebars-list' )
    );

    $layouts = array(
        'right'  => get_template_directory_uri() . '/media/admin/images/left.layout.png',
        'left' => get_template_directory_uri() . '/media/admin/images/right.layout.png',
        'full'  => get_template_directory_uri() . '/media/admin/images/full.layout.png'
    );       
    
    /* LAYOUT */
    echo ahtml::template( array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'Use custom layout' , 'myThemes' ),
        'fieldName' => 'use-post-layout',
        'action' => "{'t' : '.mythemes-post-layout' , 'f' : '-' }",
        'value' => meta::get( $post -> ID , 'use-post-layout' )
    ) );
    
    $use_post_layout = meta::get( $post -> ID, 'use-post-layout' );
    if( strlen( $use_post_layout ) == 0 || $use_post_layout === "0" ){
        $classes = 'mythemes-post-layout hidden';
    }else{
        $classes = 'mythemes-post-layout';
    }
    
    if( $post -> post_type == 'post' ){
        $type = 'single';
    }
    else{
        $type = $post -> post_type;
    }

    $layout = meta::dget( $post -> ID , 'post-layout' , myThemes::get( $type . '-layout' ) );

    $rett = ahtml::template( array(
        'type' => array(
            'template' => 'inline',
            'input' => 'imageSelect'
        ),
        'values' => $layouts,
        'coll' => 2,
        'label' => __( 'Select Layout' , 'myThemes' ),
        'fieldName' => 'post-layout',
        'value' => $layout,
        'action' => "[ 'hs' , { 'full' : '.mythemes-layout-sidebar' } ]"
    ) );
	
    if( $layout == 'full' ){
        $sidebarClass = 'mythemes-layout-sidebar hidden';
    }else{
        $sidebarClass = 'mythemes-layout-sidebar';
    }
    
    $rett .= ahtml::template( array(
        'type' => array(
                'template' => 'inline',
                'input' => 'select'
    	),
    	'values' => $sidebars,
    	'label' => __( 'Select sidebar' , 'myThemes' ),
    	'fieldName' => 'post-sidebar',
        'templateClass' => $sidebarClass,
        'value' => meta::dget( $post -> ID , 'post-sidebar' , myThemes::get( $type . '-sidebar' ) )
    ) );
    
    echo ahtml::template( array(
        'type' => array(
            'template' => 'code'
        ),
        'content' => $rett,
        'templateClass' => $classes
    ) );   
}

?>