<?php

namespace Output;

class Xml implements OutputBase
{
    /**
     * @param array $data
     * @param string $node_name
     */
    public static function output($data = array(), $node_name = 'xml')
    {
        header("Content-type:text/xml");

        $xml = \Xml\Array2XML::createXML($node_name, $data)->saveXML();

        exit($xml);
    }
}