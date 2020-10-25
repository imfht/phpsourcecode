<?php
/**
 * Printemps Framework 数据过滤拓展基类
 * (c)2015 Printemps Framework All rights reserved.
 */
class Printemps_Fliter{
    /**
     * 过滤<script></script>双标签的函数，防止因为Javascript脚本引起的XSS攻击
     * @param string $string 要过滤的内容
     * @return string 过滤后的内容
     */
    public static function fliteScirpt($string){
        if(preg_match("/<[s|S][c|C][r|R][i|I][p|P][t|T]>(.*?)<\/[s|S][c|C][r|R][i|I][p|P][t|T]>/",$string)){
            $string = preg_replace("/(<[s|S][c|C][r|R][i|I][p|P][t|T]>)/","",$string);
            $string = preg_replace("/<\/[s|S][c|C][r|R][i|I][p|P][t|T]>/","",$string);
            return $string;
        }
        else{
            return $string;
        }
    }
    /**
     * 删除SQL语句，从更高一层上防止SQL注入
     * 此函数需要谨慎使用=A= 一旦使用将会从字符中删除相关内容:)
     * @param string $sql 要过滤内容
     * @return string 过滤后的内容
     */
    public static function fliteSQL($sql){
        if(preg_match("/(select|insert|update|delete|drop|truecate|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile)/i",$sql)){
            $sql = preg_replace("/(select|insert|update|delete|drop|truecate|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile)/i","",$sql);
            return $sql;
        }
    }
}
