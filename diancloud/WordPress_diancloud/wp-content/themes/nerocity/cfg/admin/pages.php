<?php
    $pages = & myThemes_acfg::$pages;
    
    $pages = array(
        /* MAIN PAGE */
        'mythemes-general' => array(
            'menu' => array(
                'label' => __( 'General' , 'myThemes' ),
                'settings' => __( 'Themes Options' , 'myThemes' ),
            ),
            'title' => __( 'General' , 'myThemes' ),
            'description' => '',
            'content' => array(),
        )
    );
?>