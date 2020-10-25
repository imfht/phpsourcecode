<?php
//模板类，解析php页面
class Templates{

    //通过数组接受赋值
    private $_values = array();
    //保存模板内容
    private $_tpl = null;
    //保存配置文件内容
    public $_configs = array();


    //构造方法，验证目录是不存在
    public function __construct(){
        if(!is_dir(TEMP_DIR) || !is_dir(COMP_DIR) || !is_dir(CACHE_DIR)){
            //exit('目录设置不完全，请先设置！');
        }

        //引入配置文件
        $_confFile = parse_ini_file(ROOT_PATH.'/config/profile.ini');

        foreach($_confFile as $key => $value){
            $this->_configs["$key"] = $value;
        }
    }

    //assign()方法，用于变量注入
    public function assign($_key,$_value){
        //判断$_key
        if(isset($_key) && !empty($_key)){
            $this->_values[$_key] = $_value;
        }else{
            exit('请正确设置模板变量！');
        }
    }


    //display()方法，用于引入一个模板
    public function display($_file){

        //模板文件
        $_tempFile = TEMP_DIR.$_file;
        //判断模板
        if(!is_file($_tempFile)){
            exit(模板文件不存在！);
        }
        
        //编译文件
        $_compFile = COMP_DIR.md5($_file).$_file.'.php';
        //缓存文件
        $_cacheFile = CACHE_DIR.md5($_file).$_file.'.html';



        //判断是否可以直接引入缓存文件
        if(IS_CACHE){
            if(is_file($_cacheFile) && is_file($_compFile) && (filemtime($_compFile) >= filemtime($_tempFile)) && (filemtime($_cacheFile) >= filemtime($_compFile))){
                include $_cacheFile;
                return;
            }
        }
        
        //判断是不需要生成编译文件
        if(!is_file($_compFile) || (filemtime($_compFile) <= filemtime($_tempFile))){
            $this->compile($_tempFile,$_compFile);
        }
        
        //引入编译文件的内容并输出
        include $_compFile;
        
        //判断是否开启缓存件
        if(IS_CACHE){
            
            //判断是否要生成缓存文件
            if(!is_file($_cacheFile) || (filemtime($_cacheFile) <= filemtime($_compFile))){
                file_put_contents($_cacheFile,ob_get_contents());
                //清除缓冲区内容
                ob_end_clean();
                //直接载入缓存文件
            }    
            include $_cacheFile;
        }
    }






    

    //模板解析方法
    private function compile($_tempFile,$_compFile){
        
        //载入模板内容
        $this->_tpl = file_get_contents($_tempFile); 

        //解析变量
        $this->parVar();
        //解析if
        $this->parIf();
        //解析注释
        $this->parComment();
        //解析include
        $this->parInc();
        //解析foreach
        $this->parForeach();
        //解析配置文件
        $this->parConfig();


        //生成编译文件
        file_put_contents($_compFile,$this->_tpl);
        
    }

    




}
?>
