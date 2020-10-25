<?php

namespace fluiex\util;

use fluiex\util\xml\Parser;

class XML
{

    public static function toArray(&$xml, $isnormal = FALSE)
    {
        $xml_parser = new Parser($isnormal);
        $data = $xml_parser->parse($xml);
        $xml_parser->destruct();
        return $data;
    }

    public static function toString($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1)
    {
        $s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
        $space = str_repeat("\t", $level);
        foreach ($arr as $k => $v) {
            if (!is_array($v)) {
                $s .= $space . "<item id=\"$k\">" . ($htmlon ? '<![CDATA[' : '') . $v . ($htmlon ? ']]>' : '') . "</item>\r\n";
            } else {
                $s .= $space . "<item id=\"$k\">\r\n" . array2xml($v, $htmlon, $isnormal, $level + 1) . $space . "</item>\r\n";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</root>" : $s;
    }

}
