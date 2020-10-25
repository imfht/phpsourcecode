<?php
(defined('BASEPATH')) or exit('No direct script access allowed');

/* load the HMVC_Router class */

class MY_Router extends CI_Router {
    /**
     * Current module name
     *
     * @var string
     * @access public
     */
    var $module = '';
    /**
     * 默认控制器
     *
     * @var string
     * @access public
     */
    var $default_controller =  'Welcome';

    function __construct() {
        parent::__construct();
    }
    /*
     * 使用默认

    function _set_request($segments){
        parent::_set_request($segments);
    }
*/
    /*
     * 分析并构造路由
     */
    function _parse_routes() {
        // Apply the current module's routing config
        if ($module = $this->uri->segment(0)) {
            foreach ($this->config->item('modules_locations') as $location) {
                if (is_file($file = $location . $module . '/config/routes.php')) {
                    include ($file);

                    $route = (!isset($route) or !is_array($route)) ? array() : $route;
                    $this->routes = array_merge($this->routes, $route);
                    unset($route);
                }
            }
        }

        //使用默认
        return parent::_parse_routes();
    }

    /**
     * 检测路径中是否包含需要的控制器文件
     *
     * @access	private
     * @param	array
     * @return	array
     */
    function _validate_request($segments) {

        if (count($segments) == 0) {
            return $segments;
        }
        // Locate the controller with modules support
        if ($located = $this->locate($segments)) {
            return $located;
        }

        // Is there a 404 override?
        if (!empty($this->routes['404_override'])) {
            $segments = explode('/', $this->routes['404_override']);
            if ($located = $this->locate($segments)) {
                return $located;
            }
        }
        // Nothing else to do at this point but show a 404
        show_404($segments[0]);
    }



    /**
     * 寻找controller路径
     *
     * @param	array
     * @return	array
     */
    function locate($segments) {

        list($module, $directory, $controller) = array_pad($segments, 3, NULL);

        foreach ($this->config->item('modules_locations') as $location) {
            $relative = APPPATH.$location;
            //如果 包含有 application/$module/controllers文件夹
            if (is_dir($source = $relative . $module . '/controllers/')) {
                $this->module = $module;
                $this->directory =  '../'.$location.$module . '/controllers/';


                // 如果 有 application/$module/controllers/$directory.php 文件
                if ($directory && is_file($source . ucfirst($directory) . '.php')) {
                    return array_slice($segments, 1);
                }

                //如果application/$module/$directory 是一个文件夹
                if ($directory && is_dir($source . $directory . '/')) {
                    $source = $source . $directory . '/';
                    $this->directory .= $directory . '/';
                    //  index.php/$modules/$directory/$controller
                    //如果包含 控制器  $controller
                    if ($controller && is_file($source . ucfirst($controller) . '.php')) {
                        return array_slice($segments, 2);
                    }

                    //如果有默认控制器
                    if (is_file($source . $this->default_controller . '.php')) {

                        $segments[1] = $this->default_controller;
                        return array_slice($segments, 1);
                    }

                    //如果有 application/$module/$directory.php
                    if (is_file($source . $directory . '.php')) {
                        return array_slice($segments, 1);
                    }
                }

                //如果有 application/$module/$module.php
                if (is_file($source . $module . '.php')) {
                    return $segments;
                }

                // 默认控制器

                //if (is_file($source . $this->default_controller . '.php')) {
                if(is_file(APPPATH.$location.$module.'/'.'controllers/'.ucfirst($this->default_controller).'.php')){
                    $segments[0] = $this->default_controller;
                    return $segments;
                }
            }
        }

        // Root folder controller?
        if (is_file(APPPATH . 'controllers/' . $module . '.php') or is_file(APPPATH . 'controllers/' . ucfirst($module) . '.php') ) {
            return array_slice($segments, 0);
        }
        // Sub-directory controller?
        if ($directory && is_file(APPPATH . 'controllers/' . $module . '/' . $directory . '.php') or
            is_file(APPPATH . 'controllers/' . $module .'/' .ucfirst($directory).'.php')) {
            $this->directory = $module . '/';
            return array_slice($segments, 1);
        }

        // Default controller?
        if (is_file(APPPATH . 'controllers/' . $module . '/' . $this->default_controller . '.php')) {
            $segments[0] = $this->default_controller;
            return $segments;
        }
    }

    /**
     * Set the module name
     *
     * @param	string
     * @return	void
     */
    function set_module($module) {
        $this->module = $module;
    }

    // --------------------------------------------------------------------


    /**
     * Fetch the module
     *
     * @access	public
     * @return	string
     */
    function fetch_module() {
        return $this->module;
    }
}