<?php
class my_wdg_post_categories extends WP_Widget {
    
    function my_wdg_post_categories() {
        
        /* INIT CONSTRUCTOR */
        $widget_ops = array(
            'classname' => 'widget_post_categories', 
            'description' => __( 'Use only for single template' , 'myThemes' ) 
        );
        
        $this -> WP_Widget( 'my_wdg_post_categories' , myThemes::group() . ' : ' . __( 'Post Categories' , 'myThemes' ) , $widget_ops );
    }

    function widget( $args, $instance )
    {
        /* PRINT THE WIDGET */
        extract( $args , EXTR_SKIP );
        
        $title  = !empty( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
        
        if( is_singular( 'post' ) && has_category( ) ){
            echo $before_widget;

            if( !empty( $title ) ){
                echo $before_title;
                echo apply_filters( 'widget_title' , $title , $instance , $this -> id_base );
                echo $after_title;
            }
            echo '<div>';
            echo '<ul>';
            echo '<li>';
            the_category( '</li><li>' );
            echo '</li>';
            echo '</ul>';
            echo '</div>';

            echo $after_widget;
        }
    }

    function update( $new_instance, $old_instance )
    {
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
        return $instance;
    }

    function form( $instance )
    {
        /* PRINT WIDGET FORM */
        $instance = wp_parse_args( (array) $instance, array(
            'title' => ''
        ));
        
        $title = esc_attr( $instance[ 'title' ] );
        
        echo '<p>';
        echo '<label for="' . $this -> get_field_id( 'title' ) . '">' . __( 'Title' , 'myThemes' );
        echo '<input type="text" class="widefat" id="' . $this -> get_field_id( 'title' ) . '" name="' . $this -> get_field_name( 'title' ) . '" value="' . $title . '"/>';
        echo '</label>';
        echo '</p>';
    }
}
?>