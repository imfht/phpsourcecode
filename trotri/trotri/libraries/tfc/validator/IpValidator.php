<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\validator;

/**
 * IpValidator class file
 * 验证IP地址
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: IpValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class IpValidator extends Validator
{
    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" does not appear to be a valid IP address.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return ($this->isIPv4($this->getValue()) == $this->getOption());
    }

    /**
     * 验证字符串是否是IPv4格式
     * @param string $value
     * @return boolean
     */
    public function isIPv4($value)
    {
        // binary format  00000000.00000000.00000000.00000000
        if (preg_match('/^([01]{8}.){3}[01]{8}$/i', $value)) {
            $value = bindec(substr($value, 0, 8))
                   . '.' . bindec(substr($value, 9, 8))
                   . '.' . bindec(substr($value, 18, 8))
                   . '.' . bindec(substr($value, 27, 8));
        }
        // octet format 777.777.777.777
        elseif (preg_match('/^([0-9]{3}.){3}[0-9]{3}$/i', $value)) {
            $value = (int) substr($value, 0, 3)
                   . '.' . (int) substr($value, 4, 3)
                   . '.' . (int) substr($value, 8, 3)
                   . '.' . (int) substr($value, 12, 3);
        }
        // hex format ff.ff.ff.ff
        elseif (preg_match('/^([0-9a-f]{2}.){3}[0-9a-f]{2}$/i', $value)) {
            $value = hexdec(substr($value, 0, 2))
                   . '.' . hexdec(substr($value, 3, 2))
                   . '.' . hexdec(substr($value, 6, 2))
                   . '.' . hexdec(substr($value, 9, 2));
        }

        $ip2long = ip2long($value);
        if ($ip2long === false) {
            return false;
        }

        return ($value == long2ip($ip2long));
    }

    /**
     * 验证字符串是否是IPv6格式
     * @param string $value
     * @return boolean
     */
    public function isIPv6($value)
    {
        if (strlen($value) < 3) {
            return $value == '::';
        }

        if (strpos($value, '.')) {
            $lastcolon = strrpos($value, ':');
            if (!($lastcolon && $this->isIPv4(substr($value, $lastcolon + 1)))) {
                return false;
            }

            $value = substr($value, 0, $lastcolon) . ':0:0';
        }

        if (strpos($value, '::') === false) {
            return preg_match('/\A(?:[a-f0-9]{1,4}:){7}[a-f0-9]{1,4}\z/i', $value);
        }

        $colonCount = substr_count($value, ':');
        if ($colonCount < 8) {
            return preg_match('/\A(?::|(?:[a-f0-9]{1,4}:)+):(?:(?:[a-f0-9]{1,4}:)*[a-f0-9]{1,4})?\z/i', $value);
        }

        // special case with ending or starting double colon
        if ($colonCount == 8) {
            return preg_match('/\A(?:::)?(?:[a-f0-9]{1,4}:){6}[a-f0-9]{1,4}(?:::)?\z/i', $value);
        }

        return false;
    }
}
