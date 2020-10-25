<?php
class mytheme_admin {

    static function pageHeader( $pageSlug )
    {
        echo '<div class="mytheme-admin-header">';
        echo '<span class="theme"><strong>' . myThemes::name() . '</strong> ' . __( 'Version' , 'myThemes' ) . ': ' . myThemes::version() . '</span>';
        echo '<a href="http://mythem.es" target="_blank" title="Affordable WordPress Themes For Your Website or Blog"><img src="' . MYTHEMES_DEV_LOGO . '" /></a>';
        echo '<p><a href="http://mythem.es" target="_blank" title="Affordable WordPress Themes For Your Website or Blog">Affordable WordPress Themes For Your Website or Blog</a></p>';
        echo '</div>';

        echo '<table class="admin-body">';
        echo '<tr>';
    }

    static function pageContent( $pageSlug )
    {
        $cfgs = & myThemes_acfg::$pages[ $pageSlug ];
        
        $file = str_replace( 'mythemes-' , '' , $pageSlug );
            
        $sett_    = get_template_directory() . '/cfg/admin/settings';
        $sett_dir = $sett_ . '/' . $file;
        
        if( file_exists( $sett_dir . '.php' ) ){
            include $sett_dir . '.php';
        }
        
        $st = $cfgs[ 'content' ];
        
        if( !empty( $_POST ) ){
            foreach( $_POST as $key => & $d ){
                if( substr( $key , 0 , 9 ) == 'mythemes-' ){
                    $fName = str_replace( 'mythemes-' , '' ,  $key );
                    
                    /* VALIDATE INFO BEFORE SAVE */
                    $validator = '';
                    if( isset( $_POST[ $key ] ) && isset( $st[ $fName ] ) )
                        $validator = ahtml::validator( $_POST[ $key ] , ahtml::getValidator( $st[ $fName ] ) );
                    
                    set_theme_mod( $key , $validator );
                }
            }
        }
        
        $rett = '<td class="admin-content">';
            
        {   /* PAGE TITLE */
            
            $rett .= '<div class="title">';
                
            if( isset( $cfgs[ 'title' ] ) ) {
                $rett .= '<h2>' . $cfgs[ 'title' ] . '</h2>';
            }

            if( isset( $cfgs[ 'description' ] ) ){
                $rett .= '<p>' . $cfgs[ 'description' ] . '</p>';
            }

            $rett .= '</div>';
        }
            
        /* SUBMIT FORM */
        if( !isset( $cfgs[ 'update' ] ) || ( isset( $cfgs[ 'update' ] ) && $cfgs['update'] ) ){
            $rett .= '<form method="post">';
        }
            
        settings_fields( 'mythemes' );
        $content = $cfgs[ 'content' ];
            
        if( isset( $content ) && !empty( $content ) ) {
            foreach( $content  as $fieldName => $sett ) {
                $sett[ 'pageSlug' ]     = $pageSlug;
                $sett[ 'fieldName' ]    = $fieldName;
                $sett[ 'value' ]        = sett::get( 'mythemes-' . $fieldName );
                $rett .= ahtml::template( $sett );
            }
        }
            
        {   /* SUBMIT BUTTON */
            if( !isset( $cfgs[ 'update' ] ) || ( isset( $cfgs[ 'update' ] ) && $cfgs['update'] ) ){
                $rett .= '<div class="standart-generic-field submit top_delimiter">';
                $rett .= '<div class="field">';
                $rett .= '<input type="submit" class="button button-primary my-submit button-hero" value="' . __( 'Update Settings' , "myThemes" ) . '"/>';
                $rett .= '</div>';
                $rett .= '</div>';
                $rett .= '</form>';
            }
        }
            
        $rett .= '</td>';
        $rett .= '<td class="mythemes-credits">';
        $rett .= '<div class="title">';
        $rett .= '<h3>myThem.es Links</h3>';
        $rett .= '</div>';
        $rett .= include get_template_directory() . '/fw/admin/widget.php';
        $rett .= '</td>';
        $rett .= '</tr>';
        $rett .= '</table>';
        
        return $rett;
    }
    
