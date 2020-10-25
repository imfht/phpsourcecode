<?php
/**
 * wch_json.php UTF8
 * User: weicaihong.com
 * Date: 15/1/5 14:21
 * Copyright: http://www.weicaihong.com
 */

// 数据判断
if($data == FALSE)
{
    $data['errmsg'] = $act . ' empty data';
}
else
{
    $data['errmsg'] = 'ok';
}

if($post_data['debug'])
{
    echo $query_sql;
}

// 转换为json
if(!is_null($data))
{

    $json_data = json_encode($data);

    // 全部数据以UTF8 编码
    if($ec_charset != 'UTF8')
    {
        $json_data = mb_convert_encoding($json_data,'UTF-8','GBK');
    }

    // 输出
    echo $json_data;
    exit;
}
