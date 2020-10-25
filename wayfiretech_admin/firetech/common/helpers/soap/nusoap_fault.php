<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-08-05 15:55:43
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-05 16:07:32
 */

namespace common\helpers\soap;


 /**
 * Contains information for a SOAP fault.
 * Mainly used for returning faults from deployed functions
 * in a server instance.
 *
 * @author   Dietrich Ayala <dietrich@ganx4.com>
 * @version  $Id: nusoap.php,v 1.123 2010/04/26 20:15:08 snichol Exp $
 * @access public
 */
class nusoap_fault extends nusoap_base
{
    /**
     * The fault code (client|server)
     *
     * @var string
     * @access private
     */
    var $faultcode;
    /**
     * The fault actor
     *
     * @var string
     * @access private
     */
    var $faultactor;
    /**
     * The fault string, a description of the fault
     *
     * @var string
     * @access private
     */
    var $faultstring;
    /**
     * The fault detail, typically a string or array of string
     *
     * @var mixed
     * @access private
     */
    var $faultdetail;

    /**
     * constructor
     *
     * @param string $faultcode (SOAP-ENV:Client | SOAP-ENV:Server)
     * @param string $faultactor only used when msg routed between multiple actors
     * @param string $faultstring human readable error message
     * @param mixed $faultdetail detail, typically a string or array of string
     */
    function __construct($faultcode, $faultactor = '', $faultstring = '', $faultdetail = '')
    {
        parent::__construct();
        $this->faultcode = $faultcode;
        $this->faultactor = $faultactor;
        $this->faultstring = $faultstring;
        $this->faultdetail = $faultdetail;
    }

    /**
     * serialize a fault
     *
     * @return    string    The serialization of the fault instance.
     * @access   public
     */
    function serialize()
    {
        $ns_string = '';
        foreach ($this->namespaces as $k => $v) {
            $ns_string .= "\n  xmlns:$k=\"$v\"";
        }
        $return_msg =
            '<?xml version="1.0" encoding="' . $this->soap_defencoding . '"?>' .
            '<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' . $ns_string . ">\n" .
            '<SOAP-ENV:Body>' .
            '<SOAP-ENV:Fault>' .
            $this->serialize_val($this->faultcode, 'faultcode') .
            $this->serialize_val($this->faultactor, 'faultactor') .
            $this->serialize_val($this->faultstring, 'faultstring') .
            $this->serialize_val($this->faultdetail, 'detail') .
            '</SOAP-ENV:Fault>' .
            '</SOAP-ENV:Body>' .
            '</SOAP-ENV:Envelope>';
        return $return_msg;
    }
}
