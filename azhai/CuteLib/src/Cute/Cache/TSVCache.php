<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

/**
 * CSV/TSV文件缓存
 */
class TSVCache extends FileCache
{

    protected $delimiter = "\t"; //列分隔符
    protected $ext = '.csv';

    public function __construct($name, $dir = false, $delimiter = '', $at_least = 0)
    {
        parent::__construct($name, $dir);
        if (!empty($delimiter)) {
            $this->delimiter = $delimiter;
        }
        $this->at_least = $at_least;
    }

    /**
     * @param int $at_least 最少列数
     * @return array 行列二维数组
     */
    protected function readFile()
    {
        $fh = fopen($this->filename, 'rb');
        if ($fh === false) {
            return [];
        }
        $data = [];
        do {
            $line = fgetcsv($fh, 0, $this->delimiter);
            if (is_null($line) || $line === false) {
                break; //无效的文件指针返回NULL，碰到文件结束时返回FALSE
            }
            if (is_null($line[0])) {
                $line = []; //空行将被返回为一个包含有单个 null 字段的数组
            }
            if ($this->at_least > 0 && count($line) < $this->at_least) {
                continue; //列数不足
            }
            $data[] = $line;
        } while (1);
        fclose($fh);
        return $data;
    }

    protected function writeFile($data, $timeout = 0)
    {
        $fh = fopen($this->filename, 'wb');
        if ($fh === false) {
            return 0;
        }
        $size = 0;
        foreach ($data as $row) {
            $size += fputcsv($fh, $row, $this->delimiter);
        }
        fclose($fh);
        return $size;
    }

}
