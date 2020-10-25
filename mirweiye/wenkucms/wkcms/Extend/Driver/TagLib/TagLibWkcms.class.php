<?php

class TagLibWkcms extends TagLib{

    // 标签定义
    protected $tags   =  array(

        //用户标签
        'user' => array('attr'=>'type,field,how,roleid,pagenum,where,order,return', 'close'=>1),
        //文档标签
        'doc' => array('attr'=>'type,field,own,uid,usertype,ext,minscore,maxscore,pagenum,cateid,day,where,order,return', 'close'=>1),
        'doctotalnum'=>array('attr'=>'type,field,own,uid,usertype,ext,cateid,day,where,order,return', 'close'=>1),
        //加载资源
        'load' => array('attr'=>'type,href', 'close'=>0),
    );

    public function __call($method, $args) {
        $tag = substr($method, 1);
        if (!isset($this->tags[$tag])) return false;
        //解析标签属性
        $_tag = parent::parseXmlAttr($args[0], $tag);
        $_tag['cache'] = isset($_tag['cache']) ? intval($_tag['cache']) : 0;
        $_tag['return'] = isset($_tag['return']) ? trim($_tag['return']) : 'data';
        $_tag['type'] = isset($_tag['type']) ? trim($_tag['type']) : '';
        if (!$_tag['type']) return false;
        $parse_str  = '<?php ';
        if ($_tag['cache']) {
            //标签名-属性-属性值 组合标识
            ksort($_tag);
            $tag_id = md5($tag . '&' . implode('&', array_keys($_tag)) . '&' . implode('&', array_values($_tag)));
            //缓存代码开始
            $parse_str .= '$' . $_tag['return'] . ' = S(\'' . $tag_id . '\');';
            $parse_str .=  'if (false === $' . $_tag['return'] . ') { ';
        }
        $action = $_tag['type'];
        $class = '$tag_' . $tag . '_class';
        $parse_str .= $class . ' = new ' . $tag . 'Tag;';
        $parse_str .= '$' . $_tag['return'] . ' = ' . $class . '->' . $action . '(' . self::arr_to_html($_tag) . ');';
        if ($_tag['cache']) {
            //缓存代码结束
            $parse_str .= 'S(\'' . $tag_id . '\', $' . $_tag['return'] . ', ' . $_tag['cache'] . ');';
            $parse_str .= ' }';
        }
        $parse_str .= '?>';
        $parse_str .= $args[1];
        return $parse_str;
    }

    /**
     * 转换数据为HTML代码
     * @param array $data
     */
    private static function arr_to_html($data) {
        if (is_array($data)) {
            $str = 'array(';
                foreach ($data as $key=>$val) {
                    if (is_array($val)) {
                        $str .= "'$key'=>".self::arr_to_html($val).",";
                    } else {
                        if (strpos($val, '$')===0) {
                            $str .= "'$key'=>$val,";
                        } else {
                            $str .= "'$key'=>'".addslashes_deep($val)."',";
                        }
                    }
                }
                return $str.')';
            }
            return false;
        }
    }