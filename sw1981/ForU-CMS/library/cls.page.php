<?php
/*
使用范例
$p = new Page(12);
$p->handle($db->getOne("SELECT COUNT(*) FROM detail));
$list = $db->getAll("SELECT * FROM cms_detail ORDER BY id DESC LIMIT ".$p->page_start.",".$p->page_size);
echo '<ul>';
foreach ($list as $key => $value) {
  echo '<li>'.$value['id'].'</li>';
}
echo '</ul>';
echo $p->show();
*/

class Page {
  public $record_total;
  public $page_parse;
  public $page_size;
  public $page_current;
  public $page_sum;
  public $page_from = 0;
  public $page_start = 0;
  public $page_end = 0;
  public $page_radius;
  public $page_structure;

  public function __construct($page_size, $page_radius=2, $page_parse='page', $page_structure='') {
    $this->page_size = is_numeric($page_size) ? $page_size : 1;
    $this->page_radius = $page_radius;
    $this->page_parse = $page_parse;
    $this->page_structure = !empty($page_structure) ? $page_structure : cms('page_structure');
  }

  //分页初始化
  function handle($record_total) {
    $this->record_total = $record_total;

    if (isset($_GET[$this->page_parse])) {
      non_numeric_href($_GET[$this->page_parse], $_lang['illegal'], './');
      $this->page_current = str_safe($_GET[$this->page_parse]);
      if (empty($this->page_current) || $this->page_current < 1 || !is_numeric($this->page_current)) {
        $this->page_current = 1;
      } else {
        $this->page_current = intval($this->page_current);
      }
    } else {
      $this->page_current = 1;
    }
    if ($this->record_total == 0) {
      $this->page_sum = 1;
    } else {
      $this->page_sum = ceil($this->record_total / $this->page_size);
    }
    if ($this->page_current > $this->page_sum) {
      $this->page_current = $this->page_sum;
    }
    $this->page_start = ($this->page_current - 1) * $this->page_size;
  }

  // 参数说明：0.总页数。1.当前页。2.分页参数。3.分页半径。4.包含元素
  function show($page_structure='') {
    $page_sum = $this->page_sum;
    $page_current = $this->page_current;
    $page_parameter = $this->page_parse;
    $page_len = $this->page_radius;
    $page_start = '';
    $page_end = '';
    $page_start = $page_current - $page_len;
    if ($page_start <= 0) {
      $page_start = 1;
      $page_end = $page_start + $page_end;
    }
    $page_end = $page_current + $page_len;
    if ($page_end > $page_sum) {
      $page_end = $page_sum;
    }
    $page_link = $this->get_uri();
    $tmp_arr = parse_url($page_link);
    if (REWRITE) {
      $page_arr = explode('-', $page_link);
      if (count($page_arr) > 2) {
        $page_link = $page_arr[0] . '-' . $page_arr[1] . '-';
      } else {
        $dot_arr = explode('.', $page_arr[1]);
        $page_link = $page_arr[0] . '-' . $dot_arr[0] . '-';
      }
    } else {
      if (isset($tmp_arr['query'])) {
        $url = $tmp_arr['path'];
        $query = $tmp_arr['query'];
        parse_str($query, $arr);
        unset($arr[$page_parameter]);
        if (count($arr) != 0) {
          $page_link = $url . '?' . http_build_query($arr) . '&';
        } else {
          $page_link = $url . '?';
        }
      } else {
        $page_link = $page_link . '?';
      }
    }
    $page_back = '';
    $page_home = '';
    $page_list = '';
    $page_last = '';
    $page_next = '';
    $tmp = '';

    $arr = !empty($page_structure) ? $page_structure : $this->page_structure;

    if (REWRITE) {
      if ($page_current > $page_len + 1) {
        $page_home = $arr[0] . $page_link . $arr[1];
      }
      if ($page_current == 1) {
        $page_back = $arr[2];
      } else {
        $page_back = $arr[0] . $page_link . ($page_current - 1) . $arr[3];
      }
      for ($i = $page_start; $i <= $page_end; $i++) {
        if ($i == $page_current) {
          $page_list = $page_list . $arr[4] . $i . $arr[5];
        } else {
          $page_list = $page_list . $arr[0] . $page_link . $i . $arr[6] . $i . $arr[7] . $i . $arr[5];
        }
      }
      if ($page_current < $page_sum - $page_len) {
        $page_last = $arr[0] . $page_link . $page_sum . $arr[8];
      }
      if ($page_current == $page_sum) {
        $page_next = $arr[9];
      } else {
        $page_next = $arr[0] . $page_link . ($page_current + 1) . $arr[10];
      }
    } else {
      if ($page_current > $page_len + 1) {
        $page_home = $arr[0] . $page_link . $page_parameter . $arr[11];
      }
      if ($page_current == 1) {
        $page_back = $arr[2];
      } else {
        $page_back = $arr[0] . $page_link . $page_parameter . '=' . ($page_current - 1) . $arr[12];
      }
      for ($i = $page_start; $i <= $page_end; $i++) {
        if ($i == $page_current) {
          $page_list = $page_list . $arr[16] . $i . $arr[17];
        } else {
          $page_list = $page_list . $arr[0] . $page_link . $page_parameter . '=' . $i . $arr[13] . $i . $arr[7] . $i . $arr[5];
        }
      }
      if ($page_current < $page_sum - $page_len) {
        $page_last = $arr[0] . $page_link . $page_parameter . '=' . $page_sum . $arr[14];
      }
      if ($page_current == $page_sum) {
        $page_next = $arr[9];
      } else {
        $page_next = $arr[0] . $page_link . $page_parameter . '=' . ($page_current + 1) . $arr[15];
      }
    }
    $tmp = $tmp . $page_home . $page_back . $page_list . $page_next . $page_last;
    return $tmp;
  }

  public function get_uri() {
    if (isset($_SERVER['REQUEST_URI'])) {
      $uri = $_SERVER['REQUEST_URI'];
    } else {
      if (isset($_SERVER['argv'])) {
        $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
      } else {
        $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
      }
    }
    return $uri;
  }

  public function __destruct() {
  }
}
