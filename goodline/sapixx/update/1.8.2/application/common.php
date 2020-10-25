<?php
/**
 * 常用自定义函数库
 */
use think\Response;
use Ramsey\Uuid\Uuid as CreateUuid;
use think\facade\Request;

if (!function_exists('enjson')) {
    /**
     * 接口参数返回
     * @param mixed   $var  返回的数据
     * @param string  $msg  返回提示语,如果是数组就自动代替未返回的参数
     * @param integer $code 状态码 200
     * @return \think\response\Json
     */
    function enjson($code = 200,$msg = '',$var = [],$type = 'json'){
        switch ($code) {
            case 200:
                $error = '成功';
                break;
            case 401:
                $error = '未授权';
                break;
            case 403:
                $error = '没有权限';
                break;
            default:
                $error = '失败';
                break;
        }
        if(is_array($msg) || is_object($msg)){
            $var = $msg;
            $data['msg'] = $error;
        }else{
            $data['msg'] = empty($msg) ? $error : $msg;
        }
        if(isset($var['url'])){
            $data['url'] = $var['url'];
            unset($var['url']);
        }
        $data['code'] = $code;
        $data['data'] = $var;
        if($type == 'array'){
            return $data;
        }
        return Response::create($data,$type);
    }
}

if (!function_exists('code')) {
    /**
     * 友好的调试打印方法
     * @param $var
     */
    function code($var, $exit = true)
    {
        $output = print_r($var, true);
        $output = "<pre>" . htmlspecialchars($output, ENT_QUOTES)."</pre>";
        echo $output;
        if ($exit) {
            exit();
        }
    }
}

if (!function_exists('dehtml')) {
    /**
     * 把HTML实体转换为HTML可视标签
     */
    function dehtml($str)
    {
        return htmlspecialchars_decode($str);
    }
}
if (!function_exists('is_wechat')) {
    /**
     * 判断是否在微信中打开
     * @return boolean
     */
    function is_wechat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}
if (!function_exists('en_phone')) {
    /**
      * 隐藏手机号中间四位
      * @param [type] $phone
      * @return 从第三个字符隐藏4个字符
      */
    function en_phone($phone,$s = 3,$d = 4)
    {
        return substr_replace($phone, '****',$s,$d);
    }
}
if (!function_exists('money')) {
    /**
     * 格式化钱保留小数点
     * @param float $amount
     */
    function money($amount)
    {
        $amount = round($amount,2);
        return sprintf("%01.2f", $amount);
    }
}
if (!function_exists('uuid')) {
    /**
     * 获取唯一ID
     *
     * @param intege $type 类型
     * @param intege $data 要计算的KEY数据
     * @param Boolean $hex 返回的字符是否带-风格符
     * @return void
     */
    function uuid(int $type = 0, $hex = true, $data = null)
    {
        switch ($type) {
        case 1: //基于散列的MD5
            $uuid = CreateUuid::uuid4();
            break;
        case 2: //基于散列的MD5
            $uuid = CreateUuid::uuid3(CreateUuid::NAMESPACE_DNS, $data);
            break;
        case 3: //基于SHA1
            $uuid = CreateUuid::uuid5(CreateUuid::NAMESPACE_DNS, $data);
            break;
        default: //基于时间
            $uuid = CreateUuid::uuid1();
            break;
    }
        return $hex ? $uuid->getHex() : $uuid->toString();
    }
}
if (!function_exists('order_no')) {
    /**
     * 订单号
     */
    function order_no()
    {
        return \uuid\Intuuid::generateParticle();
    }
}
if (!function_exists('getcode')) {
    /**
     * 生成随机数
     * @param int $limit 要生成的随机数长度
     **/
    function getcode($limit=6)
    {
        $rand_array = range(0, 9);
        shuffle($rand_array);   //调用现成的数组随机排列函数
        $str = array_slice($rand_array, 0, $limit);//截取前$limit个
        return implode(null, $str);
    }
}
if (!function_exists('create_code')) {
    /**
     * 生成邀请码
     * @param int $id 要加密换算的ID
     *
    */
    function create_code($id)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $id;
        $code = '';
        while ($num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod].$code;
        }
        if (empty($code[3])) {
            $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        }
        return $code;
    }
}
if (!function_exists('de_code')) {
    /**
     * 解密邀请码
    * @param string $code 要解密经过create_code函数加密ID
     */
    function de_code($code)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        if (strrpos($code, '0') !== false) {
            $code = substr($code, strrpos($code, '0')+1);
        }
        $len = strlen($code);
        $code = strrev($code);
        $num = 0;
        for ($i=0; $i < $len; $i++) {
            $num += strpos($source_string, $code[$i]) * pow(35, $i);
        }
        return $num;
    }
}
if (!function_exists('ids')) {
    /**
     * Ids 参数强制转换为整形
     * @param  string|array $array 要强制转换的参数是字符串1,2,3还是数组[1,2,3]
     * @param  bool $is_ary 返回是字符串1,2,3还是数组[1,2,3]
     * @return string
     */
    function ids($ids,$is_ary = false)
    {
        if(empty($ids)){
            return $is_ary ? [] : '';
        }
        if (is_array($ids)) {
            $ids_ary = $ids;
        } else {
            $ids_ary = explode(',', trim($ids, ','));
        }
        $id_array = [];
        foreach ($ids_ary as $key => $value) {
            $id_array[$key] = abs(intval($value));
        }
        return $is_ary ? $id_array : implode(',', $id_array);
    }
}

