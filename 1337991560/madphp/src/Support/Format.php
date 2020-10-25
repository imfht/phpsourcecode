<?php

/**
 * Format
 * Help convert between various formats such as XML, JSON, CSV, etc.
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Support;

class Format
{
    // Array to convert
    protected $data = array();

    /**
     * 生成格式化类实例
     *
     *     echo $this->format->factory(array('foo' => 'bar'))->toXml();
     *
     * @param   mixed   要被格式化的数据
     * @param   string  数据的格式
     * @return  object
     */
    public static function factory($data = null, $fromType = null)
    {
        $class = __CLASS__;
        return new $class($data, $fromType);
    }

    /**
     * 避免直接实例化，使用 factory() 获取实例
     */
    private function __construct($data = null, $fromType = null)
    {
        // 如果提供已经格式化的数据，需要将数据转换为数组
        if ($fromType !== null) {
            if (method_exists($this, 'from' . ucfirst($fromType))) {
                $data = call_user_func(array($this, 'from' . ucfirst($fromType)), $data);
            } else {
                throw new \Exception('Format class does not support conversion from "' . $fromType . '".');
            }
        }

        $this->data = $data;
    }

    /**
     * 格式化为数组
     */
    public function toArray()
    {
        $data = $this->data;
        $array = array();
        foreach ((array) $data as $key => $value) {
            if (is_object($value) or is_array($value)) {
                $array[$key] = $this->toArray($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * 格式化为XML数据
     */
    public function toXml($structure = null, $basenode = 'xml')
    {
        $data = $this->data;
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }
        if ($structure === null) {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }
        // Force it to be something useful
        if (!is_array($data) AND !is_object($data)) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            // 转换布尔型为数值型 false/true to 0/1
            if (is_bool($value)) {
                $value = (int) $value;
            }
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                $key = (singular($basenode) != $basenode) ? singular($basenode) : 'item';
            }
            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

            if ($key === '_attributes' && (is_array($value) || is_object($value))) {
                $attributes = $value;
                if (is_object($attributes)) $attributes = get_object_vars($attributes);
                foreach ($attributes as $attributeName => $attributeValue) {
                    $structure->addAttribute($attributeName, $attributeValue);
                }
            // if there is another array found recursively call this function
            } elseif (is_array($value) || is_object($value)) {
                $node = $structure->addChild($key);
                // 递归
                $this->toXml($value, $node, $key);
            } else {
                // add single node.
                $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");
                $structure->addChild($key, $value);
            }
        }

        return $structure->asXML();
    }

    /**
     * 格式化为CSV
     */
    public function toCsv()
    {
        $data = $this->data;
        $data = (array) $data;

        // Multi-dimensional array
        if (isset($data[0]) && is_array($data[0])) {
            $headings = array_keys($data[0]);
            exit('d');
        // Single array
        } else {
            $headings = array_keys($data);
            $data = array($data);
        }

        $output = '"'.implode('","', $headings).'"'.PHP_EOL;
        foreach ($data as &$row) {
            // if (is_array($row)) {
            //     throw new Exception('Format class does not support multi-dimensional arrays');
            // } else {
                // Escape dbl quotes per RFC 4180
                $row    = str_replace('"', '""', $row);
                $output .= '"'.implode('","', $row).'"'.PHP_EOL;
            // }
        }

        return $output;
    }

    /**
     * 格式化为JSON
     */
    public function toJson($option = 0)
    {
        $data = $this->data;
        $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
        if ($callback === '') {
            return json_encode($data, $option);
            
            // Had to take out this code, it doesn't work on Objects.
            // $str = $data;
            // array_walk_recursive($str, function(&$item, $key) {
            //  if(!mb_detect_encoding($item, 'utf-8', true)) {
            //      $item = utf8_encode($item);
            //  }
            // });
            // return json_encode($str);
            
        // we only honour jsonp callback which are valid javascript identifiers
        } elseif (preg_match('/^[a-z_\$][a-z0-9\$_]*(\.[a-z_\$][a-z0-9\$_]*)*$/i', $callback)) {
            // this is a jsonp request, the content-type must be updated to be text/javascript
            header("Content-Type: application/javascript");
            return $callback . "(" . json_encode($data, $option) . ");";
        } else {
            // we have an invalid jsonp callback identifier, we'll return plain json with a warning field
            $this->data['warning'] = "invalid jsonp callback provided: ".$callback;
            return json_encode($data, $option);
        }
    }

    /**
     * 序列化数据
     */
    public function toSerialize()
    {
        $data = $this->data;
        return serialize($data);
    }

    /**
     * 返回变量的字符串表示
     */
    public function toPhp()
    {
        $data = $this->data;
        return var_export($data, TRUE);
    }

    /**
     * Xml数据转换为数组
     */
    public function fromXml($string)
    {
        return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    // Format CSV for output
    // This function is DODGY! Not perfect CSV support but works with my REST_Controller
    public function fromCsv($string)
    {
        $data = array();

        // Splits
        $rows = explode("\n", trim($string));
        $headings = explode(',', array_shift($rows));
        foreach ($rows as $row) {
            // The substr removes " from start and end
            $data_fields = explode('","', trim(substr($row, 1, -1)));

            if (count($data_fields) == count($headings)) {
                $data[] = array_combine($headings, $data_fields);
            }
        }

        return $data;
    }

    /**
     * JSON 格式的字符串转换为 PHP 变量
     */
    public function fromJson($string)
    {
        return json_decode(trim($string));
    }

    /**
     * 反序列化数据
     */
    public function fromSerialize($string)
    {
        return unserialize(trim($string));
    }

}

/* End of file Format.php */
