<?php
    class myThemes{

        /* READ ADMIN SETTINGS */
        static function get( $optName , $strip = false )
        {
            return sett::get( 'mythemes-' . $optName , $strip );
        }
        
        static function pget( $optName )
        {
            if( isset( $_POST ) && isset( $_POST[ 'mythemes-' . $optName ] ) )
                return $_POST[ 'mythemes-' . $optName ];
            else
                return self::get( $optName );
        }
        
        /* READ THEME SETTINGS */
        static function cfg( $sett )
        {
            $result = '';
            $file = get_template_directory() . '/cfg/static.php';
            
            if( file_exists( $file ) ){
                include $file;
                
                if( isset( $cfg[ $sett ] ) ){
                    $result = $cfg[ $sett ];
                }
            }
            
            return $result;
        }
       
        static function isDedicatedPage( $pageID )
        {
            $pages = self::cfg( 'pages' );
            
            if( !empty( $pages ) ){
                foreach( $pages as $pageSlug => $p ){
                    if( $p[ 'pageID' ] == $pageID ){
                        return true;
                    }
                }
            }
        }
        
        static function getPageInfo( $pageID )
        {
            $pages = self::cfg( 'pages' );
            
            if( !empty( $pages ) ){
                foreach( $pages as $pageSlug => $p ){
                    if( $p[ 'pageID' ] == $pageID ){
                        
                        /* INCLUDE FILE */
                        if( isset( $p[ 'content' ][ 'location' ] ) && file_exists( $p[ 'content' ][ 'location' ] ) ){
                            include $p[ 'content' ][ 'location' ];
                        }
                            
                        /* RUN METHOD */
                        if( isset( $p[ 'content' ][ 'method' ] ) ){
                            if( class_exists( $p[ 'content' ][ 'object' ] ) && method_exists( $p[ 'content' ][ 'object' ] , $p[ 'content' ][ 'method' ] ) ){
                                if( isset( $p[ 'content' ][ 'params' ] ) ){
                                    return call_user_func_array( array( new $p[ 'content' ][ 'object' ] , $p[ 'content' ][ 'method' ] ) , array( $p[ 'content' ][ 'params' ] ) );
                                }
                                else{
                                    return call_user_func_array( array( new $p[ 'content' ][ 'object' ] , $p[ 'content' ][ 'method' ] ) , array( null ) );
                                }
                            }
                        }
                    }
                }
            }
        }
        
        /* INIT FILTERS */
        static function init_filters()
        {
            $filters = self::cfg( 'filters' );
            if( !empty( $filters ) && is_array( $filters ) ){
                foreach( $filters as $filter => & $d ){
                    add_filter( $filter , $d );
                }
            }
        }
        
        /* INIT ACTIONS */
        static function init_actions()
        {
            $actions = self::cfg( 'actions' );
            if( !empty( $actions ) && is_array( $actions ) ){
                foreach( $actions as $action => & $d ){
                    add_action( $action , $d );
                }
            }
        }
        
        /* INIT SCRIPTS */
        static function init_scripts()
        {
            wp_enqueue_style( 'mythemes-google-fonts',          'http://fonts.googleapis.com/css?family=Quicksand:300,400,700|Roboto:400,300,100,500,700&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese' );
            wp_enqueue_style( 'mythemes-style',                 get_stylesheet_uri() );
            wp_enqueue_style( 'mythemes-fontello',              get_template_directory_uri() . '/media/css/fontello.css' );
            wp_enqueue_style( 'mythemes-bootstrap',             get_template_directory_uri() . '/media/css/bootstrap.min.css' );
            wp_enqueue_style( 'mythemes-typography',            get_template_directory_uri() . '/media/css/typography.css');
            wp_enqueue_style( 'mythemes-navigation',            get_template_directory_uri() . '/media/css/navigation.css' );
            wp_enqueue_style( 'mythemes-effects',               get_template_directory_uri() . '/media/css/effects.css' );
            wp_enqueue_style( 'mythemes-menu-effects',          get_template_directory_uri() . '/media/css/menu-effects.css' );
            wp_enqueue_style( 'mythemes-header',                get_template_directory_uri() . '/media/css/header.css' );
            wp_enqueue_style( 'mythemes-nav',                   get_template_directory_uri() . '/media/css/nav.css' );
            wp_enqueue_style( 'mythemes-blog',                  get_template_directory_uri() . '/media/css/blog.css' );
            wp_enqueue_style( 'mythemes-forms',                 get_template_directory_uri() . '/media/css/forms.css' );
            wp_enqueue_style( 'mythemes-elements',              get_template_directory_uri() . '/media/css/elements.css' );
            wp_enqueue_style( 'mythemes-widgets',               get_template_directory_uri() . '/media/css/widgets.css' );
            wp_enqueue_style( 'mythemes-comments',              get_template_directory_uri() . '/media/css/comments.css' );
            wp_enqueue_style( 'mythemes-comments-typography',   get_template_directory_uri() . '/media/css/comments-typography.css' );
            wp_enqueue_style( 'mythemes-footer',                get_template_directory_uri() . '/media/css/footer.css' );
            wp_enqueue_style( 'mythemes-pretty-photo',          get_template_directory_uri() . '/media/css/prettyPhoto.css' );
            wp_enqueue_style( 'mythemes-plugins',               get_template_directory_uri() . '/media/css/plugins.css' );

            wp_enqueue_script( 'bootstrap' , get_template_directory_uri() . '/media/js/bootstrap.min.js' , array( 'jquery' ) );
            wp_enqueue_script( 'mousewheel' , get_template_directory_uri() . '/media/js/jquery.mousewheel.js' );
            wp_enqueue_script( 'functions' , get_template_directory_uri() . '/media/js/functions.js' );
            wp_enqueue_script( 'backstretch' , get_template_directory_uri() . '/media/js/jquery.backstretch.min.js' );
            
            wp_enqueue_script( 'pretty-photo' , get_template_directory_uri() . '/media/js/jquery.prettyPhoto.js' );
            wp_enqueue_script( 'settings-pretty-photo' , get_template_directory_uri() . '/media/js/settings.prettyPhoto.js' );

            /* INCLUDE FOR REPLY COMMENTS */
            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
                    wp_enqueue_script( 'comment-reply' );
        }
        
        /* REGISTER THEME MENUS */
        static function reg_menus( )
        {
            register_nav_menus( self::cfg( 'menus' ) );
        }
        
        /* REGISTER THEME SIDEBARS */
        static function reg_sidebars( )
        {
            $sidebars = self::cfg( 'sidebars' );

            if( !empty( $sidebars ) && is_array( $sidebars ) ){
                foreach( $sidebars as $sidebar ){
                    register_sidebar( $sidebar );
                }
            }
            
            /* CUSTOM SIDEBARS */
            $custom = mythemes::get( self::cfg( 'custom-sidebars' ) );
            if( !empty( $custom ) && is_array( $custom ) ){
                foreach( $custom as $s ){
                    $sidebars[0][ 'name' ] = $s;
                    $sidebars[0][ 'id' ] = strtolower( str_replace( ' ' , '-' , $s ) );
                    $sidebars[0][ 'description' ] = __( 'Additional custom sidebar' , 'myThemes' );
                    register_sidebar( $sidebars[ 0 ] );
                }
            }
        }
        
        function sidebars()
        {
            $sidebars = array( 'main-sidebar' => __( 'Main sidebar' , 'myThemes' ) );
            $custom = sett::get( self::cfg( 'custom-sidebars' ) );
            if( !empty( $custom ) ){
                foreach( $custom as $s ){
                    $sidebars[ strtolower( str_replace( ' ' , '-' , $s ) ) ] = $s;
                }
            }
            return $sidebars;
        }

        static function setup()
        {
            global $content_width;
    
            $content_width = 1140;

            add_theme_support( 'content-width', $content_width );
            add_theme_support( 'custom-background', array(
                'default-color' => '090909'
            ));

            add_theme_support( 'automatic-feed-links' );
            add_theme_support( 'post-thumbnails' );

            load_theme_textdomain( 'myThemes' );
            load_theme_textdomain( 'myThemes' , get_template_directory() . '/media/languages' );
            load_child_theme_textdomain( 'myThemes' );

            add_editor_style();

            add_image_size( 'full-thumbnail' , 1140 , 410 , true );
            add_image_size( 'grid-thumbnail' , 555 , 440 , true );

            header::setup();
        }
        
        static function rssThumbnail( $content )
        {
            global $post;
            if ( has_post_thumbnail( $post->ID ) ){
                $content = '' . get_the_post_thumbnail( $post -> ID, 'small-thumb' , array( 'style' => 'float:left; margin:0 15px 15px 0;' ) ) . '' . $content;
            }
            return $content;
        }
        
        static function gravatar( $authorID , $size, $default = '' ){
            return get_avatar( $authorID , $size , $default );
        }
        
        static function comment( $comment, $args, $depth ){
            $deff = get_template_directory_uri() . '/media/img/default-avatar.png';

            $GLOBALS['comment'] = $comment;
            switch ( $comment -> comment_type ) {
                case '' : {
                    echo '<li '; comment_class(); echo' id="li-comment-'; comment_ID(); echo '">';
                    echo '<div id="comment-'; comment_ID(); echo '" class="comment-box">';
                    echo '<header>';
                    echo myThemes::gravatar( $comment -> comment_author_email , 44  , $deff );
                    echo '<span class="comment-meta">';
                    echo '<time class="comment-time">';
                    printf( '%1$s ' , get_comment_date() );
                    echo '</time>';
                    echo ' / ';
                    comment_reply_link( array_merge( $args , array( 
                        'reply_text' => __( 'Reply', 'myThemes' ),
                        'before' => '<span class="comment-replay">',
                        'after' => '</span>',
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'] )
                    ) );
                    echo '</span>';
                    echo '<cite>';
                    echo get_comment_author_link( $comment -> comment_ID );
                    echo '</cite>';
                    echo '<div class="clear"></div>';
                    echo '</header>';

                    echo '<div class="comment-quote">';
                    if ( $comment -> comment_approved == '0' ) {
                        echo '<em class="comment-awaiting-moderation">';
                        _e( 'Your comment is awaiting moderation.' , 'myThemes' );
                        echo '</em>';
                    }
                    echo get_comment_text();            
                    echo '</div>';

                    echo '</div>';
                    echo '</li>';
                    break;
                }   
                default : {
                    echo '<li '; comment_class(); echo' id="li-comment-'; comment_ID(); echo '">';
                    echo '<div id="comment-'; comment_ID(); echo '" class="comment-box">';
                    echo '<header>';
                    echo '<span class="comment-meta">';
                    echo '<time class="comment-time">';
                    printf( '%1$s ' , get_comment_date() );
                    echo '</time>';
                    echo ' / ';
                    comment_reply_link( array_merge( $args , array( 
                        'reply_text' => __( 'Reply', 'myThemes' ),
                        'before' => '<span class="comment-replay">',
                        'after' => '</span>',
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'] )
                    ) );
                    echo '</span>';
                    echo '<cite>';
                    echo get_comment_author_link( $comment -> comment_ID );
                    echo '</cite>';
                    echo '<div class="clear"></div>';
                    echo '</header>';

                    echo '<div class="comment-quote">';
                    if ( $comment -> comment_approved == '0' ) {
                        echo '<em class="comment-awaiting-moderation">';
                        _e( 'Your comment is awaiting moderation.' , 'myThemes' );
                        echo '</em>';
                    }
                    echo get_comment_text();            
                    echo '</div>';

                    echo '</div>';
                    echo '</li>';
                    break;
                }
            }
        }
        
        /* RETURN NUMBER OFF CURRENT BLOG PAGE */
        static function pagination(){
            if( (int) get_query_var('paged') > 0 ){
                $paged = get_query_var('paged');
            }else{
                if( (int) get_query_var('page') > 0 ){
                    $paged = get_query_var('page');
                }else{
                    $paged = 1;
                }
            }
            
            return $paged;
        }
        
        static function favicon()
        {
            if( myThemes::get( 'favicon' ) ){
                echo '<link rel="shortcut icon" href="' . myThemes::get( 'favicon' ) . '"/>';
            }
            else{
                if( file_exists(  get_template_directory() . '/favicon.ico' ) )
                    echo '<link rel="shortcut icon" href="' . get_template_directory_uri() . '/favicon.ico"/>';
            }
        }

        static function title( $title, $sep ){
            global $paged, $page;

            if ( is_feed() )
                return $title;

            /*/ Add the site name. */
            $title .= get_bloginfo( 'name' );

            /*/ Add the site description for the home/front page. */
            $site_description = get_bloginfo( 'description', 'display' );
            if ( $site_description && ( is_home() || is_front_page() ) )
                $title = "$title $sep $site_description";

            /*/ Add a page number if necessary. */
            if ( $paged >= 2 || $page >= 2 )
                $title = "$title $sep " . sprintf( __( 'Page %s', 'myThemes' ), max( $paged, $page ) );

            return $title;
        }

        static function group()
        {
            return 'myThem.es';
        }
        
        static function name()
        {
            $theme = wp_get_theme();
            return $theme -> title;
        }
        
        static function version()
        {
            $theme = wp_get_theme();
            return $theme -> version;
        }
    }
?>