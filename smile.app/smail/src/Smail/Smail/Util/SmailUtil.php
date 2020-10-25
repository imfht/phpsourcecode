<?php
namespace Smail\Util;

class SmailUtil
{

    function smail_is8bit($string, $charset = 'utf-8')
    {
        
        /**
         * Don't use \240 in ranges.
         * Sometimes RH 7.2 doesn't like it.
         * Don't use \200-\237 for iso-8859-x charsets. This ranges
         * stores control symbols in those charsets.
         * Use preg_match instead of ereg in order to avoid problems
         * with mbstring overloading
         */
        if (preg_match("/^iso-8859/i", $charset)) {
            $needle = '/\240|[\241-\377]/';
        } else {
            $needle = '/[\200-\237]|\240|[\241-\377]/';
        }
        return preg_match("$needle", $string);
    }

    function smail_quoteimap($str)
    {
        // FIXME use this performance improvement (not changing because this is STABLE branch): return str_replace(array('\\', '"'), array('\\\\', '\\"'), $str);
        return preg_replace("/([\"\\\\])/", "\\\\$1", $str);
    }

    /**
     * Parses a string in an imap response.
     * String starts with " or { which means it
     * can handle double quoted strings and literal strings
     *
     * @param
     *            $read
     * @param
     *            $i
     */
    function parseString($read, &$i)
    {
        $char = $read{$i};
        $s = '';
        if ($char == '"') {
            $iPos = ++ $i;
            while (true) {
                $iPos = strpos($read, '"', $iPos);
                if (! $iPos)
                    break;
                if ($iPos && $read{$iPos - 1} != '\\') {
                    $s = substr($read, $i, ($iPos - $i));
                    $i = $iPos;
                    break;
                }
                $iPos ++;
                if ($iPos > strlen($read)) {
                    break;
                }
            }
        } else 
            if ($char == '{') {
                $lit_cnt = '';
                ++ $i;
                $iPos = strpos($read, '}', $i);
                if ($iPos) {
                    $lit_cnt = substr($read, $i, $iPos - $i);
                    $i += strlen($lit_cnt) + 3; /* skip } + \r + \n */
                    /* Now read the literal */
                    $s = ($lit_cnt ? substr($read, $i, $lit_cnt) : '');
                    $i += $lit_cnt;
                    /*
                     * temp bugfix (SM 1.5 will have a working clean version)
                     * too much work to implement that version right now
                     */
                    -- $i;
                } else { /* should never happen */
                    $i += 3; /* } + \r + \n */
                    $s = '';
                }
            } else {
                return false;
            }
        ++ $i;
        return $s;
    }

