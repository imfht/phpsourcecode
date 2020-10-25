<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-12
 * Time: 下午4:28.
 */

namespace MiotApi\Util\Jsoner;

use MiotApi\Exception\JsonException;
use MiotApi\Util\Collection\Collection;

class Jsoner extends Collection
{
    const CACHE_DIR = 'json_cache';

    const SUFFIX = '.json';

    /**
     * 读取json文件里的内容为数组.
     *
     * @param $file
     *
     * @return bool|Jsoner
     */
    public static function load($file)
    {
        try {
            $file = str_replace(':', '_', $file);
            $items = JsonLoader::fileToArray(self::getCacheDir().$file.self::SUFFIX);

            if (!empty($items) && !isset($items['error-code'])) {
                return self::make($items);
            }

            return false;
        } catch (JsonException $exception) {
            return false;
        }
    }

    /**
     * json数据存储成json文件.
     *
     * @param $data
     * @param $file
     *
     * @return bool|Jsoner
     */
    public static function fill($data, $file)
    {
        try {
            $file = str_replace(':', '_', $file);
            $items = JsonLoader::dataToFile($data, self::getCacheDir().$file.self::SUFFIX);
            if (!empty($items) && !isset($items['error-code'])) {
                return self::make($items);
            }

            return false;
        } catch (JsonException $exception) {
            return false;
        }
    }

    /**
     * 数组缓存成json文件.
     *
     * @param $array
     * @param $file
     *
     * @return bool|Jsoner
     */
    public static function fillArray($array, $file)
    {
        try {
            $file = str_replace(':', '_', $file);
            $items = JsonLoader::arrayToFile($array, self::getCacheDir().$file.self::SUFFIX);
            if (!empty($items) && !isset($items['error-code'])) {
                return self::make($items);
            }

            return false;
        } catch (JsonException $exception) {
            return false;
        }
    }

    /**
     * 取缓存文件路径.
     *
     * @return string
     */
    public static function getCacheDir()
    {
        return dirname(dirname(dirname(__DIR__))).
            DIRECTORY_SEPARATOR.
            self::CACHE_DIR.
            DIRECTORY_SEPARATOR;
    }
}
