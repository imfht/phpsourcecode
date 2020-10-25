<?php
/*
Plugin Name: Full Breadcrumb
Plugin URI: https://github.com/pedroelsner/full-breadcrumb
Description: *** 一个支持自定义内容类型，分类法，文章，页面，分类，标签，作者存档的面包屑插件
Usage: 
Version: 1.2
Author: Pedro Elsner
Author URI: http://pedroelsner.com/
*/


/**
 * Full Breadcrumb
 * 
 * @since 1.0
 */
class FullBreadcrumb {

    /**
     * Default options
     * 
     * @var array
     * @access protected
     * @since 1.0
     */
    protected $_options = array(
        'type' => 'string',
        'labels' => array(
            'local'  => 'You are here:',
            'home'   => 'Home',
            'page'   => 'Page',
            'tag'    => 'Tag',
            'search' => 'Searching for',
            'author' => 'Published by',
            '404'    => 'Error 404 &rsaquo; Page not found'
        ),
        'separator' => array(
            'element' => 'span',
            'class'   => 'separator',
            'content' => '&rsaquo;'
        ),
        'local' => array(
            'element' => 'span',
            'class'   => 'local'
        ),
        'home' => array(
            'showLink'       => true,
            'showBreadcrumb' => false,
            'element'        => 'span',
            'class'          => 'home'
        ),
        'actual' => array(
            'element' => 'span',
            'class'   => 'actual'
        ),
        'quote' => array(
            'tag'    => true,
            'search' => true
        ),
        'page_ancestors' => array(
            'showLink' => false
        )
    );

    /**
     * Store elements HTML
     * 
     * @var array
     * @access protected
     * @since 1.0
     */
    protected $_elements = array();

    /**
     * Save breadcrumb created
     * 
     * @var string
     * @access private
     * @since 1.0
     */
    protected $_breadcrumb;

    /**
     * Save breadcrumb created
     * 
     * @var array
     * @access private
     * @since 1.1
     */
    protected $_array;

    /**
     * Save the called method (eg. show or get)
     * 
     * @var string
     * @access private
     * @since 1.1
     */
    protected $_method;

    /**
     * Construct
     * 
     * @param array $options Custom options
     * @access public
     * @since 1.0
     */
    public function __construct($options = array()) {
        $this->_options = array_merge($this->_options, $options);
        $this->_breadcrumb = '';

        if($this->_options['separator'] === false) {
            $this->_elements['separator'] = "";
        } else {
            $this->_elements['separator'] = sprintf('<%s class="%s">%s</%s>',
                                                    $this->_options['separator']['element'],
                                                    $this->_options['separator']['class'],
                                                    $this->_options['separator']['content'],
                                                    $this->_options['separator']['element']);
        }

        $this->_elements['local'] = sprintf('<%s class="%s">%s</%s> ',
                                                $this->_options['local']['element'],
                                                $this->_options['local']['class'],
                                                $this->_options['labels']['local'],
                                                $this->_options['local']['element']);

        $this->_elements['home_before'] = sprintf('<%s class="%s">',
                                                $this->_options['home']['element'],
                                                $this->_options['home']['class']);

        $this->_elements['home_after'] = sprintf('</%s>', $this->_options['home']['element']);

        if($this->_options['actual']) {
            $this->_elements['actual_before'] = sprintf('<%s class="%s">',
                                                            $this->_options['actual']['element'],
                                                            $this->_options['actual']['class']);
            $this->_elements['actual_after'] = sprintf('</%s>', $this->_options['actual']['element']);
        } else {
            $this->_elements['actual_before'] = "";
            $this->_elements['actual_after'] = "";
        }


        if ($this->_options['separator']['element'] == 'span') {
            $this->_elements['separator'] = sprintf(' %s ', $this->_elements['separator']);
        }

        if ($this->_options['local']['element'] == 'span') {
            $this->_elements['local'] = sprintf('%s ', $this->_elements['local']);
        }

    }

    /**
     * Make breadcrumb
     * 
     * @return string
     * @access public
     * @since 1.0
     */
    public function getBreadcrumb($method) {
        global $post;

        $this->_method = $method;

        if (is_home() && is_front_page()) {
            if ($this->_options['home']['showBreadcrumb'] == false) {
                return '';
            }
        }
        
        if($this->_method == 'get' && $this->_options['type'] == 'string') {
            $this->setBreadcrumb('<div id="breadcrumb">');
        }

        $this->_local();
        $this->_home();

        if (is_category()) {
            $this->_category();
        } elseif (is_day()) {
            $this->_day();
        } elseif (is_year()) {
            $this->_year();
        } elseif (is_single() && !is_attachment()) {
            $this->_post();
        } elseif (is_attachment()) {
            $this->_attachment();
        } elseif (is_page()) {
            $this->_page();
        } elseif (is_search()) {
            $this->_search();
        } elseif (is_tag()) {
            $this->_tag();
        } elseif (is_author()) {
            $this->_author();
        } elseif (is_404()) {
            $this->_404();
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            if (is_tax()) {
                $this->_archiveCustomPostType();
            } else {
                $this->_archive();
            }
        }

        if (get_query_var('paged')) {
            $this->setBreadcrumb(
                array(
                    $this->_elements['separator'],
                    $this->_options['labels']['page'],
                    ' ' . get_query_var('paged'),
                )
            );
        }

        if($this->_method == 'get' && $this->_options['type'] == 'string') {
            $this->setBreadcrumb('</div>');
        }

        if($this->_method == 'get' && $this->_options['type'] == 'array') {
            return $this->_array;
        } else {
            return $this->_breadcrumb;
        }
        
    }
    
