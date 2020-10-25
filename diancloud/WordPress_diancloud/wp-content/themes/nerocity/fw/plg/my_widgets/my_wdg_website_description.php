<?php
class my_wdg_website_description extends WP_Widget{
    function my_wdg_website_description()
    {
        /* INIT CONSTRUCTOR */
        $widget_ops = array(
            'classname' => 'website-description', 
            'description' => __( 'Website description' , 'myThemes' ) 
        );
        
        $this -> WP_Widget( 'my_wdg_website_description' , myThemes::group() . ' : ' . __( 'Website Description' , 'myThemes' ) , $widget_ops );
    }

    function widget( $args, $instance )
    {
        extract( $args , EXTR_SKIP );
        $title  = !empty( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
        $desc   = !empty( $instance[ 'desc' ] ) ? esc_attr( $instance[ 'desc' ] ) : '';

        echo $before_widget;

        if( !empty( $title ) ){
            echo '<h1><a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( $title ) . ' ' . esc_attr( strip_tags( $desc ) ) . '">';
            echo apply_filters( 'widget_title' , $title , $instance , $this -> id_base );
            echo '</a></h1>';
        }

        if( !empty( $desc ) ){
            echo '<p>' . $desc . '</p>';
        }

        echo $after_widget;
    }

    function update( $new_instance, $old_instance )
    {
        $instance               = $old_instance;
        $instance[ 'title' ]    = esc_attr( strip_tags( $new_instance[ 'title' ] ) );
        $instance[ 'desc' ]     = esc_attr( strip_tags( $new_instance[ 'desc' ] ) );
        return $instance;
    }

    function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => get_bloginfo( 'name' ),
            'desc' => get_bloginfo( 'description' )
        ));
        
        $title  = esc_attr( $instance[ 'title' ] );
        $desc   = esc_attr( $instance[ 'desc' ] );
        
        /* WIDGET TITLE */
        echo '<p>';
        echo '<label for="' . $this -> get_field_id( 'title' ) . '">' . __( 'Title' , 'myThemes' );
        echo '<input type="text" class="widefat" id="' . $this -> get_field_id( 'title' ) . '" name="' . $this -> get_field_name( 'title' ) . '" value="' . $title . '">';
        echo '</label>';
        echo '</p>';

        echo '<p>';
        echo '<label for="' . $this -> get_field_id( 'desc' ) . '">' . __( 'Description' , 'myThemes' );
        echo '<textarea class="widefat" id="' . $this -> get_field_id( 'desc' ) . '" name="' . $this -> get_field_name( 'desc' ) . '">' . $desc . '</textarea>';
        echo '</label>';
        echo '</p>';
    }
}
?>