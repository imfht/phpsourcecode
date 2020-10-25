<?php
/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/13
 * Time: 上午11:06
 */

function TinyWiki($customConfigFile){
    $tinyWiki = new TinyWiki($customConfigFile);
    $tinyWiki->go();
}

class TinyWikiView
{
    private $vars;

    private static $instance;
    public static function getInstance(){
        if(self::$instance==null){
            self::$instance = new TinyWikiView();
        }
        return self::$instance;
    }

    function __construct()
    {
        $this->vars = [];
    }

    function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    function __get($name)
    {
        if(isset($this->vars[$name]))
            return $this->vars[$name];
        else
            return null;
    }

    function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    public function getAllProperty(){
        return $this->vars;
    }
};



class TinyWiki
{

    public  $configs;
    private $book;
    private $book_root;
    private $view_root;
    private $uri;

    function __construct($customConfigFile)
    {
        $this->configs = json_decode(file_get_contents(__DIR__."/config.default.json"), true);      //load default configs
        $customConfigs = json_decode(@file_get_contents($customConfigFile), true);                  //load custom configs
        if($customConfigs!=null) {
            foreach ($this->configs as $key => $value) {                                            //override default configs
                if (isset($customConfigs[$key])) {
                    $this->configs[$key] = $customConfigs[$key];
                }
            }
        }
        $this->normalizeConfig();
        $this->uri = explode("?", $_SERVER["REQUEST_URI"])[0];
        if(substr($this->uri, -1) != "/") {                                                         //add a / if uri not end with /
            header("location: ".$this->uri."/");
        }
        $this->removeLastSlash($this->uri);
    }

    private function normalizeConfig(){
        $this->addonLastSlash($this->configs["site_root"]);

        switch(gettype($this->configs["books"])){
            case "string":
            default:
                $this->removeLastSlash($this->configs["books"]);
                break;
            case "array":
                for($i=0; $i<count($this->configs["books"]); $i++){
                    $this->removeLastSlash($this->configs["books"][$i]["path"]);
                    $this->removeLastSlash($this->configs["books"][$i]["uri"]);
                }
                break;
        }
//        var_dump($this->configs);
//        exit;
    }

    /**
     * remove last '/' if end with '/'
     * @param $var
     */
    private function removeLastSlash(&$var){
        if($var!= "/" && substr($var, -1) == "/"){
            $var = substr($var, 0, -1);
        }
    }

    /**
     * add a '/' if not end with '/'
     * @param $var
     */
    private function addonLastSlash(&$var){
        if(substr($var, -1) != "/"){
            $var = $var . "/";
        }
    }

    /**
     * remove duplicate '/'
     * @param $var
     */
    private function removeDuplicateSlash(&$var){
        $var = preg_replace('/\/+/', '/', $var);
    }

    private function normalizePath(&$path){
        $path = preg_replace('/\\\\/', '/', $path);
        $this->removeDuplicateSlash($path);
    }

    private function noPermission(){
        return
                isset($this->book["password"])
                && $this->book["password"] != ""
                && @$_SESSION["tiny_wiki"][md5($this->uri)]["password"] != $this->book["password"];
    }

    private function login(){
        $this->getView()->CONFIG  = $this->configs;
        $this->getView()->ERR_MSG = "";
        if(isset($_POST["password"])){
            if(!isset($this->book["password"]) || $this->book["password"]=="" || $this->book["password"]==$_POST["password"]){
                @$_SESSION["tiny_wiki"][md5($this->uri)]["password"] = @$this->book["password"];
                header("location: ".$this->uri);
                exit;
            }else{
                $this->getView()->ERR_MSG = "Password Incorrect";
            }
        }
        $this->render("/login.php");
    }

    private function replyContent(){
        $route = $_GET["route"];
        $mdFile = null;
        if ($route != "") {
            if (file_exists($this->book_root . $route . ".md")) {                  //if exist ${route}.md, use it
                $mdFile = $this->book_root . $route . ".md";
            } else if (file_exists($this->book_root . $route . "/index.md")) {     //if exist ${route}/index.md, use it
                $mdFile = $this->book_root . $route . "/index.md";
            } else if (file_exists($this->book_root . "/404.md")) {                //if both them not exist, use custom 404.md in book root
                $mdFile = $this->book_root . "/404.md";
            }
        }
        if ($mdFile == null) {                                                                //if the book donnot provide a 404.md
            $mdContent = "# 404\n404 Not Found";                       //then use the default 404 content
        } else {
            $mdContent = file_get_contents($mdFile);
        }
        echo $mdContent;
    }

    private function layout(){
        $this->getView()->CONFIG = $this->configs;
        $this->getView()->BOOK   = $this->book;
        $this->getView()->MENU   = $this->book["menu"];
        $this->render("/layout.php");
    }

    private function getView(){
        return TinyWikiView::getInstance();
    }

    private function render($tpl){
        $vars = $this->getView()->getAllProperty();
        foreach ($vars as $key => $value){
            ${$key} = $value;
        }
        require_once $this->view_root . $tpl;
    }

    private function switchMode(){                          //login mode, content reply mode, layout mode
        if ($this->noPermission()) {                        //go login mode if no permission
            $this->login();
        } else {                                            //has permission
            if (isset($_GET["route"])) {                    //request a content if query arg route is set, so go content reply mode
                $this->replyContent();
            }else{
                $this->layout();
            }
        }
    }

    public function go()
    {
        session_start();
        $this->loadBooks();
        $this->switchMode();
    }

    private function loadBooks(){

        switch(gettype($this->configs["books"])){
            case "string":
            default:
                $this->book_root = __DIR__ . "/../".$this->configs["books"];
                break;
            case "array":
                for($i=0; $i<count($this->configs["books"]); $i++){
                    $tmp = $this->configs["site_root"] . $this->configs["books"][$i]["uri"];
                    $this->removeDuplicateSlash($tmp);
                    $this->removeLastSlash($tmp);
                    if($tmp==$this->uri){
                        $this->book_root = __DIR__ . "/../".$this->configs["books"][$i]["path"];
                        $this->normalizePath($this->book_root);
                        break;
                    }
                }
                if($this->book_root==null) {
                    header('HTTP/1.1 404 Not Found');
                    exit("book not exist!");
                }
                break;
        }

        $this->book = json_decode(file_get_contents($this->book_root . "/book.json"), true);        //load book defined in config
        if(isset($this->book["theme"]) && $this->book["theme"]!=""){
            $this->view_root = __DIR__ . "/../theme/".$this->book["theme"] . "/view";               //override theme if it's defined in book.json
        }else{
            $this->view_root = __DIR__ . "/../theme/".$this->configs["theme"] . "/view";            //use global theme config in default or custom config
        }
    }

}