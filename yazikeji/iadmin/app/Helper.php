<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/24
 * Time: 下午3:48
 */

/**
 * 显示提示信息
 * @param $message
 * @param string $redirectTo
 * @param int $timeTo
 */
function showMessage($message, $redirectTo='', $timeTo=3000) {
    \Session::flash('flashMessage', $message);
    \Session::flash('timeTo', $timeTo);
    if ($redirectTo) {
        \Session::flash('redirectTo', $redirectTo);
    }
}

/**
 * 获取不带有child的数组结构
 * @param $data
 * @param int $pid
 * @param int $lev
 * @return array
 */
function getSubTree(&$data , $pid = 0 , $lev = 1) {
    static $son = array();
    foreach($data as $key => $value) {
        if($value['pid'] == $pid) {
            $value['lev'] = $lev;
            $son[] = $value;
            unset($data[$key]);
            getSubTree($data , $value['id'] , $lev+1);
        }
    }
    return $son;
}

/**
 * 获取父子结构数组
 * @param $data
 * @param int $pid
 * @return array
 */
function getTree($data, $pid=0)
{
    $tree = [];

    foreach ($data as $row) {

        if ($row['pid'] == $pid) {

            $child = getTree($data, $row['id']);

            if ($child) {
                $row['child'] = $child;
            }

            $tree[] = $row;
        }

    }
    return $tree;
}

function getCheckboxTree($data, $name, $originalData, $child=false)
{
    $html = '';
    foreach ($data as $row) {
        $checked = '';
        foreach ($originalData as $original) {
            if ($row['id'] == $original['id']) {
                $checked = 'checked';
            }
        }
        if ($row['child']) {
            $html .= '<div style="clear: both;"><li><input type="checkbox" name="'.$name.'[]" title="'.$row['display_name'].'" value="'.$row['id'].'" lay-skin="primary" '.$checked.'>';
            $html .= getCheckboxTree($row['child'], $name, $originalData, true);
            $html .= '</li>';
        } else {
            $html .= '<li style="float: left"><input type="checkbox" name="'.$name.'[]" title="'.$row['display_name'].'" value="'.$row['id'].'"lay-skin="primary" '.$checked.'></li>';
        }
    }
    if (!$child) {
        return '<ul>'.$html.'</ul>';
    } else {
        return '<ul style="margin-left:50px;">'. $html . '<div style="clear: both;"></div></ul>';
    }


}
