<?php
/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function gmtime() {
  return time() - date('Z');
}
/**
 * 获得服务器的时区
 *
 * @return  integer
 */
function server_timezone() {
  if (function_exists('date_default_timezone_get')) {
    return date_default_timezone_get();
  } else {
    return date('Z') / 3600;
  }
}
/**
 *  生成一个用户自定义时区日期的GMT时间戳
 *
 * @access  public
 * @param   int   $hour
 * @param   int   $minute
 * @param   int   $second
 * @param   int   $month
 * @param   int   $day
 * @param   int   $year
 *
 * @return void
 */
function local_mktime($hour = NULL, $minute = NULL, $second = NULL, $month = NULL, $day = NULL, $year = NULL) {
  $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['timezone'];
  /**
   * $time = mktime($hour, $minute, $second, $month, $day, $year) - date('Z') + (date('Z') - $timezone * 3600)
   * 先用mktime生成时间戳，再减去date('Z')转换为GMT时间，然后修正为用户自定义时间。以下是化简后结果
   **/
  return mktime($hour, $minute, $second, $month, $day, $year) - $timezone * 3600;
}
/**
 * 将GMT时间戳格式化为用户自定义时区日期
 *
 * @param  string     $format
 * @param  integer    $time     该参数必须是一个GMT的时间戳
 *
 * @return  string
 */
function local_date($format, $time = NULL) {
  $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['timezone'];
  if ($time === NULL) {
    $time = gmtime();
  } elseif ($time <= 0) {
    return '';
  }
  $time += $timezone * 3600;
  return date($format, $time);
}
/**
 * 转换字符串形式的时间表达式为GMT时间戳
 *
 * @param   string  $str
 *
 * @return  integer
 */
function gmstr2time($str) {
  $time = strtotime($str);
  if ($time > 0) {
    $time -= date('Z');
  }
  return $time;
}
/**
 *  将一个用户自定义时区的日期转为GMT时间戳
 *
 * @access  public
 * @param   string    $str
 *
 * @return  integer
 */
function local_strtotime($str) {
  $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['timezone'];
  /**
   * $time = mktime($hour, $minute, $second, $month, $day, $year) - date('Z') + (date('Z') - $timezone * 3600)
   * 先用mktime生成时间戳，再减去date('Z')转换为GMT时间，然后修正为用户自定义时间。以下是化简后结果
   **/
  return strtotime($str) - $timezone * 3600;
}
/**
 * 获得用户所在时区指定的时间戳
 *
 * @param   $timestamp  integer   该时间戳必须是一个服务器本地的时间戳
 *
 * @return  array
 */
function local_gettime($timestamp = NULL) {
  $tmp = local_getdate($timestamp);
  return $tmp[0];
}
/**
 * 获得用户所在时区指定的日期和时间信息
 *
 * @param   $timestamp  integer   该时间戳必须是一个服务器本地的时间戳
 *
 * @return  array
 */
function local_getdate($timestamp = NULL) {
  $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['timezone'];
  /* 如果时间戳为空，则获得服务器的当前时间 */
  if ($timestamp === NULL) {
    $timestamp = time();
  }
  $gmt = $timestamp - date('Z');
  // 得到该时间的格林威治时间
  $local_time = $gmt + $timezone * 3600;
  // 转换为用户所在时区的时间戳
  return getdate($local_time);
}
/**
 * 通过起始和结束的日期获得天数
 *
 * @param   $timestamp  integer   该时间戳必须是一个服务器本地的时间戳
 *
 * @return  array
 */
function getDateDiff($sday, $eday, $unit = 'd') {
  $diff = $eday - $sday;
  switch ($unit) {
    case 'y':
      $retval = bcdiv($diff, 60 * 60 * 24 * 365, 1);
      break;
    case 'm':
      $retval = bcdiv($diff, 60 * 60 * 24 * 30, 1);
      break;
    case 'w':
      $retval = bcdiv($diff, 60 * 60 * 24 * 7, 1);
      break;
    case 'd':
      $retval = bcdiv($diff, 60 * 60 * 24, 1);
      break;
    case 'h':
      $retval = bcdiv($diff, 60 * 60, 1);
      break;
    case 'n':
      $retval = bcdiv($diff, 60, 1);
      break;
    case 's':
      $retval = $diff;
      break;
    default:
      break;
  }
  return ceil($retval);
}
// 通过起始日期获取结束日期
// $sday 起始时间
// $days 间隔天数
// $case 0 返回格林威治时间;1 返回时间格式
function getEndDate($sday, $days, $case = 0) {
  $stime = date('Y-m-d H:i:s', $sday);
  $sarr = explode(' ', $stime);
  if (!empty($sarr)) {
    $symd = explode('-', $sarr[0]);
    $shis = explode(':', $sarr[1]);
    switch ($case) {
      case 0:
        $eday = mktime($shis[0], $shis[1], $shis[2], $symd[1], $symd[2] + $days, $symd[0]);
        break;
      case 1:
        $eday = date('Y-m-d H:i:s', mktime($shis[0], $shis[1], $shis[2], $symd[1], $symd[2] + $days, $symd[0]));
        break;
      default:
        break;
    }
    return $eday;
  } else {
    return '时间格式有问题';
  }
}
// 计算分期天数的函数
function getPeriodDays($d, $p) {
  $pd = @ceil($d / $p);
  $epd = $d - ($p - 1) * $pd;
  for ($i = 0; $i < $p; $i++) {
    if ($i == $p - 1) {
      $res[$i]['days'] = $epd;
    } else {
      $res[$i]['days'] = $pd;
    }
  }
  return $res;
}
// 毫秒级时间
function microtime_float($int=1) {
  list($msec,$sec) = explode(' ', microtime());
  if ($int==1) {
    return ((float)$msec + (float)$sec)*10000;
  } else {
    return ((float)$msec + (float)$sec);
  }
}
// 时间转16进制（无重复字串标识）
function time_hex($mt=0) {
  if (!empty($mt)) {
    $float = $mt;
  } else {
    $float = microtime_float();
  }
  return dechex(10000*$float);
}
//这个星期的星期一
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function this_monday($timestamp=0, $is_return_timestamp=true) {
  if (!$timestamp) {
    $timestamp = time();
  }
  $monday_date = date('Y-m-d', $timestamp-86400*date('w', $timestamp)+(date('w',$timestamp)>0 ? 86400 : -518400)); //6*86400
  if ($is_return_timestamp) {
    return strtotime($monday_date);
  } else {
    return $monday_date;
  }
}
function this_sunday($timestamp=0, $is_return_timestamp=true) {
  if (!$timestamp) {
    $timestamp = time();
  }
  $sunday = this_monday($timestamp) + 518400;
  if ($is_return_timestamp) {
    return $sunday;
  } else {
    return date('Y-m-d', $sunday);
  }
}
