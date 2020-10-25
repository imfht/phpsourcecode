<?php

/**
 * @return mixed
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 给浏览器静态资源加版本号,强制刷新缓存
 * @param  string $source 资源路径
 * @return string         资源路径加上版本号
 */
function loadEdition($source)
{
    $version = '1.00';

    return $source . '?v=' . $version;
}

/**
 * 返回错误信息页面提示
 * @param null $message
 * @param null $url
 * @param null $view
 * @param string $type
 * @param int $wait
 * @return \Illuminate\Http\Response
 */
function viewError($message = null, $url = null, $type = 'error' ,$view = null, $wait = 3)
{
    $view = $view ? $view : 'admin.commons.'.$type;

    return response()->view($view,[
        'url'=> $url ? route($url) : '/',
        'message'=>$message ? $message : '发生错误,请重试!',
        'wait' => $wait,
    ]);
}


/**
 * [unique_arr 去除二维数组重复值]
 * @return [type] [返回值是二维数组]
 */
function unique_arr($array2D,$stkeep=false,$ndformat=true){

    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if($stkeep) $stArr = array_keys($array2D);	//返回数据的下标

    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if($ndformat) $ndArr = array_keys(end($array2D));	//返回二维数组的最后一个下标

    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串,结果是索引一维数组
    foreach ($array2D as &$v){
        if(isset($v['pivot']))
        {
            unset($v['pivot']);
        }
        $v = implode(",",$v);
        $temp[] = $v;
    }

    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);

    //再将拆开的数组重新组装
    foreach ($temp as $k => $v)
    {
        if($stkeep) $k = $stArr[$k];
        if($ndformat)
        {
            $tempArr = explode(",",$v);
            foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
        }
        else $output[$k] = explode(",",$v);
    }

    return $output;
}