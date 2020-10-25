<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-12
 * Time: 下午4:08.
 */

namespace MiotApi\Util\Jsoner;

use MiotApi\Exception\JsonException;

class JsonLoader
{
    /**
     * Use redis or file ?
     */
    const USE_REDIS = true;

    /**
     * Creating JSON file from data.
     *
     * @param string $data → JSON data
     * @param string $file → path to the file
     *
     * @throws JsonException
     *
     * @return array
     */
    public static function dataToFile($data, $file)
    {
        $array = json_decode($data, true);

        return self::arrayToFile($array, $file);
    }

    /**
     * Creating JSON file from array.
     *
     * @param array  $array → array to be converted to JSON
     * @param string $file  → path to the file
     *
     * @throws JsonException
     *
     * @return array
     */
    public static function arrayToFile($array, $file)
    {
        $lastError = JsonLastError::check();
        $json = json_encode($lastError ? $lastError : $array, JSON_PRETTY_PRINT);
        self::saveFile($file, $json);
        if (is_null($lastError)) {
            return $array;
        } else {
            throw new JsonException($lastError['message'].' '.$file);
        }
    }

    /**
     * Create directory recursively if it doesn't exist.
     *
     *
     * @param string $file → path to the directory
     *
     * @throws JsonException → couldn't create directory
     */
    private static function createDirectory($file)
    {
        $basename = is_string($file) ? basename($file) : '';
        $path = str_replace($basename, '', $file);
        if (!empty($path) && !is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                $message = 'Could not create directory in';

                throw new JsonException($message.' '.$path);
            }
        }
    }

    /**
     * Save file.
     *
     *
     * @param string $file → path to the file
     * @param string $json → json string
     *
     * @throws JsonException → couldn't create file
     */
    private static function saveFile($file, $json)
    {
        if (self::putContent($file, $json) === false) {
            $message = 'Could not create file in';

            throw new JsonException($message.' '.$file);
        }
    }

    /**
     * Save to array the JSON file content.
     *
     * @param string $file → path or external url to JSON file
     *
     * @throws JsonException
     *
     * @return array|false
     */
    public static function fileToArray($file)
    {
        $json = self::getContent($file);
        $array = json_decode($json, true);
        $lastError = JsonLastError::check();

        return $array === null || !is_null($lastError) ? false : $array;
    }

    private static function putContent($file, $json)
    {
        if (self::USE_REDIS) {
            if (\Redis::ping()) {
                $file = str_replace(Jsoner::getCacheDir(), 'miot_json_cache:', $file);
                $file = str_replace(['/', '\\'], ':', $file);

                return \Redis::set($file, $json);
            } else {
                self::createDirectory($file);

                return file_put_contents($file, $json);
            }
        }
    }

    private static function getContent($file)
    {
        if (self::USE_REDIS) {
            if (\Redis::ping()) {
                $file = str_replace(Jsoner::getCacheDir(), 'miot_json_cache:', $file);
                $file = str_replace(['/', '\\'], ':', $file);

                return \Redis::get($file);
            } else {
                if (!is_file($file) && !filter_var($file, FILTER_VALIDATE_URL)) {
                    self::arrayToFile([], $file);
                }

                return file_get_contents($file);
            }
        }
    }
}
