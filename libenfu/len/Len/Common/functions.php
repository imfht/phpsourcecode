<?php

function debug()
{
    echo "<meta charset='UTF-8'><pre style='padding:20px; background: #000000; color: #FFFFFF;'>\r\n";
    if (func_num_args()) {
        foreach (func_get_args() as $k => $v) {
            echo "------- Debug $k -------<br/>\r\n";
            print_r($v);
            echo "<br/>\r\n";
        }
    }
    echo '</pre>';
    exit;
}

function milliSecond()
{
    list($s1, $s2) = explode(' ', microtime());

    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}

function dump($var, $echo = true, $label = null, $strict = true)
{
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo $output;
        return null;
    }

    return $output;
}

function exportCSV($data = array(), $filePath = '', $headerField = false, $fileType = 'csv')
{
    if (file_exists($filePath)) {
        return false;
    }
    // 导出类型
    $split = ",";
    if ($fileType == 'xls') {
        $split = "\t";
    }
    // 表头
    if ($headerField) {
        $first = reset($data);
        $output = implode($split, array_keys($first));
        $output .= "\r\n";
    } else {
        $output = '';
    }
    // 分割写入
    $skip = 1000;
    $max = count($data);
    $fp = fopen($filePath, "a+");
    foreach ($data as $key => $value) {
        $output .= implode($split, array_values($value));
        $output .= "\r\n";
        if ((($key != 0) && ($key % $skip == 0)) || ($max == $key + 1)) {
            fwrite($fp, $output);
            $output = '';
        }
    }
    fclose($fp);
}

function xmlToArray($xml)
{
    return \Xml\XML2Array::createArray($xml);
}

function arrayToXml($array, $node_name = 'xml')
{
    if (empty($array)) {
        return '';
    }

    return \Xml\Array2XML::createXML($node_name, $array)->saveXML();
}