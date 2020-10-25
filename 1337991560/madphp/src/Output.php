<?php

/**
 * Output
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Support\Format;

class Output
{
    public $data;
    public $args;

    public function __construct($data, $args = array())
    {
        $this->data = $data;
        $this->args = $args;
    }

    /**
     * 创建输出实例
     * @param $data
     * @param array $args
     * @return mixed
     */
    public static function getInstance($data, $args = array())
    {
        $class = __CLASS__;
        return new $class($data, $args);
    }

    /**
     * 输出 json 数据
     */
    public static function json($data, $option = 0, $return = false, $fromType = null)
    {
        if (is_resource($data)) {
            throw new \UnexpectedValueException("Output::json can not recieve resource!");
        } else {
            $output = self::getInstance($data);
            $outputData = Format::factory($output->data, $fromType)->toJson($option);
            if (!$return) {
                echo $outputData;
            }
            return $outputData;
        }
    }
    
    /**
     * 输出 xml 数据
     */
    public static function xml($data, $structure = null, $basenode = 'xml', $return = false, $fromType = null)
    {
        $output = self::getInstance($data);

        if ($structure !== null && !$structure) {
            $structure = null;
        }
        if (!$basenode) {
            $basenode = 'xml';
        }

        $xml_output = Format::factory($output->data, $fromType)->toXml($structure, $basenode);

        if (!$return) {
            header('Content-Type: text/xml');
            echo $xml_output;
        }
        return $xml_output;
    }

    /**
     * 输出 serialize 数据
     */
    public static function serialize($data, $return = false, $fromType = null)
    {
        if (is_resource($data)) {
            throw new \UnexpectedValueException("Output::serialize can not recieve resource!");
        } else {
            $output = self::getInstance($data);
            $outputData = Format::factory($output->data, $fromType)->toSerialize();
            if (!$return) {
                echo $outputData;
            }
            return $outputData;
        }
    }

    /**
     * 输出变量的字符串表示
     */
    public static function php($data, $return = false, $fromType = null)
    {
        $output = self::getInstance($data);
        $outputData = Format::factory($output->data, $fromType)->toPhp();
        if (!$return) {
            echo $outputData;
        }
        return $outputData;
    }

    /**
     * 输出数组
     */
    public static function asArray($data, $return = false, $fromType = null)
    {
        $output = self::getInstance($data);
        $outputData = Format::factory($output->data, $fromType)->toArray();
        if (!$return) {
            pp($outputData, false);
        }
        return $outputData;
    }

    /**
     * 输出csv
     */
    public static function csv($data, $fileName, $return = false, $fromType = null)
    {
        $output = self::getInstance($data);
        $outputData = Format::factory($output->data, $fromType)->toCsv();
        if (!$return) {
            header("Content-type:application/vnd.ms-excel");
            header("content-Disposition:filename={$fileName}");
            echo $outputData;
        }
        return $outputData;
    }
}