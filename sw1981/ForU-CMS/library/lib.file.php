<?php
//返回当前的脚本名
function self_name() {
  return ltrim(strrchr($_SERVER['PHP_SELF'], "/"),"/");
}
// 获取后缀名
function get_file_ext($s) {
  return ltrim(strrchr($s, "."), ".");
}
// 获取文件名
function get_file_name($s) {
  return strstr($s, ".", true);
}

// 文件绝对路径转相对路径
function handle_cont($s) {
  $pattern="/(\/[0-9a-z]+)?\/uploadfile/i";
  return SITE_SUB ? $s : preg_replace($pattern,'uploadfile',$s);
}

// 替换默认图片
function img_always($s) {
  if (!empty($s)) {
    $pattern="/(\/[0-9a-z]+)?\/uploadfile/i";
    return SITE_SUB ? $s : preg_replace($pattern,'uploadfile',$s);
  } else {
    return 'uploadfile/pic.jpg';
  }
}

// 判断目录是否存在不存在则创建
function mk_dir($dir, $mode = 0777) {
  if (is_dir($dir) || @mkdir($dir, $mode)) {
    return TRUE;
  }
  if (!mk_dir(dirname($dir), $mode)) {
    return FALSE;
  }
  return @mkdir($dir, $mode);
}

/*
 * 该方法是把数据库读出的数据进行CSV文件输出，能接受百万级别的数据输出，因为用生成器，不用担心内存溢出。
 * @param string $sql 需要导出的数据SQL
 * @param string $mark 生成文件的名字前缀
 * @param bool $is_multiple 是否要生成多个CSV文件
 * @param int $limit 每隔$limit行，刷新输出buffer,以及每个CSV文件行数限制
 *
 */
function csv_export(&$data, $titleList = '', $fileName = '') {
  ini_set("set_time_limit", 0);
  ini_set("max_execution_time", "3600");

  $dir = '../'. DATA_DIR;

  if (!is_dir($dir)){
    if (mk_dir($dir) === false) {
      alert_back($lang['mkdir_error']);
    }
  }

  $fileName = empty($fileName) ? date('YmdHis', time()) : $fileName;
  $filePath = $dir . '/'.$fileName . '.csv';

  $fp = fopen($filePath, 'w');
  fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码,防止乱码
  fputcsv($fp, $titleList);
  foreach ($data as $key => $row)
  {
    fputcsv($fp, $row);
  }
  fclose($fp);

  href($filePath);
}
