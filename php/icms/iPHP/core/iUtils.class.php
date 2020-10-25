<?php

class iUtils {
    public static $debug = false;

    public static function INPUT($input=null,$name=false){
        $input===null && $input = file_get_contents("php://input");
        $name===null && self::LOG($input,'input');

        if ($input){
            if(strpos($input,'<xml>')!==false){
                $data = self::xmlToArray($input);
            }else{
                $data = json_decode($input,true);
                if(empty($data) && strpos($input,'&')!==false){
                    parse_str($input, $data);
                }
            }
            iSecurity::slashes($data);
            iWAF::check_data($data);
            return $data;
        }else{
            return false;
        }
    }

    public static function LOG($output=null,$name='debug'){
        if(iPHP_DEBUG||self::$debug){
            if($output==='RAW'){
                $output = file_get_contents("php://input");
            }
            $sub = substr(sha1(md5(iPHP_KEY)), 8,16);
            is_array($output) && $output = var_export($output,true);
            iFS::write(iPHP_APP_CACHE.'/'.$name.'.'.$sub.'.log',$output."\n",1,'ab+');
        }
    }
    /**
     * 将xml转为array
     * @param string $xml
     * @return array|false
     */
    public static function xmlToArray($xml,&$sxml=null){
        if (!$xml) {
            return false;
        }

        // 检查xml是否合法
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $xml, true)) {
            xml_parser_free($xml_parser);
            return false;
        }

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $sxml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($sxml), true);
        return $data;
    }
    /**
     * 输出xml字符
     * @param array $values
     * @return string|bool
     **/
    public static function arrayToXml($values)
    {
        if (!is_array($values) || count($values) <= 0) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml.="<".$key.">".$val."</".$key.">";
            } else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * [lastId 记录获取最后的ID]
     * @param  [type] $name [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public static function lastId($id=null,$name=null){
        $name ===null && $name = basename(iPHP_SELF);
        $path = dirname(iPHP_SELF).'/'.$name.'.lastId.txt';
        if($id===null){
            file_exists($path) OR file_put_contents($path, 1);
            $lastId  = (int)trim(file_get_contents($path));
            return $lastId;
        }
        file_put_contents($path, $id);
    }
}
