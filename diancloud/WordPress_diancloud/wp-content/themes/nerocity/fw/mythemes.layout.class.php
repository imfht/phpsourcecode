<?php
/*
    Version : 0.0.2
 */

class mythemes_layout {
    public $layout = '';
    public $sidebars = '';
    public $sidebarsNr = '';
    public $contentClass = '';
    public $itemid = '';
    public $template = '';
    public $containerClass = '';
    public $width;
	
    function _get( $setting, $template, $itemId ) {
        
        /* ONLYR FOR CATEGORY TEMPLATE */
        $rett = '';
        $id = $itemId;
        $temp = $template;
			
        switch( $template ){
	
            case 'front-page':
            case 'single':
            case 'portfolio-single':
            case 'page':
            case 'portfolio':
                $rett = meta::get( $itemId , 'post-' . $setting );
                
                if( $rett ) break;
                $rett = myThemes::get( $template . '-' . $setting  );
                
                if( $rett ) break;
                $rett = myThemes::get( $setting  );
                break;
            default: {		
                $rett = myThemes::get( $setting  );
                break;
            }
        }
        return $rett;
    }

    function mythemes_layout( $template = '', $itemId = 0 )
    {   
        $this -> itemid = $itemId;
        $this -> template = $template;
        
        $this -> layout = $this -> _get( 'layout' , $template, $itemId );
        
        if( $this -> layout == 'left' || $this -> layout == 'right' ){
            $layout = 'left';
            if( $this -> layout == 'left' )
                $layout = 'right';
            
            $this -> contentClass = 'col-sm-8 col-md-9 col-lg-9';
            $this -> width = 797;
            return;
        }
        
        $this -> contentClass = 'col-lg-12';
        $this -> width = 1140;
    }
	
