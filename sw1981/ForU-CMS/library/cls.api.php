<?php
class Api {
  public $arr_succ = array('err'=>0,'msg'=>'success');
  public $arr_fail = array('err'=>50010,'msg'=>'Data error');
  public $token_file = '../api/access_token.txt';

  function dieJson($res) {
    die(json_encode($res, JSON_NUMERIC_CHECK));
  }

  function __destruct() {
    unset($this->arr_succ);
    unset($this->arr_fail);
    unset($this->token_file);
  }
}

class ApiCommon extends Api {
  function getCol($arr) {
    $tbl = str_safe($arr['tbl']);
    $col = str_safe($arr['col']);
    $size = !empty($arr['size']) ? intval($arr['size']) : 0;

    $res = $this->arr_succ;
    if ($res['ex'] = $GLOBALS['db']->getOne("SELECT $col FROM $tbl WHERE id = ".$arr['id'])) {
      $res['ex'] = $size ? str_cut(str_text($res['ex']), $size) : $res['ex'];
      $res['ex'] = $this->transData($res['ex']);
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getMenu($id) {
    $res = $this->arr_succ;
    if ($res['ex'] = $GLOBALS['db']->getAll("SELECT id,c_name FROM channel WHERE c_navigation=1 AND c_parent IN ($id)")) {
      $res['ex'] = $this->transData($res['ex']);
    } else {
      $res = $this->$arr_fail;
    }

    $this->dieJson($res);
  }

  function getChip($code) {
    $code = str_safe($code);

    $res = $this->arr_succ;
    if ($res['ex'] = $GLOBALS['db']->getOne("SELECT c_content FROM chip WHERE c_code='$code'")) {
      $res['ex'] = $this->transData($res['ex']);
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getRow($arr) {
    $tbl = str_safe($arr['tbl']);

    $res = $this->arr_succ;
    if ($res = $GLOBALS['db']->getRow("SELECT * FROM $tbl WHERE id = ".$arr['id'])) {
      $res['ex'] = $this->transData($res);
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getListDetail($arr) {
    $sub = get_channel($arr['id'], 'c_sub');
    $size = !empty($arr['size']) ? intval($arr['size']) : 0;

    include_once '../library/cls.page.php';
    if ($size) {
      $pager = new Page($size);
    } else {
      $pager = new Page(20);
    }

    $pager->handle($GLOBALS['db']->getOne("SELECT COUNT(*) FROM detail WHERE d_parent IN ($sub)"));

    $res = $this->arr_succ;
    $res['totalPage'] = $pager->page_sum;
    if ($res['ex'] = $GLOBALS['db']->getAll("SELECT * FROM detail WHERE d_parent IN ($sub) ORDER BY d_order ASC, id DESC LIMIT ".$pager->page_start.",".$pager->page_size)) {
      $res['ex'] = $this->transData($res['ex']);
      foreach ($res['ex'] as $key=>$val) {
        if (isset($val['d_price'])) list($res['ex'][$key]['cprice'],$res['ex'][$key]['oprice']) = explode('|', $val['d_price']);
      }
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getSlider($id=0) {
    $res = $this->arr_succ;
    $id = !empty($id) ? str_safe($id) : 0;

    if ($id) {
      $res['ex'] = $GLOBALS['db']->getAll("SELECT s_picture FROM slideshow WHERE s_parent = '$id' ORDER BY s_order ASC");
    } else {
      $res['ex'] = $GLOBALS['db']->getAll("SELECT s_picture FROM slideshow WHERE s_parent = 'mobile' ORDER BY s_order ASC");
    }
    if ($row = $res['ex']) {
      foreach ($row as $key => $value) {
        $res['ex'][$key]['s_picture'] = $this->transPicture($value['s_picture']);
      }
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getAttrValue($arr) {
    $str_ids = str_safe($arr['str_ids']);
    $type = str_safe($arr['type']);

    $arr_ids = explode(',', $str_ids);
    foreach ($arr_ids as $value) {
      $res[] = $GLOBALS['db']->getOne("SELECT ".($type == 'name' ? 'av_name' : 'id')." FROM goods_attr_value WHERE ".($type == 'name' ? 'id' : 'av_name')." = '$value'");
    }
    return implode(',', $res);
  }

  function transData($d) {
    // $arr_char = array("~","!","@","#","$","%","^","&","*","(",")","_",",","+","-","=","`","[","]","{","}",";","'",":","\"","\\","|",",",".","/","<",">","?");
    if (@preg_match("/([a-zA-z0-9\.\/\:]*)?(\/)?uploadfile/i", $d)) {
      $d = $this->transPicture($d);
    }
    elseif ($str = @$d['c_ifpicture']) {
      $d['c_picture'] = $this->transPicture($d['c_picture']);
      $d['c_content'] = $this->transPicture($d['c_content']);
      $d['c_scontent'] = $this->transPicture($d['c_scontent']);
    }
    elseif ($str = @$d[0]['c_ifpicture']){
      foreach ($d as $key=>$val) {
        $d[$key]['c_picture'] = $this->transPicture($val['c_picture']);
        $d[$key]['c_content'] = $this->transPicture($val['c_content']);
        $d[$key]['c_scontent'] = $this->transPicture($val['c_scontent']);
      }
    }
    elseif ($str = @$d['d_ifpicture']) {
      $d['date'] = local_date('Y-m-d', $d['d_date']);
      if ($d['d_ifpicture']) {
        $d['picture'] = $this->transPicture($d['d_picture']);
      }
      if ($d['d_ifslideshow']) {
        $d['slideshow'] = $this->transPicture($d['d_slideshow']);
      }
      $d['content'] = $this->transPicture($d['d_content']);
    }
    elseif ($str = @$d[0]['d_ifpicture']) {
      foreach ($d as $key=>$val) {
        if (@$d[$key]['d_date']) {
          $d[$key]['date'] = local_date('Y-m-d', $val['d_date']);
        }
        if ($d[$key]['d_ifpicture']) {
          $d[$key]['picture'] = $this->transPicture($val['d_picture']);
        }
        $d[$key]['slideshow'] = @$d[$key]['d_ifslideshow'] ? $this->transPicture($val['d_slideshow']) : '';
        $d[$key]['content'] = @$val['d_content'] ? $this->transPicture($val['d_content']) : '';
      }
    }
    return $d;
  }

  function transPicture($v) {
    $str = SERVER_URL . '/uploadfile';
    $pattern = "/([a-zA-z0-9\.\/\:]*)?(\/)?uploadfile/i";
    if (preg_match($pattern, $v)) {
      $res = preg_replace($pattern, $str, $v);
    } else {
      $res = $v;
    }
    return $res;
  }

  function transProp($did) {
    $did = intval($did);

    $res = $GLOBALS['db']->getAll("SELECT id,parent_id,p_name FROM prop WHERE d_id = $did ORDER BY id ASC");
    $arr_prop_main_ids = $GLOBALS['db']->getAll("SELECT id FROM prop WHERE d_id = $did");
    foreach($arr_prop_main_ids as $key=>$val) {
      $res[$key]['sub'] = $GLOBALS['db']->getAll("SELECT id,parent_id,p_name FROM prop WHERE parent_id = ".$val['id']." ORDER BY id ASC");
      $res[$key]['total_sub'] = count($res[$key]['sub']);
    }

    $count = count($res);
    $total = 0;
    for ($i=0; $i<$count; $i++) {
      foreach ($res[$i]['sub'] as $key=>$val) {
        $res[$i]['sub'][$key]['val'] = $GLOBALS['db']->getAll("SELECT id,p_value FROM prop_value WHERE p_id = ".$val['id']);
        $total = $total>count($res[$i]['sub'][$key]['val']) ? $total : count($res[$i]['sub'][$key]['val']);
      }
    }

    for ($t=0;$t<$count;$t++) {
      // 循环列
      for ($i=0;$i<$res[$t]['total_sub'];$i++) {
        // 循环行
        for ($j=0;$j<$total;$j++) {
          if ($i==2) {
            // 缩略图
            $res[$t]['prop_value'][$j][$i] = $this->transPicture(@$res[$t]['sub'][$i]['val'][$j]['p_value']);
          } elseif ($i==4) {
            // 对应尺寸ID转为值
            $arr_size = get_easy_array($GLOBALS['db']->getAll("SELECT p_value FROM prop_value WHERE id IN (".$res[$t]['sub'][$i]['val'][$j]['p_value'].")"), 'p_value');
            $res[$t]['prop_value'][$j][$i] = array_str($arr_size);
          } else {
            $res[$t]['prop_value'][$j][$i] = $res[$t]['sub'][$i]['val'][$j]['p_value'];
          }
        }
      }
    }

    return $res;
  }

  function getRowGoods($id) {
    $res = $this->arr_succ;

    if ($row = $GLOBALS['db']->getRow("SELECT * FROM goods WHERE id = $id")) {
      $res['ex'] = $this->transData($row);
      $arr = $GLOBALS['db']->getOne("SELECT MIN(ar_price) FROM goods_attr_relation WHERE ar_price >0 AND g_id = '$id'");
      $res['ex']['min_price'] = !empty($arr) ? $arr : $res['ex']['d_price'];
      $res['ex']['favorite'] = $GLOBALS['db']->getOne("SELECT id FROM user_favorite WHERE g_id = $id");
      $arr_rel = $GLOBALS['db']->getAll("SELECT * FROM goods_attr_relation WHERE g_id = $id");
      if ($row = $arr_rel) {
        foreach ($arr_rel as $key => $value) {
          // { priceId: 1, price: 35.0, "stock": 8, "attrValueList": [ { "attrKey": "型号", "attrValue": "2" }, { "attrKey": "颜色", "attrValue": "白色" }, { "attrKey": "大小", "attrValue": "小" }, { "attrKey": "尺寸", "attrValue": "1m" } ] }
          $res['prop'][$key]['priceId'] = $value['id'];
          $res['prop'][$key]['price'] = $value['ar_price'];
          $res['prop'][$key]['stock'] = $value['ar_quantity'];
          $res['prop'][$key]['names'] = $this->getAttrValue($value['av_ids'],'name');
          $res['prop'][$key]['stock'] = $value['ar_quantity'];
          $arr_a_ids = explode(',', $value['a_ids']);
          $arr_av_ids = explode(',', $value['av_ids']);
          foreach ($arr_a_ids as $k => $v) {
            $arr_temp = array();
            $arr_temp['attrKey'] = $GLOBALS['db']->getOne("SELECT a_name FROM goods_attr WHERE id = '$v'");
            $arr_temp['attrValue'] = $GLOBALS['db']->getOne("SELECT av_name FROM goods_attr_value WHERE id = '".$arr_av_ids[$k]."'");
            $arr_temp['selectedValue'] = $arr_av_ids[$k];
            $res['prop'][$key]['attrValueList'][] = $arr_temp;
          }
        }
      } else {
        $res['prop'] = '';
      }
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getListGoods($arr) {
    // 获取商品列表
    $sub = get_channel($arr['id'], 'c_sub');
    $size = !empty($arr['size']) ? intval($arr['size']) : 0;
    $where = !empty($arr['keyword']) ? "AND d_name LIKE '%".str_safe($arr['keyword'])."%'" : "";
    $where .= !empty($arr['rec']) ? " AND d_rec=1" : "";

    include_once '../library/cls.page.php';
    if ($size) {
      $pager = new Page($size);
    } else {
      $pager = new Page(20);
    }

    $pager->handle($GLOBALS['db']->getOne("SELECT COUNT(*) FROM goods WHERE d_parent IN ($sub) AND d_state=6 AND d_date<='".gmtime()."' $where"));

    $res = $this->arr_succ;
    $res['totalPage'] = $pager->page_sum;
    if ($row = $GLOBALS['db']->getAll("SELECT id,d_name,d_price,d_ifpicture,d_picture,d_tag FROM goods WHERE d_parent IN ($sub) AND d_state=6 AND d_date<='".gmtime()."' $where ORDER BY d_order ASC, id DESC LIMIT ".$pager->page_start.",".$pager->page_size)) {
      foreach ($row as $key=>$val) {
        $row[$key]['d_tags'] = explode(',', $val['d_tag']);
        $row[$key]['min_price'] = $GLOBALS['db']->getOne("SELECT MIN(ar_price) FROM goods_attr_relation WHERE ar_price >0 AND g_id = '".$val['id']."'");
      }
      $res['ex'] = $this->transData($row);
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }
}

class ApiUser extends Api {
  function check($openid) {
    $openid = str_safe($openid);
    $res = $this->arr_succ;

    if ($res['ex'] = $GLOBALS['db']->getOne("SELECT id FROM user WHERE u_openid = '$openid'")) {
      // pass
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function login($arr) {
    $openid = str_safe($arr['openid']);
    $mobnum = str_safe($arr['mobnum']);

    if ($arr = $GLOBALS['db']->getRow("SELECT id,u_mobile FROM user WHERE u_openid='$openid'")) {
      $res['ex'] = $arr['id'];
      if (empty($arr['u_mobile'])) {
        $GLOBALS['db']->query("UPDATE user SET u_mobile = '$mobnum'");
      }
    } else {
      $GLOBALS['db']->query("INSERT INTO user (u_openid,u_mobile) VALUES ('$openid','$mobnum')");
      $res['ex'] = $GLOBALS['db']->getOne("SELECT id FROM user WHERE u_openid='$openid'");
    }
    $res = $this->arr_succ;

    $this->dieJson($res);
  }

  function getUserInfo($openid) {
    $openid = str_safe($openid);
    $res = $this->arr_succ;

    if ($res['ex'] = $GLOBALS['db']->getRow("SELECT * FROM user WHERE u_openid = '$openid'")) {
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getOpenId($code) {
    $code = str_safe($code);
    $res = $this->arr_succ;

    $res['ex'] = https_get('https://api.weixin.qq.com/sns/jscode2session?appid='.APP_ID.'&secret='.APP_SECRET.'&js_code='.$code.'&grant_type=authorization_code');

    $this->dieJson($res);
  }

  function getMobileCode($openid) {
    $openid = str_safe($openid);
    $res = $this->arr_succ;

    if ($phoneNumbers = $GLOBALS['db']->getOne("SELECT u_mobile FROM user WHERE u_openid = '$openid'")) {
      $res['ex'] = str_code(6);
      // 发送短信
      /*
      include '../library/cls.alisms.php';
      $sms = new Alisms();
      $sms->send($phoneNumbers, $res['ex'], $signName, $templateParam);
      */
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function setOrder($arr) {
    include '../library/cls.wepay.php';

    $arr['u_id'] = intval($arr['u_id']);
    $arr['g_id'] = intval($arr['g_id']);
    $arr['o_sn'] = gmtime().str_code(6);
    $arr['o_prop'] = str_safe($arr['o_prop']);
    $arr['o_qty'] = str_safe($arr['o_qty']);
    $arr['o_cost'] = str_safe($arr['o_price']);
    $arr['o_state'] = 0;
    $arr['o_date'] = gmtime();

    if ($GLOBALS['db']->autoExecute('order', $arr, 'INSERT')) {
      $res = $this->arr_succ;
      $res['ex'] = $GLOBALS['db']->getRow("SELECT * FROM order WHERE u_id = ".$arr['u_id']." ORDER BY id DESC");
      // 调用微信统一下单
      $wepay = new Wepay();
      $wepay->pay($arr['o_cost'], $GLOBALS['db']->getOne("SELECT u_openid FROM user WHERE id = ".$arr['u_id']), $res['ex']['id']);
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function getAddr($openid) {
    $openid = str_safe($openid);

    if ($row = $GLOBALS['db']->getRow("SELECT u.id,u.u_tname,u.u_mobile,ua.ua_location FROM user AS u LEFT JOIN user_address AS ua ON ua.u_id = u.id WHERE u.u_openid = '$openid'")) {
      $res = $this->arr_succ;
      $res['ex'] = $row;
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }

  function addAddr($arr) {
    $uid = intval($arr['u_id']);
    $arr['u_tname'] = str_safe($arr['addr_name']);
    $arr['u_mobile'] = str_safe($arr['addr_mobile']);
    $openid = str_safe($arr['openid']);
    // 数据更新
    if ($id = $GLOBALS['db']->getOne("SELECT id FROM user WHERE u_openid='$openid'")) {
      $GLOBALS['db']->autoExecute('user', $arr, 'UPDATE', "id = $uid");
    }
    // 数据更新到地址表
    $arr = array();
    $arr['ua_location'] = str_safe($_POST['addr_address']);
    if ($aid = $GLOBALS['db']->getOne("SELECT id FROM user_address WHERE u_id=$uid")) {
      $GLOBALS['db']->autoExecute('user_address', $arr, 'UPDATE', "id = $aid");
    } else {
      $arr['u_id'] = $id;
      $GLOBALS['db']->autoExecute('user_address', $arr, 'INSERT');
    }

    $res = $this->arr_succ;

    $this->dieJson($res);
  }

  function delAddr($arr) {
    $uid = intval($arr['u_id']);
    $id = intval($arr['id']);

    $count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM user_address WHERE u_id = $uid");
    if ($count > 1) {
      $res = $this->arr_succ;
      $res['ex'] = $GLOBALS['db']->query("DELETE FROM user_address WHERE u_id=$uid AND id = $id");
    } else {
      $res = $this->arr_fail;
    }

    $this->dieJson($res);
  }
}

class ApiWechat extends Api {
  public $access_token;

  function checkToken() {
    // 读取文件
    if (is_file($this->token_file)) {
      $arr_token = json_decode(file_get_contents($this->token_file), true);
      if (time() > $arr_token['end_time']) {
        $arr = $this->getAccessToken();
        $this->updateFile($arr);
      } else {
        $this->access_token = $arr_token['access_token'];
      }
    } else {
      $arr = $this->getAccessToken();
      $this->updateFile($arr);
    }
    $res = $this->arr_succ;
    $res['ex'] = $this->access_token;

    $this->dieJson($res);
  }

  function sendMsgPay($arr) {
    $u_id = intval($arr['u_id']);
    $touser = str_safe($arr['open_id']);
    $page = @$arr['page'] ? intval($arr['page']) : "index";
    $prepay_id = str_safe($arr['prepay_id']);
    $arr_order = $db->getRow("SELECT o.*,g.d_name FROM order AS o INNER JOIN goods AS g ON g.id = o.g_id WHERE o.u_id = $u_id ORDER by o.id DESC");
    $value1 = $arr_order['o_sn'];
    $value2 = $arr_order['d_name'];
    $value3 = $arr_order['o_cost'];
    $value4 = local_date('Y/m/d', $arr_order['o_date']);

    $arr['touser'] = $touser;
    $arr['template_id'] = MINIP_TEMPLATE_ID_PAY;
    $arr['page'] = $page;
    $arr['form_id'] = $prepay_id;
    $arr['data'] = array(
      'keyword1'=>array('value'=>$value1),
      'keyword2'=>array('value'=>$value2),
      'keyword3'=>array('value'=>$value3),
      'keyword4'=>array('value'=>$value4)
      );
    $res = $this->arr_succ;
    $res['ex'] = $this->sendTemplateMsg($arr);

    $this->dieJson($res);
  }

  // 发送模板消息
  function sendTemplateMsg($arr) {
    $arr = json_decode(https_post('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$this->access_token, json_encode($arr, JSON_NUMERIC_CHECK)), true);
    return $arr;
  }

  function getAccessToken() {
    $res = json_decode(https_get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET), true);
    $this->access_token = $res['access_token'];
    $res['end_time'] = time()+MP_ETIME;
    return $res;
  }

  private function updateFile($arr) {
    // 写入文件
    $file = fopen($this->token_file, 'w') or die("Unable to open $this->token_file!");
    fwrite($file, json_encode($arr, JSON_NUMERIC_CHECK));
    fclose($file);
    return ;
  }
}