    static function echoPage( )
    {

        if( !isset( $_GET ) || !isset( $_GET[ 'page' ] ) ){
            wp_die( 'Invalid page name', 'myThemes' );
            return;
        }

        $pageSlug = $_GET[ 'page' ];

        /* NOTIFICATION */
        if( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] == 'true' ){
            echo '<div class="updated settings-error myTheme" id="setting-error-settings_updated">';
            echo '<p>' . __( 'options has been updated successfully' , 'myThemes' ) . '</p>';
            echo '</div>';
        }

        echo '<div class="admin-page">';
        echo self::pageHeader( $pageSlug );
        echo self::pageContent( $pageSlug );
        echo '</div>';
    }
    
    static function init_mainMenu( ) 
    {
        $parent = '';
        $pageCB = array( 'mytheme_admin', 'echoPage' );
        foreach( myThemes_acfg::$pages as $pageSlug => $d ) {   
            if( isset( $d[ 'menu' ] ) ) {
                $m = $d[ 'menu' ];
                if( strlen( $parent ) == 0 ) {
                    add_theme_page(
                        $m[ 'settings' ]                                            /* page_title   */
                        , $m[ 'settings' ]                                          /* menu_title   */
                        , 'administrator'                                           /* capability   */
                        , $pageSlug                                                 /* menu_slug    */
                        , $pageCB                                                   /* function     */                                       /* icon_url     */
                    );
                    $parent = $pageSlug;
                }
                else {
                    add_theme_page(
                        "mythemes" . "&nbsp;&raquo;&nbsp;" . $m[ 'label' ]          /* page_title   */
                        , " &raquo; " . $m[ 'label' ]                               /* menu_title   */
                        , 'administrator'                                           /* capability   */
                        , $pageSlug                                                 /* menu_slug    */
                        , $pageCB                                                   /* function     */
                    );
                }
            }
        }
    }

    static function save( $exclude = array() )
    {
        if( !isset( $_POST ) || empty( $_POST ) )
            return null;

        foreach( $_POST as $sett => $value )
            if( substr( $sett , 0 , 8 ) == 'mytheme-' && !in_array( $sett , $exclude ) )
                sett::set( $sett , $value );
    }

    static function load_css(){
        if( is_admin() ){
            
            if( isset( $_GET[ 'page' ] ) )
                wp_enqueue_media();

            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_style( 'ui-lightness' );
            wp_enqueue_style( 'thickbox' );
            
            wp_register_style( 'admin' ,  get_template_directory_uri() . '/media/admin/css/admin.css' );
            wp_register_style( 'widget' ,  get_template_directory_uri() . '/media/admin/css/widget.css' );
            wp_register_style( 'ahtml' ,  get_template_directory_uri() . '/media/admin/css/ahtml.css' );

            wp_register_style( 'template' ,  get_template_directory_uri() . '/media/admin/css/template.css' );

            wp_enqueue_style( 'admin' );
            wp_enqueue_style( 'widget' );
            wp_enqueue_style( 'ahtml' );
            wp_enqueue_style( 'template' );
        }
    }

    static function load_js(){
        if( is_admin( ) ){
            wp_register_script( 'mythemes-fields' ,  get_template_directory_uri() . '/media/admin/js/fields.js' , array( 'jquery' , 'media-upload' , 'thickbox' , 'farbtastic' )  );
            wp_register_script( 'mythemes-ahtml' ,  get_template_directory_uri() . '/media/admin/js/ahtml.js' );
            wp_register_script( 'mythemes-tools' ,  get_template_directory_uri() . '/media/admin/js/tools.js' );
            wp_register_script( 'mythemes-ahtml' ,  get_template_directory_uri() . '/media/admin/js/ahtml.js' );

            wp_enqueue_script( 'mythemes-fields' );
            wp_enqueue_script( 'mythemes-tools' );
            wp_enqueue_script( 'mythemes-ahtml' );
        }
    }
};

add_action( 'admin_menu' , array( 'mytheme_admin', 'init_mainMenu' ) );
add_action( 'admin_print_scripts', array( 'mytheme_admin' , 'load_js' ) );
add_action( 'admin_print_scripts', array( 'mytheme_admin' , 'load_css' ) );
?>