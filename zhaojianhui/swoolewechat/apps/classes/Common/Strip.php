<?php

namespace App\Common;
/**
 * 数据过滤类
 * @package App\Common
 */
class Strip
{
    /**
     * 转义字符串内容
     * @param $data
     * @return array|string
     */
    public function setStrip($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->setStrip($v);
            }
        } elseif (is_string($data)) {
            $data = addslashes(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
        }
        return $data;
    }
    /**
     * 反转义字符串内容
     * @param $data
     * @return array|string
     */
    public function unsetStrip($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->unsetStrip($v);
            }
        } elseif (is_string($data)) {
            $data = htmlspecialchars_decode(stripslashes($data));
        }

        return $data;
    }

    /**
     * 转义数据中的字符.
     *
     * @param $data
     *
     * @return array|string
     */
    public function setAddslashes($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->setAddslashes($v);
            }
        } else {
            $data = addslashes($data);
        }

        return $data;
    }

    /**
     * 去除转义标签.
     *
     * @param $data
     *
     * @return array|string
     */
    public function unsetAddslashes($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->unsetAddslashes($v);
            }
        } else {
            $data = stripcslashes($data);
        }

        return $data;
    }

    /**
     * 将特殊字符转换为 HTML 实体
     * @param $data
     * @return array|string
     */
    public function setHtmlspecialchars($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->setHtmlspecialchars($v);
            }
        } elseif (is_string($data)) {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }
    /**
     * 将特殊的 HTML 实体转换回普通字符
     * @param $data
     * @return array|string
     */
    public function unsetHtmlspecialchars($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->unsetHtmlspecialchars($v);
            }
        } elseif (is_string($data)) {
            $data = htmlspecialchars_decode($data);
        }

        return $data;
    }

    /**
     * 从字符串中去除 HTML 和 PHP 标记
     * @param $data
     * @return array|string
     */
    public function setStripTags($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->setStripTags($v);
            }
        } elseif (is_string($data)) {
            $data = strip_tags($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

    /**
     * 在字符串所有新行之前插入 HTML 换行标记
     * @param $data
     * @return array|string
     */
    public function setNl2br($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->setNl2br($v);
            }
        } elseif (is_string($data)) {
            $data = nl2br($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }
}
