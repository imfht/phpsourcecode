<?php
/**
 * Conf
 */
class Conf{
	public static $conf;

	public static function get($k) {
        if (!isset(self::$conf[$k])) {
            return false;
        }
		return self::$conf[$k];
	}
	public static function set($k,$v) {
		self::$conf[$k] = $v;
		return true;
	}
	public static function init() {
		self::$conf = file_exists(EtcRoot."/conf.inc.php")?require EtcRoot."/conf.inc.php":false;
	}
}

/**
 * Router
 */
class Router{
    private static $routerconf;

    public static function init() {
    	self::$routerconf =   (EtcRoot."/router.inc.php")?require EtcRoot."/router.inc.php":false;
    }
    public static function analyze() {
        $url = strtolower($_SERVER["REQUEST_URI"]);
        $urlP = parse_url(strtolower($_SERVER["REQUEST_URI"]));
        $method = strtolower($_SERVER["REQUEST_METHOD"]);
        foreach (self::$routerconf as $k => $v) {
        	$pats = explode(" ", $v['pattern']);
        	if($pats[0] == $method || $pats[0] == '*'){
        		if(self::check($urlP['path'], $pats[1])){
                    return $v;
                }
            }
        }
        return null;
    }
    public static function check($uri, $regex) {
		$regex = ltrim(rtrim($regex, "/"));
		$regex = "/".str_replace("/", "\/", $regex)."\/?$/";
        if(preg_match($regex, $uri, $t)){
            return true;
        }
        return false;
    }
}

//respect
class Resp {
    public static $RespType;
    public static $RespData;
    public static function out() {
        switch (self::$RespType) {
            case 'json':
                if (is_array(self::$RespData)) {
                    echo json_encode(self::$RespData);
                } else {
                    echo json_encode([self::$RespData]);
                }
                break;
            default:
                if (is_array(self::$RespData) || is_object(self::$RespData)) {
                    echo "<pre>";
                    var_dump(self::$RespData);
                    echo "</pre>";
                } else {
                    echo self::$RespData;
                }
        }
    }
    public static function init() {
        self::$RespData = '';
        self::$RespType = empty(Conf::get('RespType'))?'text':$t;
    }
}
/**
 * App
 */
class App{
    public static $HandlerClass;
    public static $HandlerClassName;
    public static $HandlerFunction;
    public static $HandlerPath;
    public static $Objects;
	public static function run() {
        Conf::init();
        Router::init();
        Resp::init();
		$t = Router::analyze();
        if ($t != null) {
            list(self::$HandlerClassName, self::$HandlerFunction) = explode("#",$t['handler']);
            self::$HandlerPath = AppRoot.'/'.self::$HandlerClassName.'.Handler.php';
            self::$HandlerClass = self::$HandlerClassName."Handler";
        } else {
            self::$HandlerClassName = Conf::get('DefaultHandler');
            self::$HandlerFunction = Conf::get('DefaultFunction');
            self::$HandlerPath = AppRoot.'/'.self::$HandlerClassName.'.Handler.php';
            self::$HandlerClass = self::$HandlerClassName."Handler";
        }
        $f = self::$HandlerFunction;
        $c = self::$HandlerClass;
        //handle the function to get resp
        if(file_exists(self::$HandlerPath)){
            require_once self::$HandlerPath;
            if (class_exists($c, FALSE)) {
                self::$Objects[$c] = new $c();
                if (method_exists($c, $f)) {
                    Resp::$RespData = self::$Objects[$c]->$f();
                }
            }
        }
        //del the resp
        Resp::out();
	}
}