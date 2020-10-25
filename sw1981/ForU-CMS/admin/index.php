<?php
set_time_limit(120);
include '../library/inc.php';

switch ($act) {
  case 'clearUploadfile':
    clearUploadfile();
  break;

  case 'welcome':
    href('cms_welcome.php');
  break;

  case 'logout':
    userLogout('cms_login.php');
  break;

  case 'baiduSend':
    // 生成数组数据
    $y = date("Y");
    $m = date("m");
    $d = date("d");
    $days = mktime(0, 0, 0, $m, $d, $y);
    $daye = mktime(23, 59, 59, $m, $d, $y);
    getDetailBaiduSend($days, $daye);
  break;

  // reset admin password
  case 'reset_admin_psw':
    $psw = psw_hash('admin');
    $sql = "UPDATE user SET u_psw = '$psw' WHERE id = 1";
    $db->query($sql);
    echo $psw;
  break;

  default:
    header('location:cms_login.php');
  break;
}

function getDetailBaiduSend($s, $e) {
  $res = $GLOBALS['db']->getAll("SELECT id FROM detail WHERE d_date<" . $e . " AND d_date>=" . $s);
  if (!empty($res)) {
    $urls = array();
    foreach ($res as $val) {
      $urls[] = $GLOBALS['cms']['s_domain'] . '/' . d_url($val['id']) . ',';
    }
    // 百度推送
    $api = BAIDU_API;
    $ch = curl_init();
    $options =  array(
      CURLOPT_URL => $api,
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => implode("\n", $urls),
      CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $j = json_decode($result,true);
    if (!empty($j['error'])) {
      alert_back('error:' . $j['error'] . ', message:' . $j['message']);
    } else {
      alert_back('剩余:' . $j['remain'] . '条，成功:' . $j['success'] . '条');
    }
  } else {
    alert_back('无新数据无需推送');
  }
}

function clearUploadfile() {
  $tbls = array(
    "image" => array(
      "channel" => "SELECT COUNT(*) FROM channel WHERE INSTR(c_picture, '{file_name}') OR INSTR(c_content, '{file_name}') OR INSTR(c_scontent, '{file_name}') OR INSTR(c_cover, '{file_name}') OR INSTR(c_slideshow, '{file_name}')",
      "chip" => "SELECT COUNT(*) FROM chip WHERE INSTR(c_content, '{file_name}')",
      "detail" => "SELECT COUNT(*) FROM detail WHERE (INSTR(d_picture, '{file_name}') OR INSTR(d_content, '{file_name}') OR INSTR(d_scontent, '{file_name}') OR INSTR(d_slideshow, '{file_name}')) AND d_date >= {log_dir}",
      "feedback" => "SELECT COUNT(*) FROM feedback WHERE (INSTR(f_content, '{file_name}') OR INSTR(f_answer, '{file_name}')) AND f_adate >= {log_dir}",
      "link" => "SELECT COUNT(*) FROM link WHERE INSTR(l_picture, '{file_name}')",
      "slideshow" => "SELECT COUNT(*) FROM slideshow WHERE INSTR(s_picture, '{file_name}')",
      "system" => "SELECT COUNT(*) FROM system WHERE INSTR(s_description, '{file_name}') OR INSTR(s_copyright, '{file_name}')"
    ),
    "file" => array(
      "channel" => "SELECT COUNT(*) FROM channel WHERE INSTR(c_content, '{file_name}') OR INSTR(c_scontent, '{file_name}')",
      "chip" => "SELECT COUNT(*) FROM chip WHERE INSTR(c_content, '{file_name}')",
      "detail" => "SELECT COUNT(*) FROM detail WHERE (INSTR(d_content, '{file_name}') OR INSTR(d_scontent, '{file_name}') OR INSTR(d_attachment, '{file_name}')) AND d_date >= {log_dir}",
      "feedback" => "SELECT COUNT(*) FROM feedback WHERE (INSTR(f_content, '{file_name}') OR INSTR(f_answer, '{file_name}')) AND f_adate >= {log_dir}",
      "system" => "SELECT COUNT(*) FROM system WHERE INSTR(s_description, '{file_name}') OR INSTR(s_copyright, '{file_name}')"
    )
  );

  // 获取log文件
  $log = ROOT_PATH . "uploadfile/clear_log.txt";
  if (is_file($log)) {
    $log_txt = file_get_contents($log);
    if (!empty($log_txt)) {
      list($log_dir, $log_date) = explode(' ', $log_txt);
    }
  } else {
    $log_dir = '0';
    $log_date = '0';
  }

  // 获取上传文件类型,目前只对image类型文件夹做清理
  $file_type = array("image","file");

  // 获取类型目录下的日期文件夹
  foreach ($file_type as $key => $value) {
    $type = $value;
    $type_dir = ROOT_PATH . "uploadfile/" . $value;
    $arr = scandir($type_dir);
    foreach ($arr as $val) {
      if ($val>=$log_dir && checkFile($val)) {
        $arr_file = scandir($type_dir . "/" . $val);
        foreach ($arr_file as $v) {
          if (checkFile($v)) {
            $res[$value][$val][] = $v;
          }
        }
      }
      $log_dir = $val;
    }
    clearFiles($tbls,$res,$type,$type_dir,$log_dir,$log);
  }

  alert_back("无用的上传文件清理完毕!");
}

function clearFiles($tbls,$res,$type,$type_dir,$log_dir,$log) {
  // 循环结果比对
  foreach ($res[$type] as $key => $arr) {
    $dir_date = mktime(0, 0, 0, getDirMonth($key), getDirDay($key), getDirYear($key));
    foreach ($arr as $file_name) {
      $count = 0;
      foreach ($tbls[$type] as $tbl=>$sql) {
        $sql = str_replace('{file_name}', $file_name, $sql);
        $sql = str_replace('{log_dir}', $log_dir, $sql);
        $count += $GLOBALS['db']->getOne($sql);
      }
      if ($count) {
        continue;
      } else {
        $f = "$type_dir/$file_name";
        @unlink($f);
        echo $f . '<BR>';
      }
    }
  }

  // 写入log文件
  if (isset($type) && isset($log_dir)) {
    $file = fopen($log, "w") or die("无法创建文件!");
    fwrite($file, $log_dir . " " . date('Y-m-d H:i:s', time()));
    fclose($file);
  }
}

function getDirYear($str) {
  return substr($str, 0, 4);
}
function getDirMonth($str) {
  return substr($str, 4, 2);
}
function getDirDay($str) {
  return substr($str, 6, 2);
}
function checkFile($str) {
  return $str!='.' && $str!='..' && $str!='index.html' && $str!='index.htm' ? true : false;
}
?>
