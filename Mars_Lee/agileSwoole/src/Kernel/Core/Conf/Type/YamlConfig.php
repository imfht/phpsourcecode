<?php

namespace Kernel\Core\Conf\Type;


use Kernel\Core\Conf\IConfig;

class YamlConfig implements IConfig
{
        public function load(string $filename) : array
        {
              if(function_exists('yaml_parse')){
                      $yaml = file_get_contents($filename);
                      $config = yaml_parse($yaml);
              }else{
                      $config = spyc_load_file($filename);
              }

              return is_array($config) ? $config : [];
        }

        public function supports(string $filename) : bool
        {
                return (bool) preg_match('#\.ya?ml(\.dist)?$#', $filename);
        }
}