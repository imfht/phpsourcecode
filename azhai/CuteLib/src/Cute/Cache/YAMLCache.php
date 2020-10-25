<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;


if (extension_loaded('yaml')) {
    function yaml_dump($data)
    {
        return yaml_emit($data, YAML_UTF8_ENCODING, YAML_LN_BREAK);
    }
} else {
    \app()->importStrip('\\Symfony\\Component\\Yaml', VENDOR_ROOT . '/yaml');
    
    function yaml_dump($data)
    {
        return \Symfony\Component\Yaml\Yaml::dump($data);
    }
    
    function yaml_parse($data)
    {
        return \Symfony\Component\Yaml\Yaml::parse($data);
    }
}


/**
 * YAML文件缓存
 */
class YAMLCache extends FileCache
{

    protected $ext = '.yml';

    protected function readFile()
    {
        $data = file_get_contents($this->filename);
        return yaml_parse($data);
    }

    protected function writeFile($data, $timeout = 0)
    {
        $data = yaml_dump($data);
        $bytes = file_put_contents($this->filename, $data, LOCK_EX);
        return $bytes && $bytes > 0;
    }

}