if (!function_exists('array_values_unset')) {
    /**
     * 删除数组中指定键值
     * @param  array $arr  要删除的数组
     * @return array
     */
    function array_values_unset(string $values, array $ary)
    {
        $ary_key = array_search($values, $ary);
        if ($ary_key !== false) {
            unset($ary[$ary_key]);
        }
        return $ary;
    }
}
if (!function_exists('sbc2Dbc')) {
    /**
    * 全角转半角
    * @param string $str
    * @return string
    **/
    function sbc2Dbc($str)
    {
        $arr = array(
        '０'=>'0', '１'=>'1', '２'=>'2', '３'=>'3', '４'=>'4','５'=>'5', '６'=>'6', '７'=>'7', '８'=>'8', '９'=>'9',
        'Ａ'=>'A', 'Ｂ'=>'B', 'Ｃ'=>'C', 'Ｄ'=>'D', 'Ｅ'=>'E','Ｆ'=>'F', 'Ｇ'=>'G', 'Ｈ'=>'H', 'Ｉ'=>'I', 'Ｊ'=>'J',
        'Ｋ'=>'K', 'Ｌ'=>'L', 'Ｍ'=>'M', 'Ｎ'=>'N', 'Ｏ'=>'O','Ｐ'=>'P', 'Ｑ'=>'Q', 'Ｒ'=>'R', 'Ｓ'=>'S', 'Ｔ'=>'T',
        'Ｕ'=>'U', 'Ｖ'=>'V', 'Ｗ'=>'W', 'Ｘ'=>'X', 'Ｙ'=>'Y','Ｚ'=>'Z', 'ａ'=>'a', 'ｂ'=>'b', 'ｃ'=>'c', 'ｄ'=>'d',
        'ｅ'=>'e', 'ｆ'=>'f', 'ｇ'=>'g', 'ｈ'=>'h', 'ｉ'=>'i','ｊ'=>'j', 'ｋ'=>'k', 'ｌ'=>'l', 'ｍ'=>'m', 'ｎ'=>'n',
        'ｏ'=>'o', 'ｐ'=>'p', 'ｑ'=>'q', 'ｒ'=>'r', 'ｓ'=>'s', 'ｔ'=>'t', 'ｕ'=>'u', 'ｖ'=>'v', 'ｗ'=>'w', 'ｘ'=>'x',
        'ｙ'=>'y', 'ｚ'=>'z',
        '（'=>'(', '）'=>')', '〔'=>'(', '〕'=>')', '【'=>'[','】'=>']', '〖'=>'[', '〗'=>']', '“'=>'"', '”'=>'"',
        '‘'=>'\'', '’'=>'\'', '｛'=>'{', '｝'=>'}', '《'=>'<','》'=>'>','％'=>'%', '＋'=>'+', '—'=>'-', '－'=>'-',
        '～'=>'~','：'=>':', '。'=>'.', '、'=>',', '，'=>',', '、'=>',',  '；'=>';', '？'=>'?', '！'=>'!', '…'=>'-',
        '‖'=>'|', '”'=>'"', '’'=>'`', '‘'=>'`', '｜'=>'|', '〃'=>'"','　'=>' ', '×'=>'*', '￣'=>'~', '．'=>'.', '＊'=>'*',
        '＆'=>'&','＜'=>'<', '＞'=>'>', '＄'=>'$', '＠'=>'@', '＾'=>'^', '＿'=>'_', '＂'=>'"', '￥'=>'$', '＝'=>'=',
        '＼'=>'\\', '／'=>'/'
    );
        return strtr($str, $arr);
    }
}
if (!function_exists('api')) {
    /**
     * Url生成
     * @param integer $version API版本
     * @param string $url 访问网址
     * @param integer $appid 当前APPID
     * @param integer $id  传入参数
     * @param array $vars  变量
     * @return void
     */
    function api(int $version,$url,int $appid = 0,array $vars = []){
        $id = isset($vars['id']) ? '/'.$vars['id'] : '';
        if(isset($vars['id'])){
            unset($vars['id']);
        }
        $vars = empty($vars) ? '' : '?'.http_build_query($vars);
        $appid = $appid ? '-'.$appid : '';
        $url  = strtolower($url);
        $urls = explode('/',$url);
        if('system' == $urls[0]){
            unset($urls[0]);
            if('miniprogram' == strtolower($urls[1])){
                unset($urls[1]);
            }
            $urls  = implode('/',$urls);
            return Request::root(true)."/openapi{$appid}/v{$version}/{$urls}";
        }else{
            return Request::root(true)."/api{$appid}/v{$version}/{$url}{$id}.html".$vars;
        }
    }
}

if (!function_exists('urls')) {
    /**
     * Url生成
     * @param integer $version API版本
     * @param string $url 访问网址
     * @param integer $appid 当前APPID
     * @param integer $id  传入参数
     * @param array $vars  变量
     * @return void
     */
    function urls($url,int $appid = 0,array $vars = []){
        $id = isset($vars['id']) ? '/'.$vars['id'] : '';
        if(isset($vars['id'])){
            unset($vars['id']);
        }
        $vars  = empty($vars) ? '' : '?'.http_build_query($vars);
        $appid = $appid ? '-'.$appid : '';
        return Request::root(true).'/app'.$appid.'/'.strtolower($url).$id.$vars;
    }
}

if (!function_exists('md5sign')) {
    /**
     * 把参数通过自然排序然后加密
     * @param array $arrdata  加密的数组
     * @param string $signkey 加密Key
     * @return string
     */
    function md5sign($arrdata,$signkey){
        try {
            $arrdata = util\Util::array_remove_empty($arrdata);
            ksort($arrdata);
            $paramstring = "";
            foreach ($arrdata as $key=>$value) {
                if (isset($value) && !empty($value)) {
                    $str[] = $key."=".trim($value);
                }
            }
            $paramstring = join("&", $str);
            $Sign = md5($paramstring."&key=".$signkey);
            return $Sign;
        } catch (\Exception $e) {
            return $e;
        }
    }
}