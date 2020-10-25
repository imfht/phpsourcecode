<?php
/**
 * Created by PhpStorm.
 * User: targaryen
 * Date: 2017/2/26
 * Time: 上午11:41.
 */

/* ------------------------------------------------------------ */
/* ---------------------- 自定义全局函数 ----------------------- */
/* ------------------------------------------------------------ */

/**
 * 对应七牛 imageView2 图片处理接口.
 * @param string $image 图片原始路径
 * @param array $params 参数
 * @param int $mode 图片处理模式
 * @param int $q 图片质量
 * @return string
 */
function imageView2($image, array $params = [], $mode = 1, $q = 100)
{
    // 允许的参数
    $allowParams = ['w', 'h'];

    if (! is_string($image)) {
        return '';
    }

    if (empty($image) || strpos($image, '?')) {
        return $image;
    }
    $webp = request()->webp ? '/format/webp' : '';
    $queryString = '?imageView2/'.$mode.$webp;

    if (isset($params['raw'])) {
        return explode('?', $queryString)[0];
    }

    if (isset($params) && ! empty($params)) {
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                if (in_array($key, $allowParams)) {
                    $queryString .= '/'.$key.'/'.$value;
                }
            }
        }
    }

    $image = str_replace('//static.', '//statics.', $image);

    // 质量处理
    $qhandle = '';

    if ($q != 100) {
        $qhandle = '/q/'.$q;
    }

    return $image.$queryString.$qhandle;
}

/**
 * 获取 package.json 对象
 * @return mixed
 */
function package()
{
    $packageFilePath = base_path().'/package.json';

    return json_decode(file_get_contents($packageFilePath));
}

/**
 * 获取版本.
 * @return string
 */
function version()
{
    return isset(package()->version) ? package()->version : '0.0.1';
}

/**
 * 动态处理文章中的图片
 * 只在文章详情页使用.
 * @param $content
 * @return mixed
 */
function handleContentImage($content)
{
    $newImgs = [];

    preg_match_all('/<img.*?src="(.*?)".*?>/is', $content, $result);
    $oldImgs = [];
    foreach ($result[1] as $value) {
        array_push($oldImgs, 'src="'.$value.'"');
        array_push($newImgs, 'src="'.imageView2($value, [], 0, '100').'" class="'.env('LAZY_LOAD_CLASS').'"');
    }
    $content = str_replace($oldImgs, $newImgs, $content);

    return $content;
}

/**
 * 截取指定字符串之间的字符串.
 * @param $begin
 * @param $end
 * @param $str
 * @return string
 */
function cutString($begin, $end, $str)
{
    $b = mb_strpos($str, $begin) + mb_strlen($begin);
    $e = mb_strpos($str, $end) - $b;

    return mb_substr($str, $b, $e);
}

/**
 * 去掉字符串的所有空格
 * @param $str
 * @return mixed
 */
function trimAll($str)
{
    $before = [' ', '　', "\t", "\n", "\r"];
    $after = ['', '', '', '', ''];

    return str_replace($before, $after, $str);
}
