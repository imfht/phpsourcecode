<?php
    if ( dynamic_sidebar( 'front-page-header-first' ) ){
        /* IF NOT EMPTY */
        
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget widget_text">';
            echo '<h3>Many Components</h3>';
            echo '<div class="textwidget">There are a lot of different components that will help you to make a perfect suit for startup project with WordPress theme Verbo.</div>';
            echo '</div>';
        }
    }
?>