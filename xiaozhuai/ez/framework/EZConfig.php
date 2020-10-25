<?php
/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/14
 * Time: 下午6:54
 */

class EZConfig
{

    /**
     * define the static path pf the project, change this by call EZ()->init()
     */
    public $PROJECT_PATH                    = "";

    /**
     * define the root of the project, for example "/var/www/myproject",
     * then it should be set to "/myproject"
     */
    public $WEB_ROOT                        = "/";

    /**
     * define the ext name of view
     */
    public $VIEW_EXT                        = "phtml";

    /**
     * define the hyphen of view, for example, a controller "Home" has an action called "index", if set this to '.', then
     * view file name should be Home{$VIEW_ACTION_HYPHEN}index.{$VIEW_EXT} (Home.index.phtml).
     * if you want to put all action view under a child dir, then you can just set this to '/'.
     */
    public $VIEW_ACTION_HYPHEN              = "/";

    /**
     * define the view engine of view renderer, avaliable engines: php, smarty, twig, haml_php, haml_twig
     */
    public $VIEW_ENGINE                     = "php";

    public $PDO_DB_DSN                      = "";

    public $PDO_DB_USER                     = "root";

    public $PDO_DB_PWD                      = "";

    public $PDO_DB_OPTIONS                  = array();

    public $MONGO_DSN                       = "";

    public $MONGO_DBNAME                    = "";


    private $others;


    function __invoke(){
        $configs = array(
            "PROJECT_PATH"           =>   $this->PROJECT_PATH,
            "WEB_ROOT"               =>   $this->WEB_ROOT,
            "VIEW_EXT"               =>   $this->VIEW_EXT,
            "VIEW_ACTION_HYPHEN"     =>   $this->VIEW_ACTION_HYPHEN,
            "VIEW_ENGINE"            =>   $this->VIEW_ENGINE,
            "PDO_DB_DSN"             =>   $this->PDO_DB_DSN,
            "PDO_DB_USER"            =>   $this->PDO_DB_USER,
            "PDO_DB_PWD"             =>   $this->PDO_DB_PWD,
            "PDO_DB_OPTIONS"         =>   $this->PDO_DB_OPTIONS,
            "MONGO_DSN"              =>   $this->MONGO_DSN,
            "MONGO_DBNAME"           =>   $this->MONGO_DBNAME
        );
        return array_merge($configs, $this->others);
    }

    private static $instance = null;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new EZConfig();
        }
        return self::$instance;
    }

    function __construct(){
        $this->others = array();
    }

    public function overrideWith(&$config){
        foreach ($config as $key => $value){           //override config
            $this->{$key} = $value;
        }
    }

    /**
     * normalize all configs value
     */
    public function normalize(){
        EZPath::removeLastSlash($this->WEB_ROOT);
        $this->VIEW_ENGINE = strtolower($this->VIEW_ENGINE);
    }

    function __set($name, $value)
    {
        $this->others[$name] = $value;
    }

    function __get($name)
    {
        if(isset($this->others[$name]))
            return $this->others[$name];
        else
            return null;
    }

    function __isset($name)
    {
        return isset($this->others[$name]);
    }

}