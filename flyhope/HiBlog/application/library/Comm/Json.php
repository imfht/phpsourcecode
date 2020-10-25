<?php
/**
 * JSON工具
 * 
 * @author chengxuan <i@chengxuan.li>
 */
namespace Comm;
class Json {

    /**
     * 通过中文不编码方式encode JSON
     *
     * @param mixed $data 编码内容
     *
     * @return \string
     */
    static public function encode($data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}
