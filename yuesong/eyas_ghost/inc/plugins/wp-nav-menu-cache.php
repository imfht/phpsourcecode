<?php 
/*
Plugin Name: WP Nav Menu Cache
Plugin URI: http://blog.wpjam.com/m/wpjam_nav_menu/ 
Description: 使用内存缓存提高 wp_nav_menu 效率
Version: 0.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/
//给 wp_nav_menu 加上对象缓存，加快效率
function ey_wp_nav_menu_cache( $args = array() ) {
    static $menu_id_slugs = array();

    $defaults = array( 
        'menu' => '', 
        'container' => 'div', 
        'container_class' => '', 
        'container_id' => '', 
        'menu_class' => 'menu', 
        'menu_id' => '',
        'echo' => true, 
        'fallback_cb' => 'wp_page_menu', 
        'before' => '', 'after' => '', 
        'link_before' => '', 
        'link_after' => '', 
        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth' => 0, 'walker' => '', 
        'theme_location' => '' 
        );

    $args = wp_parse_args( $args, $defaults );
    $args = apply_filters( 'wp_nav_menu_args', $args );
    $args = (object) $args;

    // Get the nav menu based on the requested menu
    $menu = wp_get_nav_menu_object( $args->menu );

    // Get the nav menu based on the theme_location
    if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) )
        $menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

    // get the first menu that has items if we still can't find a menu
    if ( ! $menu && !$args->theme_location ) {
        $menus = wp_get_nav_menus();
        foreach ( $menus as $menu_maybe ) {
            if ( $menu_items = wpjam_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
                $menu = $menu_maybe;
                break;
            }
        }
    }

    // If the menu exists, get its items.
    if ( $menu && ! is_wp_error($menu) && !isset($menu_items) )
        $menu_items = wpjam_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

    /*
     * If no menu was found:
     *  - Fallback (if one was specified), or bail.
     *
     * If no menu items were found:
     *  - Fallback, but only if no theme location was specified.
     *  - Otherwise, bail.
     */
    if ( ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) && !$args->theme_location ) )
        && $args->fallback_cb && is_callable( $args->fallback_cb ) )
            return call_user_func( $args->fallback_cb, (array) $args );

    if ( !$menu || is_wp_error( $menu ) || empty( $menu_items ) )
        return false;

    $nav_menu = $items = '';

    $show_container = false;
    if ( $args->container ) {
        $allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
        if ( in_array( $args->container, $allowed_tags ) ) {
            $show_container = true;
            $class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
            $id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
            $nav_menu .= '<'. $args->container . $id . $class . '>';
        }
    }

    // Set up the $menu_item variables
    _wp_menu_item_classes_by_context( $menu_items );

    $sorted_menu_items = array();
    foreach ( (array) $menu_items as $key => $menu_item )
        $sorted_menu_items[$menu_item->menu_order] = $menu_item;

    unset($menu_items);

    $sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

    $items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
    unset($sorted_menu_items);

    // Attributes
    if ( ! empty( $args->menu_id ) ) {
        $wrap_id = $args->menu_id;
    } else {
        $wrap_id = 'menu-' . $menu->slug;
        while ( in_array( $wrap_id, $menu_id_slugs ) ) {
            if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) )
                $wrap_id = preg_replace('#-(\d+)$#', '-' . ++$matches[1], $wrap_id );
            else
                $wrap_id = $wrap_id . '-1';
        }
    }
    $menu_id_slugs[] = $wrap_id;

    $wrap_class = $args->menu_class ? $args->menu_class : '';

    // Allow plugins to hook into the menu to add their own <li>'s
    $items = apply_filters( 'wp_nav_menu_items', $items, $args );
    $items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );

    $nav_menu .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
    unset( $items );

    if ( $show_container )
        $nav_menu .= '</' . $args->container . '>';

    $nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

    if ( $args->echo )
        echo $nav_menu;
    else
        return $nav_menu;
}
function wpjam_get_nav_menu_items( $menu, $args = array() ) {
    $menu = wp_get_nav_menu_object( $menu );
    
    $menu_items = get_transient('wpjam_nav_menu_'.$menu->term_id);
    if($menu_items === false){
        $menu_items = wp_get_nav_menu_items( $menu->term_id, $args );
        set_transient('wpjam_nav_menu_'.$menu->term_id, $menu_items, 3600);
    }

    return $menu_items;
}

//后台更新自定义菜单的时候，更新缓存
add_action( 'wp_update_nav_menu', 'wpjam_update_nav_menu' );
function wpjam_update_nav_menu( $menu_id = null, $menu_data = null ) {
    delete_transient('wpjam_nav_menu_'.$menu_id);
}