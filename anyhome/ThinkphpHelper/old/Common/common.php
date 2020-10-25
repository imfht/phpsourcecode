<?php

//////////
function label($fname = '')
{ 
    $ac = ACTION_NAME;
    $mod = MODULE_NAME;
    $group = GROUP_NAME;
    $TableFiled = M('TableFiled');
    $map['ac'] = ACTION_NAME;
    $map['mod'] = MODULE_NAME;
    $map['group'] = GROUP_NAME;
    $map['fname'] = $fname;
    $vo = $TableFiled->where($map)->find();
    if ($vo['title']) return $vo['title'];
    return $fname;
}

function formField($fname = '',$val = '')
{
    $ac = ACTION_NAME;
    $mod = MODULE_NAME;
    $group = GROUP_NAME;
    $ipts = "<input type=\"text\" value=\"$val\" name=\"$fname\" id=\"$fname\" class=\"form-control\">";
    return $ipts;
}
function filterueditor( $value ) {
    $str = str_replace( "\n", "", $value );
    $str = str_replace( "\r", "", $str );
    $str = addcslashes($str,'/');
    return $str;
}

///跳转以后的通知
function afterNote($msg = "",$title = '', $time = 3000,$sticky = '')
{
  if ($msg) {
    $note['msg'] = $msg;
    $note['title'] = $title;
    $note['time'] = $time;
    $note['sticky'] = $sticky;
    F('after_Note',$note);
  }
}


function cut( $Str, $Length,$sss = '' ) {
  //$Str为截取字符串，$Length为需要截取的长度
  global $s;
  $i = 0;
  $l = 0;
  $ll = strlen( $Str );
  $s = $Str;
  $f = true;
  //if(isset($Str{$i}))
  while ( $i <= $ll ) {
    if ( ord( $Str{$i} ) < 0x80 ) {
      $l++; $i++;
    } else if ( ord( $Str{$i} ) < 0xe0 ) {
      $l++; $i += 2;
    } else if ( ord( $Str{$i} ) < 0xf0 ) {
      $l += 2; $i += 3;
    } else if ( ord( $Str{$i} ) < 0xf8 ) {
      $l += 1; $i += 4;
    } else if ( ord( $Str{$i} ) < 0xfc ) {
      $l += 1; $i += 5;
    } else if ( ord( $Str{$i} ) < 0xfe ) {
      $l += 1; $i += 6;
    }

    if ( ( $l >= $Length - 1 ) && $f ) {
      $s = substr( $Str, 0, $i );
      $f = false;
    }

    if ( ( $l > $Length ) && ( $i < $ll ) ) {
      // $s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
      $s = $s . $sss; break; //如果进行了截取，字符串末尾加省略符号“...”
    }
  }
  return $s;
}
////////////////




?>