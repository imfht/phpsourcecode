<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/8/6
 * Time: 13:59
 */

namespace zeus;


class Router
{
    /**
     * @var array $_listReqMethod List of access request method
     */

    private $_listReqMethod = array();

    /**
     * @var array $_listUri List of URI's to match against
     */
    private $_listUri = array();

    /**
     * @var array $_listCall List of closures to call
     */
    private $_listCall = array();

    /**
     * @var string $_trim Class-wide items to clean
     */
    private $_trim = '/\^$';

    private $_index = "____index_";

    public function __construct($sessionHandel=null)
    {
        ob_start();
        $this->sessionHandle($sessionHandel);
        $this->xss();

        header("X-Powered-By: ".ZEUS.'/'.VER,true);
    }

    public function get($uri,$function)
    {
        $this->add($uri,$function,"GET");
    }

    public function delete($uri,$function)
    {
        $this->add($uri,$function,"DELETE");
    }

    public function post($uri,$function)
    {
        $this->add($uri,$function,"POST");
    }

    public function put($uri,$function)
    {
        $this->add($uri,$function,"PUT");
    }

    public function all($uri,$function)
    {
        $this->add($uri,$function);
    }

    /**
     * add - Adds a URI and Function to the two lists
     *
     * @param string $uri A path such as about/system
     * @param object $function An anonymous function
     */
    private function add($uri, $function,$method = "any")
    {
        if( "/" == $uri ){
            $uri = $this->_index;
        }
        $uri = trim($uri, $this->_trim);
        $this->_listUri[] = $uri;
        $this->_listCall[] = $function;
        $this->_listReqMethod[] = $method;
    }

    /**
     * submit - Looks for a match for the URI and runs the related function
     * 自定义路由与实际路由的比较
     */
    public function dispatch()
    {
        $request_method = $_SERVER["REQUEST_METHOD"];
        $_uri = explode("?",$_SERVER["REQUEST_URI"])[0];
        $_uri = isset($_uri) ? $_uri : '/';

        $uri = trim($_uri, $this->_trim);
        $replacementValues = array();
        /**
         * List through the stored URI's
         */
        foreach ($this->_listUri as $listKey => $listUri)
        {
            if( "any" != $this->_listReqMethod[$listKey] && strtoupper($request_method) != strtoupper($this->_listReqMethod[$listKey]) )
            {
                continue;
            }

            if( $this->_index == $listUri && "/" == $_uri)
            {
                $this->doCall($this->_listCall[$listKey],$replacementValues);

                return;
            }

            if( "*" == $listUri )
            {
                $this->doCall($this->_listCall[$listKey],$replacementValues);

                return;
            }
            else
            {
                /**
                 * See if there is a match
                 */
                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $listUri);

                if( count($realUri) > count($fakeUri) )
                {
                    continue;
                }

                if (preg_match("#^$listUri$#", $uri))
                {
                    /**
                     * Gather the .+ values with the real values in the URI
                     */
                    foreach ($fakeUri as $key => $value)
                    {
                        if ($value == '.+')
                        {
                            $replacementValues[] = $realUri[$key];
                        }
                    }

                    /**
                     * Pass an array for arguments
                     */
                    $this->doCall($this->_listCall[$listKey], $replacementValues);
                    return;
                }
            }
        }
    }

    private function doCall($ns_action,$params=null)
    {
        if( !is_string($ns_action) ){

            return call_user_func_array($ns_action,$params);

        }else{
            $ns_action = explode("::",$ns_action);
            $class = $ns_action[0];
            $action = $ns_action[1];

            $obj = new $class;
            return call_user_func_array(array($obj,$action),$params);
        }
    }

    private function xss()
    {
        $preg_patterns = array(
            // Fix &entity\n
            //'!(&#0+[0-9]+)!' => '$1;',
            //'/(&#*\w+)[\x00-\x20]+;/u' => '$1;>',
            //'/(&#x*[0-9A-F]+);*/iu' => '$1;',
            //any attribute starting with "on" or xml name space
            //'#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu' => '$1>',
            //javascript: and VB script: protocols
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu' => '$1=$2nojavascript...',
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu' => '$1=$2novbscript...',
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u' => '$1=$2nomozbinding...',
            // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i' => '$1>',
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu' => '$1>',
            // namespace elements
            '#</*\w+:\w[^>]*+>#i' => '',
            //unwanted tags
            '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i' => '',

            '/\'/' => '&lsquo;',
            '/"/' => '&quot;',
            '/&/' => '&amp;',
            '/</' => '&lt;',
            '/>/' => '&gt;',
            //possible SQL injection remove from string with there is no '
            '/SELECT * FROM/' => ''
        );

        $patterns = array_keys($preg_patterns);
        $replacements = array_values($preg_patterns);

        $_GET = preg_replace($patterns,$replacements,$_GET);
        $_POST = preg_replace($patterns,$replacements,$_POST);
        $_COOKIE = preg_replace($patterns,$replacements,$_COOKIE);
    }

    private function sessionHandle($sessio_handle=null)
    {
        if( $sessio_handle && is_object($sessio_handle) && is_subclass_of($sessio_handle,"SessionHandlerInterface" ))
        {
            session_set_save_handler($sessio_handle, true);
        }
        else
        {
            session_save_path(zMkdir(LOG.DS."_session"));
        }
        session_start();
    }
}