    /**
     * Parses an address string.
     * FIXME: the original author should step up and document this - the following is a guess based on a couple simple tests of *using* the function, not knowing the code inside
     *
     * @param string $address
     *            Generic email address(es) in any format, including
     *            possible personal information as well as the
     *            actual address (such as "Jose" <jose@example.org>
     *            or "Jose" <jose@example.org>, "Keiko" <keiko@example.org>)
     * @param int $max
     *            The most email addresses to parse out of the given string
     *            
     * @return array An array with one sub-array for each address found in the
     *         given string. Each sub-array contains two (?) entries, the
     *         first containing the actual email address, the second
     *         containing any personal information that was in the address
     *         string
     *        
     */
    function parseAddress($address, $max = 0)
    {
        $aTokens = array();
        $aAddress = array();
        $iCnt = strlen($address);
        $aSpecials = array(
            '(',
            '<',
            ',',
            ';',
            ':'
        );
        $aReplace = array(
            ' (',
            ' <',
            ' ,',
            ' ;',
            ' :'
        );
        $address = str_replace($aSpecials, $aReplace, $address);
        $i = 0;
        while ($i < $iCnt) {
            $cChar = $address{$i};
            switch ($cChar) {
                case '<':
                    $iEnd = strpos($address, '>', $i + 1);
                    if (! $iEnd) {
                        $sToken = substr($address, $i);
                        $i = $iCnt;
                    } else {
                        $sToken = substr($address, $i, $iEnd - $i + 1);
                        $i = $iEnd;
                    }
                    $sToken = str_replace($aReplace, $aSpecials, $sToken);
                    $aTokens[] = $sToken;
                    break;
                case '"':
                    $iEnd = strpos($address, $cChar, $i + 1);
                    if ($iEnd) {
                        // skip escaped quotes
                        $prev_char = $address{$iEnd - 1};
                        while ($prev_char === '\\' && substr($address, $iEnd - 2, 2) !== '\\\\') {
                            $iEnd = strpos($address, $cChar, $iEnd + 1);
                            if ($iEnd) {
                                $prev_char = $address{$iEnd - 1};
                            } else {
                                $prev_char = false;
                            }
                        }
                    }
                    if (! $iEnd) {
                        $sToken = substr($address, $i);
                        $i = $iCnt;
                    } else {
                        // also remove the surrounding quotes
                        $sToken = substr($address, $i + 1, $iEnd - $i - 1);
                        $i = $iEnd;
                    }
                    $sToken = str_replace($aReplace, $aSpecials, $sToken);
                    if ($sToken)
                        $aTokens[] = $sToken;
                    break;
                case '(':
                    $iEnd = strrpos($address, ')');
                    if (! $iEnd || $iEnd < $i) {
                        $sToken = substr($address, $i);
                        $i = $iCnt;
                    } else {
                        $sToken = substr($address, $i, $iEnd - $i + 1);
                        $i = $iEnd;
                    }
                    $sToken = str_replace($aReplace, $aSpecials, $sToken);
                    $aTokens[] = $sToken;
                    break;
                case ',':
                case ';':
                case ';':
                case ' ':
                    $aTokens[] = $cChar;
                    break;
                default:
                    $iEnd = strpos($address, ' ', $i + 1);
                    if ($iEnd) {
                        $sToken = trim(substr($address, $i, $iEnd - $i));
                        $i = $iEnd - 1;
                    } else {
                        $sToken = trim(substr($address, $i));
                        $i = $iCnt;
                    }
                    if ($sToken)
                        $aTokens[] = $sToken;
            }
            ++ $i;
        }
        $sPersonal = $sEmail = $sComment = $sGroup = '';
        $aStack = $aComment = array();
        foreach ($aTokens as $sToken) {
            if ($max && $max == count($aAddress)) {
                return $aAddress;
            }
            $cChar = $sToken{0};
            switch ($cChar) {
                case '=':
                case '"':
                case ' ':
                    $aStack[] = $sToken;
                    break;
                case '(':
                    $aComment[] = substr($sToken, 1, - 1);
                    break;
                case ';':
                    if ($sGroup) {
                        $sEmail = trim(implode(' ', $aStack));
                        $aAddress[] = array(
                            $sGroup,
                            $sEmail
                        );
                        $aStack = $aComment = array();
                        $sGroup = '';
                        break;
                    }
                case ',':
                    if (! $sEmail) {
                        while (count($aStack) && ! $sEmail) {
                            $sEmail = trim(array_pop($aStack));
                        }
                    }
                    if (count($aStack)) {
                        $sPersonal = trim(implode('', $aStack));
                    } else {
                        $sPersonal = '';
                    }
                    if (! $sPersonal && count($aComment)) {
                        $sComment = implode(' ', $aComment);
                        $sPersonal .= $sComment;
                    }
                    $aAddress[] = array(
                        $sEmail,
                        $sPersonal
                    );
                    $sPersonal = $sComment = $sEmail = '';
                    $aStack = $aComment = array();
                    break;
                case ':':
                    $sGroup = implode(' ', $aStack);
                    break;
                    $aStack = array();
                    break;
                case '<':
                    $sEmail = trim(substr($sToken, 1, - 1));
                    break;
                case '>':
				/* skip */
				break;
                default:
                    $aStack[] = $sToken;
                    break;
            }
        }
        /* now do the action again for the last address */
        if (! $sEmail) {
            while (count($aStack) && ! $sEmail) {
                $sEmail = trim(array_pop($aStack));
            }
        }
        if (count($aStack)) {
            $sPersonal = trim(implode('', $aStack));
        } else {
            $sPersonal = '';
        }
        if (! $sPersonal && count($aComment)) {
            $sComment = implode(' ', $aComment);
            $sPersonal .= $sComment;
        }
        $aAddress[] = array(
            $sEmail,
            $sPersonal
        );
        return $aAddress;
    }

    /**
     * 分析数组
     *
     * @param
     *            $read
     * @param
     *            $i
     */
    function parseArray($read, &$i)
    {
        $i = strpos($read, '(', $i);
        $i_pos = strpos($read, ')', $i);
        $s = substr($read, $i + 1, $i_pos - $i - 1);
        $a = explode(' ', $s);
        if ($i_pos) {
            $i = $i_pos + 1;
            return $a;
        } else {
            return false;
        }
    }

    /**
     * 转化为可读大小
     *
     * @param
     *            $bytes
     */
    function show_readable_size($bytes)
    {
        $bytes /= 1024;
        $type = 'K';
        if ($bytes / 1024 > 1) {
            $bytes /= 1024;
            $type = 'M';
        }
        if ($bytes < 10) {
            $bytes *= 10;
            settype($bytes, 'integer');
            $bytes /= 10;
        } else {
            settype($bytes, 'integer');
        }
        return $bytes . '<small>' . $type . '</small>';
    }
}