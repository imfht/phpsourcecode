<?php
    class header{

        static function setup()
        {
            $args = array(
                'default-image'          => get_template_directory_uri() . '/media/img/header.jpg',
                'random-default'         => false,
                'width'                  => 2600,
                'height'                 => 500,
                'flex-height'            => true,
                'flex-width'             => true,
                'default-text-color'     => 'ffffff',
                'header-text'            => true,
                'uploads'                => true,
                'wp-head-callback'       => array( 'header' , 'custom_script' ),
                'admin-head-callback'    => array( 'header' , 'admin' ),
                'admin-preview-callback' => array( 'header' , 'preview' )
            );

            add_theme_support( 'custom-header', $args );
        }

        static function preview()
        {
            get_template_part( 'cfg/templates/header' );
        }

        static function admin()
        {
            wp_enqueue_style( 'mythemes-google-fonts',          'http://fonts.googleapis.com/css?family=Quicksand:300,400,700|Roboto:400,300,100,500,700&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese' );
            wp_enqueue_style( 'mythemes-effects',       get_template_directory_uri() . '/media/css/effects.css' );
            wp_enqueue_style( 'mythemes-header',        get_template_directory_uri() . '/media/css/header.css' );

            $first_color    = myThemes::get( 'first-color' );
            $second_color   = myThemes::get( 'second-color' );
            ?>
                <style>
                    div.mythemes-header div.overflow-wrapper,
                    div.mythemes-header div.overflow-wrapper:after,
                    div.mythemes-header div.overflow-wrapper::before,
                    div.mythemes-header div.overflow-wrapper:before{
                        -moz-box-sizing: border-box;
                             box-sizing: border-box;
                    }
                    div.mythemes-header div.overflow-wrapper{
                        margin: 0px;
                    }
                    .mythemes-header-animation .mythemes-logo{
                        color: #<?php echo get_header_textcolor(); ?>;
                    }
                    .mythemes-header-animation .mythemes-description{
                        color: rgba(<?php echo mythemes_tools::hex2rgb( get_header_textcolor() ); ?> , 0.65 );
                    }
                    div.mythemes-header .valign-cell p.buttons a.btn{
                        background: <?php echo myThemes::get( 'first-color' ); ?>
                    }
                    div.mythemes-header .valign-cell p.buttons a.btn.second-button{
                        background: <?php echo myThemes::get( 'second-color' ); ?>
                    }
                    /* DARK BORDER BOTTOM */
                    .btn{
                        font-family: Quicksand, sans-serif, Arial, serif;
                        font-size: 13px;

                        padding: 12px 25px;
                        border-bottom: 2px solid <?php echo mythemes_tools::brightness( $first_color , -40 ); ?>;
                    }

                    .btn.second-button{
                        border-bottom: 2px solid <?php echo mythemes_tools::brightness( $second_color , -40 ); ?>;
                    }
                </style>
            <?php

            
        }

        static function custom_script(){
            get_template_part( 'cfg/templates/custom-script' );
        }
    }
?>