    /**
     * Define breadcrump
     * 
     * @param boolean|string $local
     * @access public
     * @since 1.0
     */
    public function setBreadcrumb($local) {
        if($local) {
            if (is_array($local)) {
                if($this->_method == 'get' && $this->_options['type'] == 'array') {
                    $this->_array[] = implode('', $local);
                } else {
                    foreach ($local as $value) {
                        $this->_breadcrumb .= $value;
                    }
                }
            } else {
                $this->_breadcrumb .= $local;
                $this->_array[] = $local;
            }
        }
    }

    /**
     * Local
     * 
     * @access protected
     * @since 1.0
     */
    protected function _local() {
        if ($this->_options['labels']['local']) {
            $this->setBreadcrumb($this->_elements['local']);
        }
    }

    /**
     * Home
     * 
     * @access protected
     * @since 1.0
     */
    protected function _home() {
        if ($this->_options['home']['showLink'] == false) {
            $this->setBreadcrumb(
                array(
                    $this->_options['labels']['home'],
                    $this->_elements['separator'],
                )
            );
        } else {
            $this->setBreadcrumb(
                array(
                    '<a href="' . home_url() . '">',
                    $this->_options['labels']['home'],
                    '</a>',
                    $this->_elements['separator'],
                )
            );
        }
    }
    
