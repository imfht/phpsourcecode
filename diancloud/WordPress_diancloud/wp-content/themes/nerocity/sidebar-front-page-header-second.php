<?php
    if ( dynamic_sidebar( 'front-page-header-second' ) ){
        /* IF NOT EMPTY */    
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget widget_text">';
            echo '<h3>Block Model</h3>';
            echo '<div class="textwidget">With Verbo free WordPress theme you can easily combine components in a variety ways for different design projects. It\'s easy!</div>';
            echo '</div>';
        }
    }
?>