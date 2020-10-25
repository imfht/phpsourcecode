<?php

/**
 * digdata
 * @abstract 数据挖取
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Digdata
{
    /**
     * @abstract 检查数据是否匹配
     */
    public static function match($source, $regexp)
    {
        if (preg_match('!' . $regexp . '!Uis', $source)) {
            return true; //能匹配
        } else {
            return false; //不能匹配
        }
    }

    /**
     * @abstract 获取匹配的数据
     */
    public static function getMatch($source, $regexp, $haveRegexp = false)
    {
        preg_match('!' . $regexp . '!Uis', $source, $matchs);
        if (!empty($matchs)) {
            if ($haveRegexp) {
                return $matchs[0];
            } else {
                return $matchs[1];
            }
        } else {
            return false;
        }
    }

    /**
     * @abstract 列表处理
     */
    public static function matchList($source, $delimiter = '</li>', $regexp, $haveRegexp = false)
    {
        $temp = array();
        $source = explode($delimiter, $source);
        foreach ($source as $res) {
            if ($res = Digdata::getMatch($res, $regexp, $haveRegexp)) {
                $temp[] = $res;
            }
        }
        return $temp;
    }

    /**
     * @abstract 多层挖取数据
     */
    public static function deepMath($source, $regexpArray = array(), $haveRegexp = false)
    {
        $temp = array();
        foreach ($regexpArray as $parent => $son) {
            if (is_array($son)) {
                $temp[] = self::deepMath(self::getMatch($source, $parent, $haveRegexp), $son, $haveRegexp);
            } else {
                $temp = self::getMatch(self::getMatch($source, $parent, $haveRegexp), $son, $haveRegexp);
            }
        }
        return $temp;
    }

    /**
     * @abstract 抓取全部数据，返回数组
     */
    public static function getMatchAll($source, $regexp, $haveRegexp = false)
    {
        preg_match_all('!' . $regexp . '!Uis', $source, $matchs);
        if (!empty($matchs)) {
            if ($haveRegexp) {
                return $matchs[0];
            } else {
                unset($matchs[0]);
                return $matchs;
            }
        } else {
            return false;
        }
    }

    /**
     * @abstract 深度抓取全部数据，返回数组
     */
    public static function deepMatchAll($source, $regexpArray = array(), $haveRegexp = false)
    {
        if (!empty($source) && !empty($regexpArray) && is_array($regexpArray)) {
            $temp = array();
            foreach ($regexpArray as $key => $val) {
                if (is_array($val)) {
                    $temp[] = self::deepMatchAll(self::getMatchAll($source, $key, $haveRegexp), $val,
                        $haveRegexp);
                } else {
                    $temp = self::getMatchAll(self::getMatchAll($source, $key, $haveRegexp), $val, $haveRegexp);
                }
            }
            return $temp;
        }
    }
}

?>