    /**
     * Category
     * 
     * @access protected
     * @since 1.0
     */
    protected function _category() {
        global $wp_query;

        $obj            = $wp_query->get_queried_object();        
        $category       = get_category($obj->term_id);
        $parentCategory = get_category($category->parent);
            
        if ($category->parent != 0) {
            if($this->_method == 'get' && $this->_options['type'] == 'array') {
                foreach(explode('|', get_category_parents($parentCategory, true, '|')) as $parent) {
                    $this->setBreadcrumb($parent);
                }
            } else {
                $this->setBreadcrumb(get_category_parents($parentCategory, true, $this->_elements['separator']));
            }
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                single_cat_title('', false),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Day
     * 
     * @access protected
     * @since 1.0
     */
    protected function _day() {
        $this->setBreadcrumb(
            array(
                get_the_time('Y'),
                $this->_elements['separator'],
                get_the_time('F'),
                $this->_elements['separator'],
                $this->_elements['actual_before'],
                get_the_time('d'),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Month
     * 
     * @access protected
     * @since 1.0
     */
    protected function _month() {
        $this->setBreadcrumb(
            array(
                get_the_time('Y'),
                $this->_elements['separator'],
                $this->_elements['actual_before'],
                get_the_time('F'),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Year
     * 
     * @access protected
     * @since 1.0
     */
    protected function _year() {
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                get_the_time('Y'),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Post
     * 
     * @access protected
     * @since 1.0
     */
    protected function _post() {
        global $post;

        if (get_post_type() != 'post' ){
                $post_type = get_post_type_object(get_post_type());
                if(get_post_type_archive_link($post_type->name)) {
                    $this->setBreadcrumb(
                        array(
                            '<a href="' . get_post_type_archive_link($post_type->name) . '" title="' . $post_type->labels->menu_name . '">',
                            $post_type->labels->menu_name,
                            '</a>',
                        )
                    );
                } else {
                    $this->setBreadcrumb($post_type->labels->menu_name);
                }
                $this->setBreadcrumb($this->_elements['separator']);
        }

        $taxonomies = get_post_taxonomies($post->ID);
        if (count($taxonomies) > 0) {
            foreach($taxonomies as $taxonomy) {
                if(is_taxonomy_hierarchical($taxonomy)) {
                    foreach (wp_get_object_terms($post->ID, $taxonomy) as $term) {
                        if($term->taxonomy == 'category') {
                            foreach(explode('|', get_category_parents($term->term_id, false, '|', true)) as $ancestor) {
                                if(trim($ancestor) && $ancestor != $term->slug) {
                                    $ancestor = get_category_by_slug($ancestor);
                                    $this->setBreadcrumb('<a href="' . get_term_link($ancestor->slug, $taxonomy) . '" title="' . $ancestor->name . '">' . $ancestor->name . '</a> ');
                                }
                            }
                        }
                        $this->setBreadcrumb('<a href="' . get_term_link($term->slug, $taxonomy) . '" title="' . $term->name . '">' . $term->name . '</a> ');
                    }
                    $this->setBreadcrumb($this->_elements['separator']);
                }
            }
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                get_the_title(),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Archive for Custom Post Type
     * 
     * @access protected
     * @since 1.0
     */
    protected function _archiveCustomPostType() {
        $post_type = get_post_type_object(get_post_type());
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $taxonomy = get_taxonomy($term->taxonomy);
        if(get_post_type_archive_link($post_type->name)) {
            $this->setBreadcrumb('<a href="' . get_post_type_archive_link($post_type->name) . '" title="' . $post_type->labels->menu_name . '">' . $post_type->labels->menu_name . '</a>');
        } else {
            $this->setBreadcrumb($post_type->labels->menu_name);
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['separator'],
                $taxonomy->label,
                $this->_elements['separator'],
                $this->_elements['actual_before'],
                $term->name,
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Archive
     * 
     * @access protected
     * @since 1.0
     */
    protected function _archive() {
        $post_type = get_post_type_object(get_post_type());
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                $post_type->labels->menu_name,
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Attachment
     * 
     * @access protected
     * @since 1.0
     */
    protected function _attachment() {
        global $post;

        $parent = get_post($post->post_parent);
        $categories = get_the_category($parent->ID);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $this->setBreadcrumb(get_category_parents($category, TRUE, $this->_elements['separator']));
            }
            $this->setBreadcrumb($this->_elements['separator']);
        }
        
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                get_the_title(),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Page
     * 
     * @access protected
     * @since 1.0
     */
    protected function _page() {
        global $post;

        $taxonomies = get_post_taxonomies($post->ID);
        if (count($taxonomies) > 0) {
            foreach($taxonomies as $taxonomy) {
                if(is_taxonomy_hierarchical($taxonomy)) {
                    foreach (wp_get_object_terms($post->ID, $taxonomy) as $term) {
                        $this->setBreadcrumb('<a href="' . get_term_link($term->slug, $taxonomy) . '" title="' . $term->name . '">' . $term->name . '</a> ');
                    }
                    $this->setBreadcrumb($this->_elements['separator']);
                }
            }
        }

        if (!$post->post_parent) {
            $this->setBreadcrumb(
                array(
                    $this->_elements['actual_before'],
                    get_the_title(),
                    $this->_elements['actual_after'],
                )
            );
            return;
        }

        $parent_id = $post->post_parent;
        $pages = array();
        while ($parent_id) {
            $page = get_page($parent_id);
            if($this->_options['page_ancestors']['showLink']) {
                $page_name = get_the_title($page->ID);
                $pages[] = '<a title="' . $page_name . '" href="' . get_permalink($page->ID) . '">' . $page_name . '</a>';
            } else {
                $pages[] = '' . get_the_title($page->ID) . '';
            }
            $parent_id = $page->post_parent;
        }
        $pages = array_reverse($pages);
        foreach ($pages as $page) {
            $this->setBreadcrumb(
                array(
                    $page,
                    $this->_elements['separator'],
                )
            );
        }

        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                get_the_title(),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Search
     * 
     * @access protected
     * @since 1.0
     */
    protected function _search() {
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                $this->_options['labels']['search'],
                $this->_options['quote']['search'] ? ' &lsquo;' . get_search_query() . '&rsquo;' : get_search_query(),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Tag
     * 
     * @access protected
     * @since 1.0
     */
    protected function _tag() {
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                $this->_options['labels']['tag'],
                $this->_options['quote']['tag'] ? ' &lsquo;' . single_tag_title('', false) . '&rsquo;' : single_tag_title('', false),
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * Author
     * 
     * @access protected
     * @since 1.0
     */
    protected function _author() {
        global $author;

        $userdata = get_userdata($author);
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                $this->_options['labels']['author'],
                ' ' . $userdata->display_name,
                $this->_elements['actual_after'],
            )
        );
    }

    /**
     * 404
     * 
     * @access protected
     * @since 1.0
     */
    protected function _404() {
        $this->setBreadcrumb(
            array(
                $this->_elements['actual_before'],
                $this->_options['labels']['404'],
                $this->_elements['actual_after'],
            )
        );
    }

}

/**
 * Show Breadcrumb
 * 
 * @param array $settings
 * @access public
 * @since 1.0
 */
function show_full_breadcrumb($settings = array()) {
    $breadcrumb = new FullBreadcrumb($settings);
    echo $breadcrumb->getBreadcrumb('show');
}

/**
 * Return Breadcrumb
 * 
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
function get_full_breadcrumb($settings = array()) {
    $breadcrumb = new FullBreadcrumb($settings);
    return $breadcrumb->getBreadcrumb('get');
}
