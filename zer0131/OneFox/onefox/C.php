<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 通用工具类
 */

namespace onefox;

class C {

    /**
     * @param $str
     * @param bool $onlyCharacterBase
     * @return string
     */
    public static function filterChars($str, $onlyCharacterBase = false) {
        $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/_-0123456789';
        if ($onlyCharacterBase) {
            $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $left = trim($str, $base);
        if ( '' === $left) {
            return $str;
        } else {
            return '';
        }
    }

    /**
     * 迭代创建目录
     * @param string $path
     * @param int $mode
     * @return bool
     */
    public static function mkDirs($path, $mode = 0777) {
        if (!is_dir($path)) {
            if (!self::mkDirs(dirname($path), $mode)) {
                return false;
            }
            if (!mkdir($path, $mode)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @param boolean $haschar
     * @return string
     */
    public static function genRandomKey($length = 10, $haschar = true) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        if ($haschar) {
            $chars .= "!@#$%^&*()-_[]{}<>~`+=,.;:/?";//包含特殊字符
        }
        $randomKey = '';
        for ($i = 0; $i < $length; $i++) {
            $randomKey .= $chars[mt_rand(1, strlen($chars) - 1)];
        }
        return $randomKey;
    }

    /**
     * 加载文件
     * @param string $filePath
     * @return mixed
     */
    public static function loadFile($filePath) {
        if (file_exists($filePath)) {
            return include $filePath;
        }
        return null;
    }

    /**
     * 生成模板页面输出用的tree
     * @param array $list 二维数组
     * @param int $pid 父级编号
     * @param int $level 层级
     * @param string $html html输出前缀
     * @return array
     */
    public static function html2Tree(array $list, $pid = 0, $level = 1, $html = ' -- ') {
        $tree = [];
        foreach ($list as $v) {
            if ($v['parent_id'] == $pid) {
                $v['sort'] = $level;
                $v['html'] = '|' . str_repeat($html, $level);
                $tree[] = $v;
                $tree = array_merge($tree, self::html2Tree($list, $v['id'], $level + 1, $html));
            }
        }
        return $tree;
    }

    /**
     * 二维数组转化为树形列表
     * @param array $data
     * @return array
     */
    public static function data2Tree(array $data) {
        $items = [];
        foreach ($data as $val) {
            $items[$val['id']] = $val;
        }
        unset($data);
        $tree = [];
        foreach ($items as $item) {
            if (isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['son'][] = &$items[$item['id']];
            } else {
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }

    /**
     * 计算两个时间戳的时间差
     * @param int $begin 开始时间戳
     * @param int $end 结束时间戳
     * @param boolean $returnStr 是否返回字符串
     * @return array|string
     */
    public static function timeDiff($begin, $end, $returnStr = true) {
        if ($begin < $end) {
            $starttime = $begin;
            $endtime = $end;
        } else {
            $starttime = $end;
            $endtime = $begin;
        }
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $daysStr = $days ? $days . '天' : '';
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $hoursStr = $hours ? $hours . '小时' : '';
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $minsStr = $mins ? $mins . '分钟' : '';
        $secs = $remain % 60;
        $secsStr = $secs ? $secs . '秒' : '';
        if ($returnStr) {
            return $daysStr . $hoursStr . $minsStr . $secsStr;
        }
        return [
            "day" => $days,
            "hour" => $hours,
            "min" => $mins,
            "sec" => $secs
        ];
    }


    /**
     * 根据参数获得签名
     * @param array $p
     * @param string $signKey
     * @return string
     */
    public static function sign(array $p, $signKey = '2#!&70op#e') {
        $signStr = '';
        unset($p['sign']);
        if (is_array($p) && !empty($p) && ksort($p)) {
            foreach ($p as $k => $v) {
                if ($v !== '') {
                    $signStr .= "{$k}={$v}&";
                }
            }
        }
        $signStr = rtrim($signStr, '&');
        return md5($signStr . $signKey);
    }

    /**
     * 创建类
     * @param $className
     * @return mixed|null
     */
    /*public static function newClass($className) {
        if (!$className) {
            return null;
        }
        if (!isset(self::$_classObj[$className]) || !self::$_classObj[$className]) {
            if (!class_exists($className)) {
                return null;
            }
            self::$_classObj[$className] = new $className;
        }
        return self::$_classObj[$className];
    }*/

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public static function xmlEncode($data, $root = 'onefox', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
        if (is_array($attr)) {
            $_attr = [];
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    public static function data2Xml($data, $item='item', $id='id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? self::data2Xml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    /**
     * 数据库查询结果排序
     * 使用方法: C::sortDbRet($data, ['column_name'=>SORT_ASC]);
     * @param $data
     * @param $columns
     * @return mixed
     */
    public static function sortDbRet($data, $columns) {
        $args = [];
        foreach ($columns as $k => $v) {
            $args[] = array_column($data, $k);
            $args[] = $v;
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * 导出csv文件
     * @param array $header
     * @param array $data
     * @param string $fileName
     * @param bool $isWin
     */
    public static function exportToCSV(array $header, array $data, $fileName, $isWin = true) {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $fileName = $fileName . '-' . date('YmdHis') . '.csv';
        if ($isWin) {
            header("Content-Type: application/vnd.ms-excel; charset=GB2312");
        } else {
            header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        }
        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        //输出头处理
        foreach ($header as $k => $v) {
            $header[$k] = $isWin ? iconv('utf-8', 'gb2312', $v) : $v;
        }
        fputcsv($fp, $header);
        //计数器
        $cnt = 0;
        //buffer刷新行数
        $limit = 500;
        foreach ($data as $key => $val) {
            $cnt++;
            if ($cnt == $limit) {
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = [];
            foreach ($val as $i => $v) {
                $row[] = $isWin ? iconv('utf-8', 'gb2312', $v) : $v;
            }
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /**
     * 执行事务，$func为要执行的函数，失败返回false
     * @param callable $func
     * @param DB $db
     * @param array $args
     * @return bool
     */
    final public static function withTransaction(callable $func, DB $db, $args = []) {
        if ($db->beginTransaction() === false) {
            Log::warning('Start transaction failed');
            return false;
        }
        try {
            $ret = call_user_func_array($func, $args);
            if ($ret === false) {
                throw new \Exception('Transaction procedure return false');
            }
            if ($db->executeTransaction() === false) {
                throw new \Exception('Commit transaction failed');
            }
            return true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::warning("Rollback transaction {$message}");
            $db->rollBack();
            return false;
        }
    }
}

