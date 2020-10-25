<?php
 /**
 * 处理搜索关键字
 * @param  string $keyword 要解析的关键字（输入商品关键字/@货号/#编号）
 * @return array ['type' => 类型,'keyword' => 关键字];
 */
function skeyword(string $keyword = null){
    if(empty($keyword)){
        return false;
    }
    $str_first = mb_substr($keyword,0,1,'utf-8');
    $type = 0; //默认标题关键词
    switch ($str_first) {
        case '@':
            $type = 1; //货号
            break;
        case '#':
            $type = 2; //商品编号
            break;
        case '*':
            $type = 3; //商品编号
            break;  
    }
    if($type){
        $keyword = mb_substr($keyword,1,null,'utf-8');
        if(empty($keyword)){
            return false;
        }
    }
    return ['type'=>$type,'keyword' => $keyword];
}

/**
 * 倍减递归算法
 */
function numProgress($number,$n = 2,$i = 1){
    static $data = [];
    $number = intval($number/$n);
    if($number >= 1){
        $data[$i] = $number;
        numProgress($number,$n,$i+1);
    }
    return $data;
}