    function echoSidebar( $position )
    {
        $sidebar = $this -> _get( 'sidebar', $this -> template, $this -> itemid );

        if( $this -> layout == $position ){
            echo '<aside class="col-sm-4 col-md-3 col-lg-3 sidebar-to-' . $position . '">';
            if( dynamic_sidebar( $sidebar ) ){

            }
            else{
                if( myThemes::get( 'default-content' ) ){
                    if( is_singular( 'post' ) ){
                        global $post;
                        /* FOR SINGLE */
                        /* META DETAILS */
                        echo '<div class="widget widget_post_meta">';
                        $name = get_the_author_meta( 'display_name' , $post -> post_author );
        
                        echo '<div>';
                        echo '<ul>';
                        edit_post_link( '<i class="icon-pencil"></i>' . __( 'Edit' , 'myThemes' ) , '<li>', '</li>' );
                        echo '<li><a href="' . get_day_link( get_post_time( 'Y', false , $post -> ID ) , get_post_time( 'm' , false , $post -> ID ) , get_post_time( 'd' , false , $post -> ID ) ) . '">';
                        echo '<time datetime="' . get_post_time( 'Y-m-d', false , $post -> ID  ) . '"><i class="icon-calendar"></i>' . get_post_time( get_option( 'date_format' ), false , $post -> ID  ) . '</time></a></li>';
                        echo '<li><a href="' . get_author_posts_url( $post-> post_author ) . '" title="' . __( 'Writed by ' , 'myThemes' ) . ' ' . $name . '"><i class="icon-user-5"></i>' . $name . '</a></li>';
                        
                        if( $post -> comment_status == 'open' ) {
                            $nr = get_comments_number( $post -> ID );
                            if( $nr == 1){
                                $comments = $nr . ' ' . __( 'Comment' , 'myThemes' );
                            }
                            else{
                                $comments = $nr . ' ' . __( 'Comments' , 'myThemes' );
                            }
                            echo '<li><a href="' . get_comments_link( $post -> ID ). '"><i class="icon-comment"></i>' . $comments . '</a></li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        echo '</div>';

                        /* NEWSLETTER */
                        echo '<div class="widget widget_newsletter">';
                        echo '<h4 class="widget-title"><i></i>' . __( 'Newsletter' , 'myThemes' ) . '</h4>';
                        echo '<span class="description">' . __( 'subscribe with FeedBurner' , 'myThemes' ) . '</span>';
                        echo '<form class="subscribe" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="javascript:utils.feedburner( \'mythem_es\' );">';
                        echo '<p>';
                        echo '<input type="text" class="text" name="email" value="E-mail" onfocus="if (this.value == \'E-mail\') {this.value = \'\';}" onblur="if (this.value == \'\' ) { this.value = \'E-mail\';}"><span class="email"></span>';
                        echo '<input type="hidden" value="mythem_es" name="uri">';
                        echo '<input type="hidden" name="loc" value="en_US">';
                        echo '<button type="submit" value=""><i class="icon-right-open-big"></i></button>';
                        echo '</p>';
                        echo '</form>';
                        echo '</div>';

                        /* ARTICLE CATEGORIES */
                        if( has_category() ){
                            echo '<div class="widget widget_post_categories">';
                            echo '<h4 class="widget-title"><i></i>' . __( 'Article Categories' , 'myThemes' ) . '</h4>';
                            echo '<div>';
                            echo '<ul>';
                            echo '<li>';
                            the_category( '</li><li>' );
                            echo '</li>';
                            echo '</ul>';
                            echo '</div>';
                            echo '</div>';
                        }

                        /* ARTICLE TAGS */
                        if( has_tag() ){
                            echo '<div class="widget widget_post_tags">';
                            echo '<h4 class="widget-title"><i></i>' . __( 'Article Tags' , 'myThemes' ) . '</h4>';
                            echo '<div class="tagcloud">';

                            $tags = wp_get_post_tags( $post -> ID );

                            foreach( $tags as $t => $tag ){
                                echo '<a href="' . get_tag_link( $tag -> term_id ) . '" title="' . $tag -> count . ' topic">';
                                echo $tag -> name;
                                echo '</a>';
                            }
                
                            echo '<div class="clear"></div>';
                            echo '</div>';
                            echo '</div>';
                        }

                    }
                    else{
                        /* FOR OTHERS CASE */
                        /* SEARCH */
                        echo '<div class="widget widget_search">';
                        get_template_part( 'searchform' );
                        echo '</div>';

                        /* TAGS */
                        $tags = get_tags();

                        if( !empty( $tags ) ){

                            echo '<div id="tag_cloud" class="widget widget_tag_cloud">';
                            echo '<h4 class="widget-title"><i></i>' . __( 'Tags' , 'myThemes' ) . '</h4>';
                            echo '<div class="tagcloud">';

                            foreach ( $tags as $tag ) {
                                echo '<a href="' . get_tag_link( $tag -> term_id ) . '" title="' . $tag -> count . ' ' . __( 'Topic' , 'myThemes' ) . '" class="tag-link-' . $tag -> term_id . '">';
                                echo $tag -> name . '</a>';
                            }

                            echo '</div>';
                            echo '</div>';
                        }

                        /* CATEGORIES */
                        $categories = get_categories( );

                        if( !empty( $categories ) ){

                            echo '<div id="categories" class="widget widget_categories">';
                            echo '<h4 class="widget-title"><i></i>' . __( 'Categories' , 'myThemes' ) . '</h4>';
                            echo '<ul>';
                            foreach( $categories as $c ){
                                echo '<li class="cat-item cat-item-' . $c -> term_id . '">';
                                echo '<a href="' . get_category_link( $c -> term_id ) . '" title="' . __( 'View all posts filed under' , 'myThemes' ) . ' ' . $c -> name . '">' . $c -> name . '</a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                    }
                }
            }
            echo '<div class="clearfix"></div>';
            echo '</aside>';

            return;
        }
    }

    function contentWrapper( $position ){

        $rett = '';

        if( !empty( $this -> layout ) && $position == 'right' ){
            $rett = '</div>';
        }

        if( !empty( $this -> layout ) && $position == 'left' ){
            $rett = '<div class="content-border ' . $this -> layout . '">';
        }

        return $rett;
    }
	
    function contentClass( ) {
        return $this -> contentClass;
    }
    
    function countSidebars( $layout )
    {
        return count( explode( '+', $layout ) ) - 2;
    }
}
?>