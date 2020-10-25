<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-11 04:48:28
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-21 15:01:47
 */


namespace common\helpers;

use Yii;
use yii\helpers\BaseStringHelper;
use Ramsey\Uuid\Uuid;

class StringHelper extends BaseStringHelper
{
    /**
     * 生成Uuid
     *
     * @param string $type 类型 默认时间 time/md5/random/sha1/uniqid 其中uniqid不需要特别开启php函数
     * @param string $name 加密名
     * @return string
     * @throws \Exception
     */
    public static function uuid($type = 'time', $name = 'php.net')
    {
        switch ($type) {
                // 生成版本1（基于时间的）UUID对象
            case  'time':
                $uuid = Uuid::uuid1();

                break;
                // 生成第三个版本（基于名称的和散列的MD5）UUID对象
            case  'md5':
                $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);

                break;
                // 生成版本4（随机）UUID对象
            case  'random':
                $uuid = Uuid::uuid4();

                break;
                // 产生一个版本5（基于名称和散列的SHA1）UUID对象
            case  'sha1':
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $name);

                break;
                // php自带的唯一id
            case  'uniqid':
                return md5(uniqid(md5(microtime(true) . self::randomNum(8)), true));

                break;
        }

        return $uuid->toString();
    }

    /**
     * 日期转时间戳
     *
     * @param $value
     * @return false|int
     */
    public static function dateToInt($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return strtotime($value);
        }

        return $value;
    }

    /**
     * 时间戳转日期
     *
     * @param $value
     * @return false|int
     */
    public static function intToDate($value, $format = 'Y-m-d H:i:s')
    {
        if (empty($value)) {
            return date($format);
        }

        if (is_numeric($value)) {
            return date($format, $value);
        }

        return $value;
    }

    /**
     * 获取缩略图地址
     *
     * @param string $url
     * @param int $width
     * @param int $height
     */
    public static function getThumbUrl($url, $width, $height)
    {
        $url = str_replace('attachment/images', 'attachment/thumb', $url);
        return self::createThumbUrl($url, $width, $height);
    }

    /**
     * 创建缩略图地址
     *
     * @param string $url
     * @param int $width
     * @param int $height
     */
    public static function createThumbUrl($url, $width, $height)
    {
        $url = explode('/', $url);
        $nameArr = explode('.', end($url));
        $url[count($url) - 1] = $nameArr[0] . "@{$width}x{$height}." . $nameArr[1];

        return implode('/', $url);
    }

    /**
     * 获取压缩图片地址
     *
     * @param $url
     * @param $quality
     * @return string
     */
    public static function getAliasUrl($url, $alias = 'compress')
    {
        $url = explode('/', $url);
        $nameArr = explode('.', end($url));
        $url[count($url) - 1] = $nameArr[0] . "@{$alias}." . $nameArr[1];

        return implode('/', $url);
    }



    /**
     * 分析枚举类型配置值
     *
     * 格式 a:名称1,b:名称2
     *
     * @param $string
     * @return array
     */
    public static function parseAttr($string)
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }

        return $value;
    }

    /**
     * 返回字符串在另一个字符串中第一次出现的位置
     *
     * @param $string
     * @param $find
     * @return bool
     * true | false
     */
    public static function strExists($string, $find)
    {
        return !(strpos($string, $find) === false);
    }


    public static function subtext($text, $length)
    {
        if(mb_strlen($text, 'utf8') > $length) {
            return mb_substr($text, 0, $length, 'utf8').'...';
        } else {
            return $text;
        }
    
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @param  [string]  $str     [字符串]
     * @param  integer $start   [起始位置]
     * @param  integer $length  [截取长度]
     * @param  string  $charset [字符串编码]
     * @param  boolean $suffix  [是否有省略号]
     * @return [type]           [description]
     */
    public static function msubstr($str, $start=0, $length=15, $charset="utf-8", $suffix=false) {
        if(function_exists("mb_substr")) {
            return mb_substr($str, $start, $length, $charset);
        } elseif(function_exists('iconv_substr')) {
            return iconv_substr($str,$start,$length,$charset);
        }
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        if($suffix) {
            return $slice."…";
        }
        return $slice;
    }

    
    /**
     * @计算中文字符串长度，只支持UTF8编码
     */
    public static function utf8_strlen($string = null) {
        preg_match_all("/./us", $string, $match);
        return count($match[0]);
    }

    /**
     *
     * 中英混合字符串长度判断
     * @param unknown_type $str
     * @param unknown_type $charset
     */
    public static function strLength($str, $charset = 'utf-8') {
        if ($charset == 'utf-8')
        $str = iconv ( 'utf-8', 'gb2312', $str );
        $num = strlen ( $str );
        $cnNum = 0;
        for($i = 0; $i < $num; $i ++) {
        if (ord ( substr ( $str, $i + 1, 1 ) ) > 127) {
            $cnNum ++;
            $i ++;
        }
        }
        $enNum = $num - ($cnNum * 2);
        $number = ($enNum / 2) + $cnNum;
        return ceil ( $number );
    }

    /**
     *
     * 中英混合的字符串截取
     * @param unknown_type $sourcestr
     * @param unknown_type $cutlength
     */
    public static function cut_str($sourcestr, $cutlength,$suffix="...") {
        $returnstr = '';
        $i = 0;
        $n = 0;
        $str_length = strlen ( $sourcestr ); //字符串的字节数
        while ( ($n < $cutlength) and ($i <= $str_length) ) {
        $temp_str = substr ( $sourcestr, $i, 1 );
        $ascnum = Ord ( $temp_str ); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) //如果ASCII位高与224，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 3 ); //根据UTF-8编码规范，将3个连续的字符计为单个字符  
            $i = $i + 3; //实际Byte计为3
            $n ++; //字串长度计1
        } elseif ($ascnum >= 192) //如果ASCII位高与192，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 2 ); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n ++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
            $i = $i + 1; //实际的Byte数仍计1个
            $n ++; //但考虑整体美观，大写字母计成一个高位字符
        } else //其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
        }
        }
        if ($str_length > $cutlength) {
            $returnstr = $returnstr .$suffix; //超过长度时在尾处加上省略号
        }
        return $returnstr;
    }

    public static function  currency_format($currency, $decimals = 2) {
        $currency = floatval($currency);
        if (empty($currency)) {
            return '0.00';
        }
        $currency = number_format($currency, $decimals);
        $currency = str_replace(',', '', $currency);
        return $currency;
    }

    /**
     * XML 字符串载入对象中
     *
     * @param string $string 必需。规定要使用的 XML 字符串
     * @param string $class_name 可选。规定新对象的 class
     * @param int $options 可选。规定附加的 Libxml 参数
     * @param string $ns
     * @param bool $is_prefix
     * @return bool|\SimpleXMLElement
     */
    public static function simplexmlLoadString(
        $string,
        $class_name = 'SimpleXMLElement',
        $options = 0,
        $ns = '',
        $is_prefix = false
    ) {
        libxml_disable_entity_loader(true);
        if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
            return false;
        }

        return simplexml_load_string($string, $class_name, $options, $ns, $is_prefix);
    }

    /**
     * 字符串提取汉字
     *
     * @param $string
     * @return mixed
     */
    public static function strToChineseCharacters($string)
    {
        preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $string, $chinese);

        return $chinese;
    }

    /**
     * 字符首字母转大小写
     *
     * @param $str
     * @return mixed
     */
    public static function strUcwords($str)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }

    /**
     * 驼峰命名法转下划线风格
     *
     * @param $str
     * @return string
     */
    public static function toUnderScore($str)
    {
        $array = [];
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] == strtolower($str[$i])) {
                $array[] = $str[$i];
            } else {
                if ($i > 0) {
                    $array[] = '-';
                }

                $array[] = strtolower($str[$i]);
            }
        }

        return implode('', $array);
    }

    /**
     * 获取字符串后面的字符串
     *
     * @param string $fileName 文件名
     * @param string $type 字符类型
     * @param int $length 长度
     * @return bool|string
     */
    public static function clipping($fileName, $type = '.', $length = 0)
    {
        return substr(strtolower(strrchr($fileName, $type)), $length);
    }

    /**
     * 获取随机字符串
     *
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));

        $hash = '';
        if (!$numeric) {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }

        $max = strlen($seed) - 1;
        $seed = str_split($seed);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }

        return $hash;
    }

    /**
     * 获取数字随机字符串
     *
     * @param bool $prefix 判断是否需求前缀
     * @param int $length 长度
     * @return string
     */
    public static function randomNum($prefix = false, $length = 8)
    {
        $str = $prefix ?? '';
        return $str . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $length);
    }

    /**
     * 字符串匹配替换
     *
     * @param $search
     * @param $replace
     * @param $subject
     * @param null $count
     * @return mixed
     */
    public static function replace($search, $replace, $subject, &$count = null)
    {
        return str_replace($search, $replace, $subject, $count);
    }

    /**
     * 验证是否Windows
     *
     * @return bool
     */
    public static function isWindowsOS()
    {
        return strncmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * 将一个字符串部分字符用*替代隐藏
     *
     * @param string $string 待转换的字符串
     * @param int $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
     * @param int $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
     * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
     * @param string $glue 分割符
     * @return bool|string
     */
    public static function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@")
    {
        if (empty($string)) {
            return false;
        }

        $array = [];
        if ($type == 0 || $type == 1 || $type == 4) {
            $strlen = $length = mb_strlen($string);

            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, "utf8");
                $string = mb_substr($string, 1, $strlen, "utf8");
                $strlen = mb_strlen($string);
            }
        }

        switch ($type) {
            case 0:
                for ($i = $bengin; $i < ($bengin + $len); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", $array);
                break;
            case 1:
                $array = array_reverse($array);
                for ($i = $bengin; $i < ($bengin + $len); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", array_reverse($array));
                break;
            case 2:
                $array = explode($glue, $string);
                $array[0] = self::hideStr($array[0], $bengin, $len, 1);
                $string = implode($glue, $array);
                break;
            case 3:
                $array = explode($glue, $string);
                $array[1] = self::hideStr($array[1], $bengin, $len, 0);
                $string = implode($glue, $array);
                break;
            case 4:
                $left = $bengin;
                $right = $len;
                $tem = array();
                for ($i = 0; $i < ($length - $right); $i++) {
                    if (isset($array[$i])) {
                        $tem[] = $i >= $left ? "*" : $array[$i];
                    }
                }

                $array = array_chunk(array_reverse($array), $right);
                $array = array_reverse($array[0]);
                for ($i = 0; $i < $right; $i++) {
                    $tem[] = $array[$i];
                }
                $string = implode("", $tem);
                break;
        }

        return $string;
    }

    /**
     * 第一个参数：生成名字的数目；
     * 第一个参数：生成电话号码数目；
     * 第二个参数：生成的类型（支持数组，字符串格式，默认为数组）；
     * 第三个参数：生成的电话号码中间是否有空格（默认为有空格）.
     *
     * @param int|null post
     * @return string
     * @throws NotFoundHttpException
     */
    public static function generate_mobile($count, $type = "array", $white_space = false)
    {
        $arr = array(
            130, 131, 132, 133, 134, 135, 136, 137, 138, 139,
            144, 147,
            150, 151, 152, 153, 155, 156, 157, 158, 159,
            176, 177, 178,
            180, 181, 182, 183, 184, 185, 186, 187, 188, 189,
        );
        for ($i = 0; $i < $count; $i++) {
            $tmp[] = $arr[array_rand($arr)] . ' ' . mt_rand(1000, 9999) . ' ' . mt_rand(1000, 9999);
        }
        if ($type === "string") {
            $tmp = json_encode($tmp); //如果是字符串，解析成字符串
        }
        if ($white_space === true) {
            $tmp = preg_replace("/\s*/", "", $tmp);
        }
        return $tmp;
    }

    /**
     * 数字转换为中文
     * @param  integer  $num  目标数字
     */
    public static function number2chinese($num)
    {
        if (is_int($num) && $num < 100) {
            $char = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
            $unit = ['', '十', '百', '千', '万'];
            $return = '';
            if ($num < 10) {
                $return = $char[$num];
            } elseif ($num%10 == 0) {
                $firstNum = substr($num, 0, 1);
                if ($num != 10) $return .= $char[$firstNum];
                $return .= $unit[strlen($num) - 1];
            } elseif ($num < 20) {
                $return = $unit[substr($num, 0, -1)]. $char[substr($num, -1)];
            } else {
                $numData = str_split($num);
                $numLength = count($numData) - 1;
                foreach ($numData as $k => $v) {
                    if ($k == $numLength) continue;
                    $return .= $char[$v];
                    if ($v != 0) $return .= $unit[$numLength - $k];
                }
                $return .= $char[substr($num, -1)];
            }
            return $return;
        }
    }

    public static function isimplexml_load_string($string, $class_name = 'SimpleXMLElement', $options = 0, $ns = '', $is_prefix = false) {
        libxml_disable_entity_loader(true);
        if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
            return false;
        }
        $string = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f\\x7f]/', '', $string); 	
        return simplexml_load_string($string, $class_name, $options, $ns, $is_prefix);
    }

    public static function getXml($result) {
		if(substr($result, 0 , 5) != "<xml>"){
			return $result;
		}
		$results = json_decode(json_encode(self::isimplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if (!is_array($results)) {
			return 'xml结构错误';
		}
        $logPath = Yii::getAlias('@runtime/wechat/payparameters'.date('ymd').'.log');

        FileHelper::writeLog($logPath, 'xml处理中'.json_encode($results));
		return $results;
    }
    
    /**
     * url参数转化成数组
     * @auth xieyang
     * @date 2018年5月10日 13:51:23
     * @param string
     * @return mixed
     */
    public static function convertUrlArray($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     *数组 转化url参数
    * @auth xieyang
    * @date 2018年5月10日 13:51:31
    * @param string
    * @return mixed
    */
    public static function getUrlString($array_query)
    {
        $tmp = array();
        foreach($array_query as $k=>$param)
        {
            $tmp[] = $k.'='.$param;
        }
        $params = implode('&',$tmp);
        return $params;
    }

    // 生成随机的昵称
    public static function  getname($name_count = 1)
    {
        $firstname_arr  = array(
            '赵', '钱', '孙', '李', '周', '吴', '郑', '王', '冯', '陈', '褚', '卫', '蒋', '沈', '韩', '杨', '朱', '秦', '尤', '许', '何', '吕', '施', '张', '孔', '曹', '严', '华', '金', '魏', '陶', '姜',
            '戚', '谢', '邹', '喻', '柏', '水', '窦', '章', '云', '苏', '潘', '葛', '奚', '范', '彭', '郎', '鲁', '韦', '昌', '马', '苗', '凤', '花', '方', '任', '袁', '柳', '鲍', '史', '唐', '费', '薛', '雷', '贺', '倪',
            '汤', '滕', '殷', '罗', '毕', '郝', '安', '常', '傅', '卞', '齐', '元', '顾', '孟', '平', '黄', '穆', '萧', '尹', '姚', '邵', '湛', '汪', '祁', '毛', '狄', '米', '伏', '成', '戴', '谈', '宋', '茅', '庞', '熊',
            '纪', '舒', '屈', '项', '祝', '董', '梁', '杜', '阮', '蓝', '闵', '季', '贾', '路', '娄', '江', '童', '颜', '郭', '梅', '盛', '林', '钟', '徐', '邱', '骆', '高', '夏', '蔡', '田', '樊', '胡', '凌', '霍', '虞',
            '万', '支', '柯', '管', '卢', '莫', '柯', '房', '裘', '缪', '解', '应', '宗', '丁', '宣', '邓', '单', '杭', '洪', '包', '诸', '左', '石', '崔', '吉', '龚', '程', '嵇', '邢', '裴', '陆', '荣', '翁', '荀', '于',
            '惠', '甄', '曲', '封', '储', '仲', '伊', '宁', '仇', '甘', '武', '符', '刘', '景', '詹', '龙', '叶', '幸', '司', '黎', '溥', '印', '怀', '蒲', '邰', '从', '索', '赖', '卓', '屠', '池', '乔', '胥', '闻', '莘',
            '党', '翟', '谭', '贡', '劳', '逄', '姬', '申', '扶', '堵', '冉', '宰', '雍', '桑', '寿', '通', '燕', '浦', '尚', '农', '温', '别', '庄', '晏', '柴', '瞿', '阎', '连', '习', '容', '向', '古', '易', '廖', '庾',
            '终', '步', '都', '耿', '满', '弘', '匡', '国', '文', '寇', '广', '禄', '阙', '东', '欧', '利', '师', '巩', '聂', '关', '荆', '司马', '上官', '欧阳', '夏侯', '诸葛', '闻人', '东方', '赫连', '皇甫', '尉迟',
            '公羊', '澹台', '公冶', '宗政', '濮阳', '淳于', '单于', '太叔', '申屠', '公孙', '仲孙', '轩辕', '令狐', '徐离', '宇文', '长孙', '慕容', '司徒', '司空'
        );
        $lastname_arr  = array(
            '伟', '刚', '勇', '毅', '俊', '峰', '强', '军', '平', '保', '东', '文', '辉', '力', '明', '永', '健', '世', '广', '志', '义', '兴', '良', '海', '山', '仁', '波', '宁', '贵', '福', '生', '龙',
            '元', '全', '国', '胜', '学', '祥', '才', '发', '武', '新', '利', '清', '飞', '彬', '富', '顺', '信', '子', '杰', '涛', '昌', '成', '康', '星', '光', '天', '达', '安', '岩', '中', '茂', '进', '林', '有', '坚',
            '和', '彪', '博', '诚', '先', '敬', '震', '振', '壮', '会', '思', '群', '豪', '心', '邦', '承', '乐', '绍', '功', '松', '善', '厚', '庆', '磊', '民', '友', '裕', '河', '哲', '江', '超', '浩', '亮', '政', '谦',
            '亨', '奇', '固', '之', '轮', '翰', '朗', '伯', '宏', '言', '若', '鸣', '朋', '斌', '梁', '栋', '维', '启', '克', '伦', '翔', '旭', '鹏', '泽', '晨', '辰', '士', '以', '建', '家', '致', '树', '炎', '德', '行',
            '时', '泰', '盛', '雄', '琛', '钧', '冠', '策', '腾', '楠', '榕', '风', '航', '弘', '秀', '娟', '英', '华', '慧', '巧', '美', '娜', '静', '淑', '惠', '珠', '翠', '雅', '芝', '玉', '萍', '红', '娥', '玲', '芬',
            '芳', '燕', '彩', '春', '菊', '兰', '凤', '洁', '梅', '琳', '素', '云', '莲', '真', '环', '雪', '荣', '爱', '妹', '霞', '香', '月', '莺', '媛', '艳', '瑞', '凡', '佳', '嘉', '琼', '勤', '珍', '贞', '莉', '桂',
            '娣', '叶', '璧', '璐', '娅', '琦', '晶', '妍', '茜', '秋', '珊', '莎', '锦', '黛', '青', '倩', '婷', '姣', '婉', '娴', '瑾', '颖', '露', '瑶', '怡', '婵', '雁', '蓓', '纨', '仪', '荷', '丹', '蓉', '眉', '君',
            '琴', '蕊', '薇', '菁', '梦', '岚', '苑', '婕', '馨', '瑗', '琰', '韵', '融', '园', '艺', '咏', '卿', '聪', '澜', '纯', '毓', '悦', '昭', '冰', '爽', '琬', '茗', '羽', '希', '欣', '飘', '育', '滢', '馥', '筠',
            '柔', '竹', '霭', '凝', '晓', '欢', '霄', '枫', '芸', '菲', '寒', '伊', '亚', '宜', '可', '姬', '舒', '影', '荔', '枝', '丽', '阳', '妮', '宝', '贝', '初', '程', '梵', '罡', '恒', '鸿', '桦', '骅', '剑', '娇',
            '纪', '宽', '苛', '灵', '玛', '媚', '琪', '晴', '容', '睿', '烁', '堂', '唯', '威', '韦', '雯', '苇', '萱', '阅', '彦', '宇', '雨', '洋', '忠', '宗', '曼', '紫', '逸', '贤', '蝶', '菡', '绿', '蓝', '儿', '翠',
            '钱', '孙', '李', '周', '吴', '郑', '王', '冯', '陈', '褚', '卫', '蒋', '沈', '韩', '杨', '朱', '秦', '尤', '许', '何', '吕', '施', '张', '孔', '曹', '严', '华', '金', '魏', '陶', '姜', '戚', '谢', '邹', '喻', '柏', '水', '窦', '章', '云', '苏', '潘', '葛', '奚', '范', '彭', '郎', '鲁', '韦', '昌', '马', '苗', '凤', '花', '方', '任', '袁', '柳', '鲍', '史', '唐', '费', '薛', '雷', '贺', '倪', '汤', '滕', '殷', '罗', '毕', '郝', '安', '常', '傅', '卞', '齐', '元', '顾', '孟', '平', '黄', '穆', '萧', '尹', '姚', '邵', '湛', '汪', '祁', '毛', '狄', '米', '伏', '成', '戴', '谈', '宋', '茅', '庞', '熊', '纪', '舒', '屈', '项', '祝', '董', '梁', '杜', '阮', '蓝', '闵', '季', '贾', '路', '娄', '江', '童', '颜', '郭', '梅', '盛', '林', '钟', '徐', '邱', '骆', '高', '夏', '蔡', '田', '樊', '胡', '凌', '霍', '虞', '万', '支', '柯', '管', '卢', '莫', '柯', '房', '裘', '缪', '解', '应', '宗', '丁', '宣', '邓', '单', '杭', '洪', '包', '诸', '左', '石', '崔', '吉', '龚', '程', '嵇', '邢', '裴', '陆', '荣', '翁', '荀', '于', '惠', '甄', '曲', '封', '储', '仲', '伊', '宁', '仇', '甘', '武', '符', '刘', '景', '詹', '龙', '叶', '幸', '司', '黎', '溥', '印', '怀', '蒲', '邰', '从', '索', '赖', '卓', '屠', '池', '乔', '胥', '闻', '莘', '党', '翟', '谭', '贡', '劳', '逄', '姬', '申', '扶', '堵', '冉', '宰', '雍', '桑', '寿', '通', '燕', '浦', '尚', '农', '温', '别', '庄', '晏', '柴', '瞿', '阎', '连', '习', '容', '向', '古', '易', '廖', '庾', '终', '步', '都', '耿', '满', '弘', '匡', '国', '文', '寇', '广', '禄', '阙', '东', '欧', '利', '师', '巩', '聂', '关', '荆',
            '烟'
        );
        //,$file_name='name.txt' 
        // }
        $temp = [];
        for ($j = 1; $j <= $name_count; $j++) {
            $firstname_rand_key   = mt_rand(0, count($firstname_arr) - 1);
            $firstname   =  $firstname_arr[$firstname_rand_key];
            $name_length = mt_rand(1, 2);
            $lastname = '';
            for ($i = 1; $i <= $name_length; $i++) {
                $lastname_rand_key = mt_rand(0, count($lastname_arr) - 1);
                $lastname    .= $lastname_arr[$lastname_rand_key];
            }
            $temp[] = $firstname . $lastname;
        }
        return $temp;
    }
}
