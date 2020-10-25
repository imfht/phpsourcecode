<?php
/*
Plugin Name: BeautyMark
Plugin URI: http://www.tecmz.com
Description: Beauty markdown document easily !
Version: 1.0.0
Author: edwin404
Author URI: http://www.eamonning.com
License: GPL
*/


if (!defined('BEAUTYMARK_INCLUDE')) {
    define('BEAUTYMARK_INCLUDE', true);

    class BeautyMark
    {
        private $version = '1.0.0';

        private $cnt = 0;

        const IS_MD = '_beautymark_status';

        protected $new_api_post = false;

        function __construct()
        {
            load_plugin_textdomain('beautymark-osi', NULL, basename(dirname(__FILE__)));

            add_post_type_support('post', 'beautymark-osi');
            add_post_type_support('page', 'beautymark-osi');

            add_action('template_redirect', array($this, 'template_redirect'));
            add_action('do_meta_boxes', array($this, 'do_meta_boxes'), 20, 2);

            add_filter('wp_insert_post_data', array($this, 'wp_insert_post_data'), 10, 2);

            add_action('xmlrpc_call', array($this, 'xmlrpc_call'));
            add_action('xmlrpc_call_success_mw_newPost', array($this, 'xmlrpc_call_success_mw_newPost'), 10, 2);
        }

        public function xmlrpc_call($xmlrpc_method)
        {
            // $make_filterable = array('metaWeblog.getRecentPosts', 'wp.getPosts', 'wp.getPages');
            // do nothing
        }

        public function xmlrpc_call_success_mw_newPost($post_id, $args)
        {
            $this->new_api_post = true;
            remove_filter('wp_insert_post_data', array($this, 'wp_insert_post_data'), 10, 2);
            $post = (array)get_post($post_id);
            $post = $this->wp_insert_post_data($post, $post);

            wp_update_post($post);
        }

        public function wp_insert_post_data($data, $postarr)
        {
            // run once
            remove_filter('wp_insert_post_data', array($this, 'wp_insert_post_data'), 10, 2);

            // checks
            $nonced = (isset($_POST['_beautymark_enable_nonce']) && wp_verify_nonce($_POST['_beautymark_enable_nonce'], 'beautymark-save'));
            $enable_ticked = ($nonced && isset($_POST['beautymark_enable_markdown']));
            $id = (isset($postarr['ID'])) ? $postarr['ID'] : 0;
            $post_type_to_check = isset($postarr['post_type']) ? $postarr['post_type'] : '';
            if ('revision' === $post_type_to_check) {
                $parent = get_post($data['post_parent']);
                $post_type_to_check = $parent->post_type;
            }
            $supports = post_type_supports($post_type_to_check, 'beautymark-osi');

            // double check in case this is a new xml-rpc post. Disable couldn't be checked.
            if ($this->new_api_post) {
                $enable_ticked = false;
            }

            if ($id) {
                if ($supports && $enable_ticked) {
                    //exit('yes');
                    update_post_meta($id, self::IS_MD, 1);
                } else {
                    //exit('no');
                    update_post_meta($id, self::IS_MD, 0);
                }
            }

            return $data;

        }

        public function do_meta_boxes($type, $context)
        {
            // allow disabling for folks who think markdown should always be on.
            //if (defined('SD_HIDE_MARKDOWN_BOX') && SD_HIDE_MARKDOWN_BOX)
            //    return;
            //print_r(get_post_types());exit();
            if ('side' == $context && in_array($type, array_keys(get_post_types())) && post_type_supports($type, 'beautymark-osi')) {
                add_meta_box('sd-markdown', __('BeautyMark', 'beautymark-osi'), array($this, 'meta_box'), $type, 'side', 'high');
            }
        }

        public function meta_box()
        {
            global $post;
            $screen = get_current_screen();
            wp_nonce_field('beautymark-save', '_beautymark_enable_nonce', false, true);
            echo '<p><input type="checkbox" name="beautymark_enable_markdown" id="beautymark_enable_markdown" value="1" ';
            if ('add' !== $screen->action) {
                checked(get_post_meta($post->ID, self::IS_MD, true));
            }
            echo ' /> <label for="beautymark_enable_markdown">' . __('Markdown enable', 'beautymark-osi') . '</label></p>';
        }

        public function the_content($content)
        {
            $post = get_post();
            if (!empty($post) && get_post_meta($post->ID, self::IS_MD, true)) {

                remove_filter('the_content', 'wpautop');
                remove_filter('the_content', 'wptexturize');

                $this->cnt++;
                return
                    '<div class="markdown_container">'
                    . '    <div class="markdown_sidebar">'
                    . '        <h1>目录</h1>'
                    . '        <div class="markdown-body editormd-preview-container custom_toc_container" id="custom_toc_container_' . $this->cnt . '"></div>'
                    . '    </div>'
                    . '    <div class="markdown_view" id="markdown_view_' . $this->cnt . '">'
                    . '        <textarea style="display:none;">' . ($content) . '</textarea>'
                    . '    </div>'
                    . '</div>';
            } else {
                return $content;
            }
        }

        public function template_redirect()
        {
            if (true || is_single()) {
                $post = get_post();
                if (!empty($post) && get_post_meta($post->ID, self::IS_MD, true)) {


                    add_filter('the_content', array(&$this, 'the_content'), 9);

                    $base = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__));

                    // 添加样式文件
                    wp_enqueue_style('beautymark.preview', $base . '/mdeditor/css/editormd.preview.css', array(), $this->version);
                    wp_enqueue_style('beautymark.style', $base . '/res/style.css', array(), $this->version);

                    // 添加脚本文件
                    wp_register_script('beautymark.jquery', $base . '/res/jquery-1.11.3.js', array(), $this->version, true);
                    wp_enqueue_script('beautymark.jquery');

                    wp_register_script('beautymark.marked', $base . '/mdeditor/lib/marked.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.marked');

                    wp_register_script('beautymark.prettify', $base . '/mdeditor/lib/prettify.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.prettify');

                    wp_register_script('beautymark.raphael', $base . '/mdeditor/lib/raphael.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.raphael');

                    wp_register_script('beautymark.underscore', $base . '/mdeditor/lib/underscore.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.underscore');

                    wp_register_script('beautymark.sequence-diagram', $base . '/mdeditor/lib/sequence-diagram.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.sequence-diagram');

                    wp_register_script('beautymark.flowchart', $base . '/mdeditor/lib/flowchart.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.flowchart');

                    wp_register_script('beautymark.jquery.flowchart', $base . '/mdeditor/lib/jquery.flowchart.min.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.jquery.flowchart');

                    wp_register_script('beautymark.editormd', $base . '/mdeditor/editormd.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.editormd');

                    wp_register_script('beautymark.script', $base . '/res/script.js', array('jquery'), $this->version, true);
                    wp_enqueue_script('beautymark.script');

                }
            }

        }

    }

    new BeautyMark();
}