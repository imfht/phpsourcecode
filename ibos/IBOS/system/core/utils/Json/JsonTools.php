<?php


namespace application\core\utils\Json;


class JsonTools
{
    protected static $_messages = array(
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );

    /**
     * The option parameter is optional and it is bitmask consisting of
     * JSON_HEX_QUOT,
     * JSON_HEX_TAG,
     * JSON_HEX_AMP,
     * JSON_HEX_APOS,
     * JSON_NUMERIC_CHECK,
     * JSON_PRETTY_PRINT,
     * JSON_UNESCAPED_SLASHES,
     * JSON_FORCE_OBJECT,
     * JSON_UNESCAPED_UNICODE. The behaviour of these constants is described on the JSON constants page.
     *
     * @param mixed $value
     * @param int $options
     * @return string
     * @throws JsonException
     */
    public static function encode($value, $options = 0)
    {
        // JSON_UNESCAPED_UNICODE workaround for PHP 5.3
        $result = preg_replace_callback(
            '/\\\\u([0-9a-zA-Z]{4})/',
            function ($matches) {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UTF-16');
            },
            json_encode($value, $options)
        );

        if ($result) {
            return $result;
        }

        throw new JsonException(self::$_messages[json_last_error()], json_last_error());
    }

    /**
     * @param $json
     * @param bool $assoc
     * @return mixed
     * @throws JsonException
     */
    public static function decode($json, $assoc = false)
    {
        $result = json_decode($json, $assoc);

        if ($result) {
            return $result;
        }

        throw new JsonException(self::$_messages[json_last_error()], json_last_error());
    }

    public static function beginObject()
    {
        return '{';
    }

    public static function endObject()
    {
        return '}';
    }

    public static function beginArray()
    {
        return '[';
    }

    public static function endArray()
    {
        return ']';
    }

    /**
     * Returns "key": value pair, using json_encode to encode the value
     *
     * @param int|string $key
     * @param mixed $value
     * @param int $options
     * @return string
     * @throws JsonException
     */
    public static function value($key, $value, $options = 0)
    {
        $data = array();
        $data[$key] = $value;
        return trim(self::encode($data, $options), '{}');
    }

    /**
     * Returns "key": value pair
     * The value is used raw, without any encoding or sanitizing
     *
     * @param $key
     * @param $value
     * @return string
     */
    public static function rawValue($key, $value)
    {
        return sprintf('"%s":%s', $key, $value);
    }

    public static function delimiter()
    {
        return ", ";
    }

    public static function indent($size = 4)
    {
        return str_repeat(" ", $size);
    }

    public static function newLine()
    {
        return "\n";
    }

    /**
     * Pretty-print JSON string
     * The code is based on
     *
     * Use 'format' option to select output format - currently html and txt supported, txt is default
     * Use 'indent' option to override the indentation string set in the format - by default for the 'txt' format it's a tab
     * Use 'offset' option to add initial indentation to every single line
     *
     * @param string $json Original JSON string
     * @param array $options Encoding options
     * @return string
     */
    public static function prettyPrint($json, $options = array())
    {
        $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        $indent = 0;

        $format = 'txt';
        
        if (isset($options['format'])) {
            $format = $options['format'];
        }

        switch ($format) {
            case 'html':
                $lineBreak = '<br />';
                $ind = '&nbsp;&nbsp;&nbsp;&nbsp;';
                break;
            default:
            case 'txt':
                $lineBreak = "\n";
                $ind = "\t";
                break;
        }

        // override the defined indent setting with the supplied option
        if (isset($options['indent'])) {
            $ind = $options['indent'];
        }

        $inLiteral = false;
        foreach ($tokens as $token) {
            if ($token == '') {
                continue;
            }

            $prefix = str_repeat($ind, $indent);
            if (!$inLiteral && ($token == '{' || $token == '[')) {
                $indent++;
                if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
                    $result .= $prefix;
                }
                $result .= $token . $lineBreak;
            } elseif (!$inLiteral && ($token == '}' || $token == ']')) {
                $indent--;
                $prefix = str_repeat($ind, $indent);
                $result .= $lineBreak . $prefix . $token;
            } elseif (!$inLiteral && $token == ',') {
                $result .= $token . $lineBreak;
            } else {
                $result .= ($inLiteral ? '' : $prefix) . $token;

                // Count # of unescaped double-quotes in token, subtract # of
                // escaped double-quotes and if the result is odd then we are
                // inside a string literal
                if ((substr_count($token, "\"") - substr_count($token, "\\\"")) % 2 != 0) {
                    $inLiteral = !$inLiteral;
                }
            }
        }

        if (isset($options['offset'])) {
            $result = str_replace("\n", "\n" . $options['offset'], $result);
        }

        return $result;
    }
}