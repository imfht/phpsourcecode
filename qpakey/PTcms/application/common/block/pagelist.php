<?php

class PagelistBlock extends PT_Base {

    public function exec($param) {
        $param['section'] = empty($param['section']) ? 4 : $param['section'];
        $param['totalnum'] = empty($param['totalnum']) ? 0 : $param['totalnum'];
        $param['pagesize'] = empty($param['pagesize']) ? 10 : $param['pagesize'];
        $param['pagenum'] = ceil($param['totalnum'] / $param['pagesize']);
        $param['maxpage'] = empty($param['maxpage']) ? $param['pagenum'] : $param['maxpage'];
        $param['minpage'] = empty($param['minpage']) ? 1 : $param['minpage'];
        $param['page'] = empty($param['page']) ? min($param['maxpage'], max($param['minpage'], I('get.page', 'int', 1))) : $param['page'];

        if ($param['page'] == $param['minpage']) {
            $list['first'] = array('num' => 1, 'status' => 1);
            $list['prev'] = array('num' => 1, 'status' => 1);
        } else {
            $list['first'] = array('num' => 1, 'status' => 0);
            $list['prev'] = array('num' => $param['page'] - 1, 'status' => 0);
        }
        if ($param['page'] == $param['maxpage']) {
            $list['last'] = array('num' => $param['maxpage'], 'status' => 1);
            $list['next'] = array('num' => $param['maxpage'], 'status' => 1);
        } else {
            $list['last'] = array('num' => $param['maxpage'], 'status' => 0);
            $list['next'] = array('num' => $param['page'] + 1, 'status' => 0);
        }
        $start = $param['page'] - $param['section'];
        if ($start >= $param['minpage']) {
            $end = $param['page'] + $param['section'];
            if ($end > $param['maxpage']) {
                $end = $param['maxpage'];
                $start = $param['maxpage'] - 2 * $param['section'];
                $start = ($start < $param['minpage']) ? $param['minpage'] : $start;
            }
        } else {
            $start = $param['minpage'];
            $end = $param['minpage'] + 2 * $param['section'];
            $end = ($end > $param['maxpage']) ? $param['maxpage'] : $end;
        }
        for ($i = $start; $i <= $end; $i++) {
            $list['num'][] = array(
                'num' => $i,
                'status' => ($i == $param['page']) ? 1 : 0,
            );
        }
        $list['totalnum'] = $param['totalnum'];
        $list['page'] = $param['page'];
        $list['pagenum'] = $param['pagenum'];
        $list['pagesize'] = $param['pagesize'];
        return $list;
    }
}