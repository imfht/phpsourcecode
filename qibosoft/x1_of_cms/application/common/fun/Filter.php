<?php
namespace app\common\fun;

//内容过滤
class Filter{
    
    static $bad_word = '<iframe |<script |<\/script>|<\/iframe>|<\?php ';   //过滤的字符
    
    /**
     * 过滤字符
     * @param string $content
     * @return void|mixed
     */
    public static function str($content=''){        
        if ($content=='') {
            return ;
        }
        strstr($content,'<pre ') && $content = preg_replace_callback('/<pre ([^>]+)>(.*?)<\/pre>/is',array(self,'replace_pre'),$content);     //必须排第一位,这是代码段
        
        strstr($content,'<script ') && $content = preg_replace_callback('/<script ([^>]+)>(.*?)<\/script>/is',array(self,'replace'),$content);
        strstr($content,'<iframe ') && $content = preg_replace_callback('/<iframe ([^>]+)>(.*?)<\/iframe>/is',array(self,'replace'),$content);
        strstr($content,'<?php') && $content = preg_replace_callback('/<\?php ([^>]+)>(.*?)\?>/is',array(self,'replace'),$content);
        
        $content = preg_replace_callback('/('.self::$bad_word.')/is',array(self,'replace'),$content);     //过滤漏网之鱼
        return $content;
    }
    
    /**
     * 全局过滤
     */
    public static function all($data=[]){
        $array = [];
        foreach ($data AS $key=>$content){
            if (preg_match('/('.self::$bad_word.')/is',$content)) {
                $array[$key] = self::str($content);
            }
        }
        return $array;
    }
    
    private function replace($array=[]){
        return str_replace(['<','>','"',"'"], ['&lt;','&gt;','&quot;','&#39;'], $array[0]);
    }

    private function replace_pre($array=[]){
        return str_replace($array[2],str_replace(['<','>','"',"'"], ['&lt;','&gt;','&quot;','&#39;'],$array[2]),$array[0]);
    }
    
    /**
     * 安全检查
     */
    public static function check_safe(){
        $array = input();
        foreach($array AS $key=>$value){
            if (is_array($value) || is_numeric($value) || preg_match('/^([-\w]*)$/', $value)) {
                continue;
            }
            if (preg_match("/([ \r\t\n]+)eval([ \r\t\n]*)\(/is", urldecode($value))) {
                die('内容中有非法字符eval(');
            }elseif (preg_match("/<\?php([ \r\t\n]+)/is", urldecode($value))) {
                die('内容中有非法字符?php');
            }
        }
    }
    
    
}