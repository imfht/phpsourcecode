<?php namespace App\Common;
/**
 *  该公共类库主要LightFtp的帮助类函数
 */
class Helper {

    public static function sortContent($content, $type = 'filename-a')
    {
        foreach($content as $k => $val)
        {
            $result[$content[$k]['type']][] = $content[$k];
        }

        return $result;
    }
}