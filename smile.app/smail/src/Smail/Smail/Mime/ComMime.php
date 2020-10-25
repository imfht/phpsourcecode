<?php
namespace Smail\Mime;

use Smail\Mime\Message;
use Smail\Util\ComFunc;

/**
 * email parse util class
 *
 * @author fuyou
 *        
 */
class ComMime
{

    /**
     * Encodes header as quoted-printable
     *
     * Encode a string according to RFC 1522 for use in headers if it
     * contains 8-bit characters or anything that looks like it should
     * be encoded.
     */
    public static function encodeHeader($string, $default_charset = 'utf-8')
    {
        // Use B encoding for multibyte charsets
        $mb_charsets = array(
            'utf-8',
            'big5',
            'gb2312',
            'euc-kr',
            'gbk'
        );
        if (in_array($default_charset, $mb_charsets)) {
            return self::encodeHeaderBase64($string, $default_charset);
        }
        // Encode only if the string contains 8-bit characters or =?
        $j = strlen($string);
        $max_l = 75 - strlen($default_charset) - 7;
        $aRet = array();
        $ret = '';
        $iEncStart = $enc_init = false;
        $cur_l = $iOffset = 0;
        for ($i = 0; $i < $j; ++ $i) {
            switch ($string{$i}) {
                case '"':
                case '=':
                case '<':
                case '>':
                case ',':
                case '?':
                case '_':
                    if ($iEncStart === false) {
                        $iEncStart = $i;
                    }
                    $cur_l += 3;
                    if ($cur_l > ($max_l - 2)) {
                        /* if there is an stringpart that doesn't need encoding, add it */
                        $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                        $aRet[] = "=?$default_charset?Q?$ret?=";
                        $iOffset = $i;
                        $cur_l = 0;
                        $ret = '';
                        $iEncStart = false;
                    } else {
                        $ret .= sprintf("=%02X", ord($string{$i}));
                    }
                    break;
                case '(':
                case ')':
                    if ($iEncStart !== false) {
                        $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                        $aRet[] = "=?$default_charset?Q?$ret?=";
                        $iOffset = $i;
                        $cur_l = 0;
                        $ret = '';
                        $iEncStart = false;
                    }
                    break;
                case ' ':
                    if ($iEncStart !== false) {
                        $cur_l ++;
                        if ($cur_l > $max_l) {
                            $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                            $aRet[] = "=?$default_charset?Q?$ret?=";
                            $iOffset = $i;
                            $cur_l = 0;
                            $ret = '';
                            $iEncStart = false;
                        } else {
                            $ret .= '_';
                        }
                    }
                    break;
                default:
                    $k = ord($string{$i});
                    if ($k > 126) {
                        if ($iEncStart === false) {
                            // do not start encoding in the middle of a string, also take the rest of the word.
                            $sLeadString = substr($string, 0, $i);
                            $aLeadString = explode(' ', $sLeadString);
                            $sToBeEncoded = array_pop($aLeadString);
                            $iEncStart = $i - strlen($sToBeEncoded);
                            $ret .= $sToBeEncoded;
                            $cur_l += strlen($sToBeEncoded);
                        }
                        $cur_l += 3;
                        /* first we add the encoded string that reached it's max size */
                        if ($cur_l > ($max_l - 2)) {
                            $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                            $aRet[] = "=?$default_charset?Q?$ret?= "; /* the next part is also encoded => separate by space */
                            $cur_l = 3;
                            $ret = '';
                            $iOffset = $i;
                            $iEncStart = $i;
                        }
                        $enc_init = true;
                        $ret .= sprintf("=%02X", $k);
                    } else {
                        if ($iEncStart !== false) {
                            $cur_l ++;
                            if ($cur_l > $max_l) {
                                $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                                $aRet[] = "=?$default_charset?Q?$ret?=";
                                $iEncStart = false;
                                $iOffset = $i;
                                $cur_l = 0;
                                $ret = '';
                            } else {
                                $ret .= $string{$i};
                            }
                        }
                    }
                    break;
            }
        }
        if ($enc_init) {
            if ($iEncStart !== false) {
                $aRet[] = substr($string, $iOffset, $iEncStart - $iOffset);
                $aRet[] = "=?$default_charset?Q?$ret?=";
            } else {
                $aRet[] = substr($string, $iOffset);
            }
            $string = implode('', $aRet);
        }
        return $string;
    }

    /**
     * Decodes headers
     *
     * This functions decode strings that is encoded according to
     * RFC1522 (MIME Part Two: Message Header Extensions for Non-ASCII Text).
     * Patched by Christian Schmidt <christian@ostenfeld.dk> 23/03/2002
     */
    public static function decodeHeader($string, $utfencode = true, $htmlsave = true)
    {
        if (is_array($string)) {
            $string = implode("\n", $string);
        }
        $i = 0;
        $iLastMatch = - 2;
        $encoded = false;
        $aString = explode(' ', $string);
        $ret = '';
        foreach ($aString as $chunk) {
            if ($encoded && $chunk === '') {
                continue;
            } elseif ($chunk === '') {
                $ret .= ' ';
                continue;
            }
            $encoded = false;
            /* if encoded words are not separated by a linear-space-white we still catch them */
            $j = $i - 1;
            while ($match = preg_match('/^(.*)=\?([^?]*)\?(Q|B)\?([^?]*)\?=(.*)$/Ui', $chunk, $res)) {
                /* if the last chunk isn't an encoded string then put back the space, otherwise don't */
                if ($iLastMatch !== $j) {
                    if ($htmlsave) {
                        $ret .= '&#32;';
                    } else {
                        $ret .= ' ';
                    }
                }
                $iLastMatch = $i;
                $j = $i;
                if ($htmlsave) {
                    $ret .= htmlspecialchars($res[1]);
                } else {
                    $ret .= $res[1];
                }
                $encoding = ucfirst($res[3]);
                $code = $res[2];
                switch ($encoding) {
                    case 'B':
                        $replace = base64_decode($res[4]);
                        if ($utfencode) {
                            $replace = iconv($code, 'utf-8', $replace);
                        } elseif ($htmlsave) {
                            $replace = htmlspecialchars($replace);
                        }
                        $ret .= $replace;
                        break;
                    case 'Q':
                        $replace = str_replace('_', ' ', $res[4]);
                        $replace = preg_replace('/=([0-9a-f]{2})/ie', 'chr(hexdec("\1"))', $replace);
                        if ($utfencode) {
                            $replace = iconv($code, 'utf-8', $replace);
                        } elseif ($htmlsave) {
                            $replace = htmlspecialchars($replace);
                        }
                        $ret .= $replace;
                        break;
                    default:
                        break;
                }
                $chunk = $res[5];
                $encoded = true;
            }
            if (! $encoded) {
                if ($htmlsave) {
                    $ret .= '&#32;';
                } else {
                    $ret .= ' ';
                }
            }
            if (! $encoded && $htmlsave) {
                $ret .= htmlspecialchars($chunk);
            } else {
                $ret .= $chunk;
            }
            ++ $i;
        }
        /* remove the first added space */
        if ($ret) {
            if ($htmlsave) {
                $ret = substr($ret, 5);
            } else {
                $ret = substr($ret, 1);
            }
        }
        return $ret;
    }

    /**
     * Fold header lines per RFC 2822/2.2.3 and RFC 822/3.1.1
     *
     * Herein "soft" folding/wrapping (with whitespace tokens) is
     * what we refer to as the preferred method of wrapping - that
     * which we'd like to do within the $soft_wrap limit, but if
     * not possible, we will try to do as soon as possible after
     * $soft_wrap up to the $hard_wrap limit. Encoded words don't
     * need to be detected in this phase, since they cannot contain
     * spaces.
     *
     * "Hard" folding/wrapping (with "hard" tokens) is what we refer
     * to as less ideal wrapping that will be done to keep within
     * the $hard_wrap limit. This adds other syntactical breaking
     * elements such as commas and encoded words.
     *
     * @param string $header
     *            The header content being folded
     * @param integer $soft_wrap
     *            The desirable maximum line length
     *            (OPTIONAL; default is 78, per RFC)
     * @param string $indent
     *            Wrapped lines will already have
     *            whitespace following the CRLF wrap,
     *            but you can add more indentation (or
     *            whatever) with this. The use of this
     *            parameter is DISCOURAGED, since it
     *            can corrupt the redisplay (unfolding)
     *            of headers whose content is space-
     *            sensitive, like subjects, etc.
     *            (OPTIONAL; default is an empty string)
     * @param string $hard_wrap
     *            The absolute maximum line length
     *            (OPTIONAL; default is 998, per RFC)
     *            
     * @return string The folded header content, with a trailing CRLF.
     * @deprecated
     *
     */
    public static function foldLine($header, $soft_wrap = 78, $indent = '', $hard_wrap = 998)
    {
        // the "hard" token list can be altered if desired,
        // for example, by adding ":"
        // (in the future, we can take optional arguments
        // for overriding or adding elements to the "hard"
        // token list if we want to get fancy)
        //
        // the order of these is significant - preferred
        // fold points should be listed first
        //
        // it is advised that the "=" always come first
        // since it also finds encoded words, thus if it
        // comes after some other token that happens to
        // fall within the encoded word, the encoded word
        // could be inadvertently broken in half, which
        // is not allowable per RFC
        $hard_break_tokens = array(
            '=', // includes encoded word detection
            ',',
            ';'
        );
        // the order of these is significant too
        $whitespace = array(
            ' ',
            "\t"
        );
        $CRLF = "\r\n";
        $folded_header = '';
        // if using an indent string, reduce wrap limits by its size
        if (! empty($indent)) {
            $soft_wrap -= strlen($indent);
            $hard_wrap -= strlen($indent);
        }
        while (strlen($header) > $soft_wrap) {
            $soft_wrapped_line = substr($header, 0, $soft_wrap);
            // look for a token as close to the end of the soft wrap limit as possible
            foreach ($whitespace as $token) {
                // note that this if statement also fails when $pos === 0,
                // which is intended, since blank lines are not allowed
                if ($pos = strrpos($soft_wrapped_line, $token)) {
                    $new_fold = substr($header, 0, $pos);
                    // make sure proposed fold doesn't create a blank line
                    if (! trim($new_fold))
                        continue;
                        // with whitespace breaks, we fold BEFORE the token
                    $folded_header .= $new_fold . $CRLF . $indent;
                    $header = substr($header, $pos);
                    // ready for next while() iteration
                    continue 2;
                }
            }
            // we were unable to find a wrapping point within the soft
            // wrap limit, so now we'll try to find the first possible
            // soft wrap point within the hard wrap limit
            $hard_wrapped_line = substr($header, 0, $hard_wrap);
            // look for a *SOFT* token as close to the
            // beginning of the hard wrap limit as possible
            foreach ($whitespace as $token) {
                // use while loop instead of if block because it
                // is possible we don't want the first one we find
                $pos = $soft_wrap - 1; // -1 is corrected by +1 on next line
                while ($pos = strpos($hard_wrapped_line, $token, $pos + 1)) {
                    $new_fold = substr($header, 0, $pos);
                    // make sure proposed fold doesn't create a blank line
                    if (! trim($new_fold))
                        continue;
                        // with whitespace breaks, we fold BEFORE the token
                    $folded_header .= $new_fold . $CRLF . $indent;
                    $header = substr($header, $pos);
                    // ready for next outter while() iteration
                    continue 3;
                }
            }
            // we were still unable to find a soft wrapping point within
            // both the soft and hard wrap limits, so if the length of
            // what is left is no more than the hard wrap limit, we'll
            // simply take the whole thing
            if (strlen($header) <= strlen($hard_wrapped_line))
                break;
                // otherwise, we can't quit yet - look for a "hard" token
                // as close to the end of the hard wrap limit as possible
            foreach ($hard_break_tokens as $token) {
                // note that this if statement also fails when $pos === 0,
                // which is intended, since blank lines are not allowed
                if ($pos = strrpos($hard_wrapped_line, $token)) {
                    // if we found a "=" token, we must determine whether,
                    // if it is part of an encoded word, it is the beginning
                    // or middle of one, where we need to readjust $pos a bit
                    if ($token == '=') {
                        // if we found the beginning of an encoded word,
                        // we want to break BEFORE the token
                        if (preg_match('/^(=\?([^?]*)\?(Q|B)\?([^?]*)\?=)/i', substr($header, $pos))) {
                            $pos --;
                        }                         // check if we found this token in the *middle*
                        // of an encoded word, in which case we have to
                        // ignore it, pushing back to the token that
                        // starts the encoded word instead
                        // of course, this is only possible if there is
                        // more content after the next hard wrap
                        // then look for the end of an encoded word in
                        // the next part (past the next hard wrap)
                        // then see if it is in fact part of a legitimate
                        // encoded word
                        else 
                            if (strlen($header) > $hard_wrap && ($end_pos = strpos(substr($header, $hard_wrap), '?=')) !== FALSE && preg_match('/(=\?([^?]*)\?(Q|B)\?([^?]*)\?=)$/i', substr($header, 0, $hard_wrap + $end_pos + 2), $matches)) {
                                $pos = $hard_wrap + $end_pos + 2 - strlen($matches[1]) - 1;
                            }
                    }
                    // $pos could have been changed; make sure it's
                    // not at the beginning of the line, as blank
                    // lines are not allowed
                    if ($pos === 0)
                        continue;
                        // we are dealing with a simple token break...
                        // for non-whitespace breaks, we fold AFTER the token
                        // and add a space after the fold if not immediately
                        // followed by a whitespace character in the next part
                    $folded_header .= substr($header, 0, $pos + 1) . $CRLF;
                    // don't go beyond end of $header, though
                    if (strlen($header) > $pos + 1) {
                        $header = substr($header, $pos + 1);
                        if (! in_array($header{0}, $whitespace))
                            $header = ' ' . $indent . $header;
                    } else {
                        $header = '';
                    }
                    // ready for next while() iteration
                    continue 2;
                }
            }
            // finally, we just couldn't find anything to fold on, so we
            // have to just cut it off at the hard limit
            $folded_header .= $hard_wrapped_line . $CRLF;
            // is there more?
            if (strlen($header) > strlen($hard_wrapped_line)) {
                $header = substr($header, strlen($hard_wrapped_line));
                if (! in_array($header{0}, $whitespace))
                    $header = ' ' . $indent . $header;
            } else {
                $header = '';
            }
        }
        // add any left-overs
        $folded_header .= $header;
        // make sure it ends with a CRLF
        if (substr($folded_header, - 2) != $CRLF)
            $folded_header .= $CRLF;
        return $folded_header;
    }

    /**
     * check the string if it's 8bit
     *
     * @param string $string            
     * @param string $charset            
     */
    public static function is8bit($string, $charset = 'utf-8')
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

    /**
     * utf7 encode
     *
     * @param string $s            
     * @param
     *            $charset
     */
    public static function utf7_encode($s, $charset = 'utf-8')
    {
        if ($s == '')
            return '';
        if ((strtolower($charset) != 'iso-8859-1')) {
            $utf7_s = ComFunc::sm_mb_convert_encoding($s, 'UTF7-IMAP', $charset);
            if ($utf7_s != '')
                return $utf7_s;
        }
        // Later code works only for ISO-8859-1
        $b64_s = ''; // buffer for substring to be base64-encoded
        $utf7_s = ''; // imap-utf7-encoded string
        for ($i = 0; $i < strlen($s); $i ++) {
            $c = $s[$i];
            $ord_c = ord($c);
            if ((($ord_c >= 0x20) and ($ord_c <= 0x25)) or (($ord_c >= 0x27) and ($ord_c <= 0x7e))) {
                if ($b64_s) {
                    $utf7_s = $utf7_s . '&' . $this->encodeBASE64($b64_s) . '-';
                    $b64_s = '';
                }
                $utf7_s = $utf7_s . $c;
            } elseif ($ord_c == 0x26) {
                if ($b64_s) {
                    $utf7_s = $utf7_s . '&' . $this->encodeBASE64($b64_s) . '-';
                    $b64_s = '';
                }
                $utf7_s = $utf7_s . '&-';
            } else {
                $b64_s = $b64_s . chr(0) . $c;
            }
        }
        if ($b64_s) {
            $utf7_s = $utf7_s . '&' . $this->encodeBASE64($b64_s) . '-';
            $b64_s = '';
        }
        return $utf7_s;
    }

    public static function utf7_decode($s, $charset = 'utf-8')
    {
        if ($s == '')
            return '';
        if ((strtolower($charset) != 'iso-8859-1') && ($charset != '')) {
            $utf7_s = ComFunc::sm_mb_convert_encoding($s, $charset, 'UTF7-IMAP');
            if ($utf7_s != '')
                return $utf7_s;
        }
        // Later code works only for ISO-8859-1
        $b64_s = '';
        $iso_8859_1_s = '';
        for ($i = 0, $len = strlen($s); $i < $len; $i ++) {
            $c = $s[$i];
            if (strlen($b64_s) > 0) {
                if ($c == '-') {
                    if ($b64_s == '&') {
                        $iso_8859_1_s = $iso_8859_1_s . '&';
                    } else {
                        $iso_8859_1_s = $iso_8859_1_s . self::decodeBASE64(substr($b64_s, 1));
                    }
                    $b64_s = '';
                } else {
                    $b64_s = $b64_s . $c;
                }
            } else {
                if ($c == '&') {
                    $b64_s = '&';
                } else {
                    $iso_8859_1_s = $iso_8859_1_s . $c;
                }
            }
        }
        return $iso_8859_1_s;
    }

    public static function encodeBASE64($s)
    {
        $B64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+,';
        $p = 0; // phase: 1 / 2 / 3 / 1 / 2 / 3...
        $e = ''; // base64-encoded string
                 // foreach($s as $c) {
        for ($i = 0; $i < strlen($s); $i ++) {
            $c = $s[$i];
            if ($p == 0) {
                $e = $e . substr($B64Chars, ((ord($c) & 252) >> 2), 1);
                $t = (ord($c) & 3);
                $p = 1;
            } elseif ($p == 1) {
                $e = $e . $B64Chars[($t << 4) + ((ord($c) & 240) >> 4)];
                $t = (ord($c) & 15);
                $p = 2;
            } elseif ($p == 2) {
                $e = $e . $B64Chars[($t << 2) + ((ord($c) & 192) >> 6)];
                $e = $e . $B64Chars[ord($c) & 63];
                $p = 0;
            }
        }
        if ($p == 1) {
            $e = $e . $B64Chars[$t << 4];
        } elseif ($p == 2) {
            $e = $e . $B64Chars[$t << 2];
        }
        return $e;
    }

    public static function decodeBASE64($s)
    {
        $B64Values = array(
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'P' => 15,
            'Q' => 16,
            'R' => 17,
            'S' => 18,
            'T' => 19,
            'U' => 20,
            'V' => 21,
            'W' => 22,
            'X' => 23,
            'Y' => 24,
            'Z' => 25,
            'a' => 26,
            'b' => 27,
            'c' => 28,
            'd' => 29,
            'e' => 30,
            'f' => 31,
            'g' => 32,
            'h' => 33,
            'i' => 34,
            'j' => 35,
            'k' => 36,
            'l' => 37,
            'm' => 38,
            'n' => 39,
            'o' => 40,
            'p' => 41,
            'q' => 42,
            'r' => 43,
            's' => 44,
            't' => 45,
            'u' => 46,
            'v' => 47,
            'w' => 48,
            'x' => 49,
            'y' => 50,
            'z' => 51,
            '0' => 52,
            '1' => 53,
            '2' => 54,
            '3' => 55,
            '4' => 56,
            '5' => 57,
            '6' => 58,
            '7' => 59,
            '8' => 60,
            '9' => 61,
            '+' => 62,
            ',' => 63
        );
        $p = 0;
        $d = '';
        $unicodeNullByteToggle = 0;
        for ($i = 0, $len = strlen($s); $i < $len; $i ++) {
            $c = $s[$i];
            if ($p == 0) {
                $t = $B64Values[$c];
                $p = 1;
            } elseif ($p == 1) {
                if ($unicodeNullByteToggle) {
                    $d = $d . chr(($t << 2) + (($B64Values[$c] & 48) >> 4));
                    $unicodeNullByteToggle = 0;
                } else {
                    $unicodeNullByteToggle = 1;
                }
                $t = ($B64Values[$c] & 15);
                $p = 2;
            } elseif ($p == 2) {
                if ($unicodeNullByteToggle) {
                    $d = $d . chr(($t << 4) + (($B64Values[$c] & 60) >> 2));
                    $unicodeNullByteToggle = 0;
                } else {
                    $unicodeNullByteToggle = 1;
                }
                $t = ($B64Values[$c] & 3);
                $p = 3;
            } elseif ($p == 3) {
                if ($unicodeNullByteToggle) {
                    $d = $d . chr(($t << 6) + $B64Values[$c]);
                    $unicodeNullByteToggle = 0;
                } else {
                    $unicodeNullByteToggle = 1;
                }
                $t = ($B64Values[$c] & 3);
                $p = 0;
            }
        }
        return $d;
    }

    /**
     * Get the MIME structure
     *
     * This function gets the structure of a message and stores it in the "message" class.
     * It will return this object for use with all relevant header information and
     * fully parsed into the standard "message" object format.
     */
    public static function mime_structure($bodystructure, $flags = array())
    {
        
        /* Isolate the body structure and remove beginning and end parenthesis. */
        $read = trim(substr($bodystructure, strpos(strtolower($bodystructure), 'bodystructure') + 13));
        $read = trim(substr($read, 0, - 1));
        $i = 0;
        $msg = Message::parseStructure($read, $i);
        if (! is_object($msg)) {
            exit();
        }
        if (count($flags)) {
            foreach ($flags as $flag) {
                $char = strtoupper($flag{1});
                switch ($char) {
                    case 'S':
                        if (strtolower($flag) == '\\seen') {
                            $msg->is_seen = true;
                        }
                        break;
                    case 'A':
                        if (strtolower($flag) == '\\answered') {
                            $msg->is_answered = true;
                        }
                        break;
                    case 'D':
                        if (strtolower($flag) == '\\deleted') {
                            $msg->is_deleted = true;
                        }
                        break;
                    case 'F':
                        if (strtolower($flag) == '\\flagged') {
                            $msg->is_flagged = true;
                        }
                        break;
                    case 'M':
                        if (strtolower($flag) == '$mdnsent') {
                            $msg->is_mdnsent = true;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $msg;
    }

    /**
     * attachment
     *
     * @param
     *            $message
     * @param
     *            $exclude_id
     * @param
     *            $id
     */
    public static function formatAttachments($message, $exclude_id)
    {
        $att_ar = $message->getAttachments($exclude_id);
        if (! count($att_ar))
            return '';
        $i = 0;
        foreach ($att_ar as $att) {
            $ent = $att->entity_id;
            $header = $att->header;
            $type0 = strtolower($header->type0);
            $type1 = strtolower($header->type1);
            $name = '';
            if ($type0 == 'message' && $type1 == 'rfc822') {
                $rfc822_header = $att->rfc822_header;
                $filename = $rfc822_header->subject;
                if (trim($filename) == '') {
                    $filename = 'untitled-[' . $ent . ']';
                }
                $from_o = $rfc822_header->from;
                if (is_object($from_o)) {
                    $from_name = $from_o->getAddress(false);
                } elseif (is_array($from_o) && count($from_o) && is_object($from_o[0])) {
                    $from_name = $from_o[0]->getAddress(false);
                } else {
                    $from_name = "Unknown sender";
                }
                $from_name = self::decodeHeader($from_name);
                $description = $from_name;
            } else {
                if (is_object($header->disposition)) {
                    $filename = $header->disposition->getProperty('filename');
                    if (trim($filename) == '') {
                        $name = self::decodeHeader($header->disposition->getProperty('name'));
                        if (trim($name) == '') {
                            $name = $header->getParameter('name');
                            if (trim($name) == '') {
                                if (trim($header->id) == '') {
                                    $filename = 'untitled-[' . $ent . ']' . '.' . strtolower($header->type1);
                                } else {
                                    $filename = 'cid: ' . $header->id . '.' . strtolower($header->type1);
                                }
                            } else {
                                $filename = $name;
                            }
                        } else {
                            $filename = $name;
                        }
                    }
                } else {
                    $filename = $header->getParameter('name');
                    if (! trim($filename)) {
                        if (trim($header->id) == '') {
                            return;
                            $filename = 'untitled-[' . $ent . ']' . '.' . strtolower($header->type1);
                        } else {
                            $filename = 'cid: ' . $header->id . '.' . strtolower($header->type1);
                        }
                    }
                }
                if ($header->description) {
                    $description = self::decodeHeader($header->description);
                } else {
                    $description = '';
                }
            }
            $attachments[$i]['filename'] = self::decodeHeader($filename);
            $attachments[$i]['type'] = htmlspecialchars($type0) . '/' . htmlspecialchars($type1);
            $attachments[$i]['description'] = $description;
            $attachments[$i]['size'] = ComFunc::show_readable_size($header->size);
            $i ++;
        }
        return $attachments;
    }

    /**
     * Decodes encoded message body
     *
     * This function decodes the body depending on the encoding type.
     * Currently quoted-printable and base64 encodings are supported.
     * decode_body hook was added to this function in 1.4.2/1.5.0
     * 
     * @param string $body
     *            encoded message body
     * @param string $encoding
     *            used encoding
     * @return string decoded string
     * @since 1.0
     */
    public static function decodeBody($body, $encoding)
    {
        $body = str_replace("\r\n", "\n", $body);
        $encoding = strtolower($encoding);
        if ($encoding == 'quoted-printable' || $encoding == 'quoted_printable') {
            /**
             * quoted_printable_decode() function is broken in older
             * php versions.
             * Text with \r\n decoding was fixed only
             * in php 4.3.0. Minimal code requirement 4.0.4 +
             * str_replace("\r\n", "\n", $body); call.
             */
            $body = quoted_printable_decode($body);
        } elseif ($encoding == 'base64') {
            $body = base64_decode($body);
        }
        return $body;
    }

    /**
     * This returns a parsed string called $body.
     * That string can then
     * be displayed as the actual message in the HTML. It contains
     * everything needed, including HTML Tags, Attachments at the
     * bottom, etc.
     */
    public static function formatBody($imap_stream, $message, $color, $wrap_at, $ent_num, $id)
    {
        $body = '';
        $body_message = $message->getEntity($ent_num);
        if (($body_message->header->type0 == 'text') || ($body_message->header->type0 == 'rfc822')) {
            $body = self::mime_fetch_body($imap_stream, $id, $ent_num);
            $body = self::decodeBody($body, $body_message->header->encoding);
            /*
             * If there are other types that shouldn't be formatted, add
             * them here.
             */
            $show_html_default = 1;
            if ($body_message->header->type1 == 'html') {
                if ($show_html_default != 1) {
                    $entity_conv = array(
                        '&nbsp;' => ' ',
                        '<p>' => "\n",
                        '<P>' => "\n",
                        '<br>' => "\n",
                        '<BR>' => "\n",
                        '<br />' => "\n",
                        '<BR />' => "\n",
                        '&gt;' => '>',
                        '&lt;' => '<'
                    );
                    $body = strtr($body, $entity_conv);
                    $body = strip_tags($body);
                    $body = trim($body);
                    self::translateText($body, $wrap_at, $body_message->header->getParameter('charset'));
                } else {
                    $charset = $body_message->header->getParameter('charset');
                    if (! empty($charset)) {
                        $charset = strtolower($charset);
                        if ($charset == 'gbk' || $charset == 'gb2312' || $charset == 'gb18030') {
                            $body = iconv($charset, 'utf-8//IGNORE', $body);
                        }
                        $body = self::magicHTML($body, $id, $message);
                    }
                }
            } else {
                $charset = $body_message->header->getParameter('charset');
                self::translateText($body, $wrap_at, $charset);
            }
        }
        return $body;
    }

    /**
     * This function trys to locate the entity_id of a specific mime element
     */
    public static function find_ent_id($id, $message)
    {
        for ($i = 0, $ret = ''; $ret == '' && $i < count($message->entities); $i ++) {
            if ($message->entities[$i]->header->type0 == 'multipart') {
                $ret = self::find_ent_id($id, $message->entities[$i]);
            } else {
                if (strcasecmp($message->entities[$i]->header->id, $id) == 0) {
                    return $message->entities[$i]->entity_id;
                } elseif (! empty($message->entities[$i]->header->parameters['name'])) {
                    /**
                     * This is part of a fix for Outlook Express 6.x generating
                     * cid URLs without creating content-id headers
                     * @@JA - 20050207
                     */
                    if (strcasecmp($message->entities[$i]->header->parameters['name'], $id) == 0) {
                        return $message->entities[$i]->entity_id;
                    }
                }
            }
        }
        return $ret;
    }

    /*
     * This starts the parsing of a particular structure. It is called recursively,
     * so it can be passed different structures. It returns an object of type
     * $message.
     * First, it checks to see if it is a multipart message. If it is, then it
     * handles that as it sees is necessary. If it is just a regular entity,
     * then it parses it and adds the necessary header information (by calling out
     * to mime_get_elements()
     */
    public static function mime_fetch_body($imap_stream, $id, $ent_id = 1, $fetch_size = 0)
    {
        $uid_support = true;
        /*
         * Do a bit of error correction. If we couldn't find the entity id, just guess
         * that it is the first one. That is usually the case anyway.
         */
        if (! $ent_id) {
            $cmd = "FETCH $id BODY[]";
        } else {
            $cmd = "FETCH $id BODY[$ent_id]";
        }
        
        if ($fetch_size != 0)
            $cmd .= "<0.$fetch_size>";
        $smail = new smail();
        $data = $smail->smimap_run_command($imap_stream, $cmd, true, $response, $message, $uid_support);
        do {
            $topline = trim(array_shift($data));
        } while ($topline && $topline[0] == '*' && ! preg_match('/\* [0-9]+ FETCH.*/i', $topline));
        
        $wholemessage = implode('', $data);
        if (preg_match('/\{([^\}]*)\}/', $topline, $regs)) {
            $ret = substr($wholemessage, 0, $regs[1]);
            /*
             * There is some information in the content info header that could be important
             * in order to parse html messages. Let's get them here.
             */
        } else 
            if (preg_match('/"([^"]*)"/', $topline, $regs)) {
                $ret = $regs[1];
            } else 
                if ((stristr($topline, 'nil') !== false) && (empty($wholemessage))) {
                    $ret = $wholemessage;
                } else {
                    $data = $smail->smimap_run_command($imap_stream, "FETCH $passed_id BODY[]", true, $response, $message, $uid_support);
                    array_shift($data);
                    $wholemessage = implode('', $data);
                    $ret = $wholemessage;
                }
        return $ret;
    }

    /**
     * Encodes string according to rfc2047 B encoding header formating rules
     *
     * It is recommended way to encode headers with character sets that store
     * symbols in more than one byte.
     *
     * Function requires mbstring support. If required mbstring functions are missing,
     * function returns false and sets E_USER_WARNING level error message.
     *
     * Minimal requirements - php 4.0.6 with mbstring extension. Please note,
     * that mbstring functions will generate E_WARNING errors, if unsupported
     * character set is used. mb_encode_mimeheader function provided by php
     * mbstring extension is not used in order to get better control of header
     * encoding.
     *
     * Used php code functions - function_exists(), trigger_error(), strlen()
     * (is used with charset names and base64 strings). Used php mbstring
     * functions - mb_strlen and mb_substr.
     *
     * Related documents: rfc 2045 (BASE64 encoding), rfc 2047 (mime header
     * encoding), rfc 2822 (header folding)
     *
     * @param string $string
     *            header string that must be encoded
     * @param string $charset
     *            character set. Must be supported by mbstring extension.
     *            Use _mb_list_encodings() to detect supported charsets.
     * @return string string encoded according to rfc2047 B encoding formating rules
     * @since 1.5.1 and 1.4.6
     */
    private static function encodeHeaderBase64($string, $charset)
    {
        if (! function_exists('mb_strlen') || ! function_exists('mb_substr')) {
            trigger_error('encodeHeaderBase64: Required mbstring functions are missing.', E_USER_WARNING);
            return false;
        }
        $aRet = array();
        /**
         * header length = 75 symbols max (same as in encodeHeader)
         * remove $charset length
         * remove =? ? ?= (5 chars)
         * remove 2 more chars (\r\n ?)
         */
        $iMaxLength = 75 - strlen($charset) - 7;
        // set first character position
        $iStartCharNum = 0;
        // loop through all characters. count characters and not bytes.
        $encoded_string = '';
        for ($iCharNum = 1; $iCharNum <= mb_strlen($string, $charset); $iCharNum ++) {
            // encode string from starting character to current character.
            $encoded_string = base64_encode(mb_substr($string, $iStartCharNum, $iCharNum - $iStartCharNum, $charset));
            // Check encoded string length
            if (strlen($encoded_string) > $iMaxLength) {
                // if string exceeds max length, reduce number of encoded characters and add encoded string part to array
                $aRet[] = base64_encode(mb_substr($string, $iStartCharNum, $iCharNum - $iStartCharNum - 1, $charset));
                // set new starting character
                $iStartCharNum = $iCharNum - 1;
                // encode last char (in case it is last character in string)
                $encoded_string = base64_encode(mb_substr($string, $iStartCharNum, $iCharNum - $iStartCharNum, $charset));
            } // if string is shorter than max length - add next character
        }
        // add last encoded string to array
        $aRet[] = $encoded_string;
        // set initial return string
        $sRet = '';
        foreach ($aRet as $string) {
            // TODO: Do we want to control EOL (end-of-line) marker
            if ($sRet != '')
                $sRet .= " ";
                // add header tags and encoded string to return string
            $sRet .= '=?' . $charset . '?B?' . $string . '?=';
        }
        return $sRet;
    }

    /*
     * translateText
     * Extracted from strings.php 23/03/2002
     */
    private static function translateText(&$body, $wrap_at, $charset)
    {
        $body_ary = explode("\n", $body);
        for ($i = 0; $i < count($body_ary); $i ++) {
            $line = $body_ary[$i];
            if (strlen($line) - 2 >= $wrap_at) {
                self::wordWrap($line, $wrap_at, $charset);
            }
            $charset = strtolower($charset);
            if ($charset == 'gbk' || $charset == 'gb2312' || $charset == 'gb18030') {
                $line = iconv($charset, 'utf-8//IGNORE', $line);
            }
            $line = str_replace("\t", '        ', $line);
            ComFunc::parseUrl($line);
            $quotes = 0;
            $pos = 0;
            $j = strlen($line);
            while ($pos < $j) {
                if ($line[$pos] == ' ') {
                    $pos ++;
                } else 
                    if (strpos($line, '&gt;', $pos) === $pos) {
                        $pos += 4;
                        $quotes ++;
                    } else {
                        break;
                    }
            }
            $body_ary[$i] = $line;
        }
        $body = implode("\n", $body_ary);
    }

    /**
     * Wraps text at $wrap characters
     *
     * Has a problem with special HTML characters, so call this before
     * you do character translation.
     *
     * Specifically, &#039 comes up as 5 characters instead of 1.
     * This should not add newlines to the end of lines.
     */
    private static function wordWrap(&$line, $wrap, $charset = null)
    {
        preg_match('/^([\t >]*)([^\t >].*)?$/', $line, $regs);
        $beginning_spaces = $regs[1];
        if (isset($regs[2])) {
            $words = explode(' ', $regs[2]);
        } else {
            $words = array();
        }
        $i = 0;
        $line = $beginning_spaces;
        while ($i < count($words)) {
            /* Force one word to be on a line (minimum) */
            $line .= $words[$i];
            $line_len = strlen($beginning_spaces) + mb_strlen($words[$i], $charset) + 2;
            if (isset($words[$i + 1]))
                $line_len += mb_strlen($words[$i + 1], $charset);
            $i ++;
            /* Add more words (as long as they fit) */
            while ($line_len < $wrap && $i < count($words)) {
                $line .= ' ' . $words[$i];
                $i ++;
                if (isset($words[$i]))
                    $line_len += mb_strlen($words[$i], $charset) + 1;
                else
                    $line_len += 1;
            }
            /* Skip spaces if they are the first thing on a continued line */
            while (! isset($words[$i]) && $i < count($words)) {
                $i ++;
            }
            /* Go to the next line if we have more to process */
            if ($i < count($words)) {
                $line .= "\n";
            }
        }
    }

    /**
     *
     * @param $body the
     *            body of the message
     * @param $id the
     *            id of the message
     * @param boolean $take_mailto_links
     *            When TRUE, converts mailto: links
     *            into internal SM compose links
     *            (optional; default = TRUE)
     * @return a string with html safe to display in the browser.
     */
    private static function magicHTML($body, $id, $message)
    {
        $has_unsafe_images = false;
        $view_unsafe_images = true;
        $force_tag_closing = true;
        /**
         * Don't display attached images in HTML mode.
         */
        // $attachment_common_show_images = false;
        $tag_list = Array(
            false,
            "object",
            "meta",
            "html",
            "head",
            "base",
            "link",
            "frame",
            "iframe",
            "plaintext",
            "marquee"
        );
        $rm_tags_with_content = Array(
            "script",
            "applet",
            "embed",
            "title",
            "frameset",
            "xmp",
            "xml"
        );
        $self_closing_tags = Array(
            "img",
            "br",
            "hr",
            "input",
            "outbind"
        );
        $rm_attnames = Array(
            "/.*/" => Array(
                "/target/i",
                "/^on.*/i",
                "/^dynsrc/i",
                "/^data.*/i",
                "/^lowsrc.*/i"
            )
        );
        
        $secremoveimg = "../images/" . 'sec_remove_eng.png';
        $bad_attvals = Array(
            "/.*/" => Array(
                "/^src|background/i" => Array(
                    Array(
                        "/^([\'\"])\s*\S+script\s*:.*([\'\"])/si",
                        "/^([\'\"])\s*mocha\s*:*.*([\'\"])/si",
                        "/^([\'\"])\s*about\s*:.*([\'\"])/si"
                    ),
                    Array(
                        "\\1$secremoveimg\\2",
                        "\\1$secremoveimg\\2",
                        "\\1$secremoveimg\\2"
                    )
                ),
                "/^href|action/i" => Array(
                    Array(
                        "/^([\'\"])\s*\S+script\s*:.*([\'\"])/si",
                        "/^([\'\"])\s*mocha\s*:*.*([\'\"])/si",
                        "/^([\'\"])\s*about\s*:.*([\'\"])/si"
                    ),
                    Array(
                        "\\1#\\1",
                        "\\1#\\1",
                        "\\1#\\1"
                    )
                ),
                "/^style/i" => Array(
                    Array(
                        "/\/\*.*\*\//",
                        "/expression/i",
                        "/binding/i",
                        "/behaviou*r/i",
                        "/include-source/i",
                        
                        // position:relative can also be exploited
                        // to put content outside of email body area
                        // and position:fixed is similarly exploitable
                        // as position:absolute, so we'll remove it
                        // altogether....
                        //
                        // Does this screw up legitimate HTML messages?
                        // If so, the only fix I see is to allow position
                        // attributes (any values? I think we still have
                        // to block static and fixed) only if $use_iframe
                        // is enabled (1.5.0+)
                        //
                        // was: "/position\s*:\s*absolute/i",
                        //
                        "/position\s*:/i",
                        "/(\\\\)?u(\\\\)?r(\\\\)?l(\\\\)?/i",
                        "/url\s*\(\s*([\'\"])\s*\S+script\s*:.*([\'\"])\s*\)/si",
                        "/url\s*\(\s*([\'\"])\s*mocha\s*:.*([\'\"])\s*\)/si",
                        "/url\s*\(\s*([\'\"])\s*about\s*:.*([\'\"])\s*\)/si",
                        "/(.*)\s*:\s*url\s*\(\s*([\'\"]*)\s*\S+script\s*:.*([\'\"]*)\s*\)/si"
                    ),
                    Array(
                        "",
                        "idiocy",
                        "idiocy",
                        "idiocy",
                        "idiocy",
                        "idiocy",
                        "url",
                        "url(\\1#\\1)",
                        "url(\\1#\\1)",
                        "url(\\1#\\1)",
                        "\\1:url(\\2#\\3)"
                    )
                )
            )
        );
        if (! $view_unsafe_images) {
            array_push($bad_attvals{'/.*/'}{'/^src|background/i'}[0], '/^([\'\"])\s*https*:.*([\'\"])/si');
            array_push($bad_attvals{'/.*/'}{'/^src|background/i'}[1], "\\1$secremoveimg\\1");
            array_push($bad_attvals{'/.*/'}{'/^style/i'}[0], '/url\([\'\"]?https?:[^\)]*[\'\"]?\)/si');
            array_push($bad_attvals{'/.*/'}{'/^style/i'}[1], "url(\\1$secremoveimg\\1)");
        }
        
        $add_attr_to_tag = Array(
            "/^a$/i" => Array(
                'target' => '"_blank"',
                'title' => '"' . 'This external link will open in a new window' . '"'
            )
        );
        $trusted = self::_sanitize($body, $tag_list, $rm_tags_with_content, $self_closing_tags, $force_tag_closing, $rm_attnames, $bad_attvals, $add_attr_to_tag, $message, $id);
        if (strpos($trusted, $secremoveimg)) {
            $has_unsafe_images = true;
        }
        return $trusted;
    }

    /**
     * A small helper function to use with array_walk.
     * Modifies a by-ref
     * value and makes it lowercase.
     *
     * @param $val a
     *            value passed by-ref.
     * @return void since it modifies a by-ref value.
     */
    private static function _casenormalize(&$val)
    {
        $val = strtolower($val);
    }

    /**
     * This is the main function and the one you should actually be calling.
     * There are several variables you should be aware of an which need
     * special description.
     *
     * Since the description is quite lengthy, see it here:
     * http://linux.duke.edu/projects/mini/htmlfilter/
     *
     * @param $body the
     *            string with HTML you wish to filter
     * @param $tag_list see
     *            description above
     * @param $rm_tags_with_content see
     *            description above
     * @param $self_closing_tags see
     *            description above
     * @param $force_tag_closing see
     *            description above
     * @param $rm_attnames see
     *            description above
     * @param $bad_attvals see
     *            description above
     * @param $add_attr_to_tag see
     *            description above
     * @param $message message
     *            object
     * @param $id message
     *            id
     * @return sanitized html safe to show on your pages.
     */
    private static function _sanitize($body, $tag_list, $rm_tags_with_content, $self_closing_tags, $force_tag_closing, $rm_attnames, $bad_attvals, $add_attr_to_tag, $message, $id)
    {
        $rm_tags = array_shift($tag_list);
        /**
         * Normalize rm_tags and rm_tags_with_content.
         */
        @array_walk($tag_list, '_casenormalize');
        @array_walk($rm_tags_with_content, '_casenormalize');
        @array_walk($self_closing_tags, '_casenormalize');
        /**
         * See if tag_list is of tags to remove or tags to allow.
         * false means remove these tags
         * true means allow these tags
         */
        $curpos = 0;
        $open_tags = Array();
        $trusted = "\n<!-- begin sanitized html -->\n";
        $skip_content = false;
        /**
         * Take care of netscape's stupid javascript entities like
         * &{alert('boo')};
         */
        $body = preg_replace("/&(\{.*?\};)/si", "&amp;\\1", $body);
        while (($curtag = self::_getnxtag($body, $curpos)) != FALSE) {
            list ($tagname, $attary, $tagtype, $lt, $gt) = $curtag;
            $free_content = substr($body, $curpos, $lt - $curpos);
            /**
             * Take care of <style>
             */
            if ($tagname == "style" && $tagtype == 1) {
                list ($free_content, $curpos) = self::_fixstyle($body, $gt + 1, $message, $id);
                if ($free_content != FALSE) {
                    if (! is_array($attary)) {
                        $attary = array(
                            $attary
                        );
                    }
                    $attary = self::_fixatts($tagname, $attary, $rm_attnames, $bad_attvals, $add_attr_to_tag, $message, $id);
                    
                    $trusted .= self::_tagprint($tagname, $attary, $tagtype);
                    $trusted .= $free_content;
                    $trusted .= self::_tagprint($tagname, false, 2);
                }
                continue;
            }
            if ($skip_content == false) {
                $trusted .= $free_content;
            }
            if ($tagname != false) {
                if ($tagtype == 2) {
                    if ($skip_content == $tagname) {
                        /**
                         * Got to the end of tag we needed to remove.
                         */
                        $tagname = false;
                        $skip_content = false;
                    } else {
                        if ($skip_content == false) {
                            if ($tagname == "body") {
                                $tagname = "div";
                            }
                            if (isset($open_tags{$tagname}) && $open_tags{$tagname} > 0) {
                                $open_tags{$tagname} --;
                            } else {
                                $tagname = false;
                            }
                        }
                    }
                } else {
                    /**
                     * $rm_tags_with_content
                     */
                    if ($skip_content == false) {
                        /**
                         * See if this is a self-closing type and change
                         * tagtype appropriately.
                         */
                        if ($tagtype == 1 && in_array($tagname, $self_closing_tags)) {
                            $tagtype = 3;
                        }
                        /**
                         * See if we should skip this tag and any content
                         * inside it.
                         */
                        if ($tagtype == 1 && in_array($tagname, $rm_tags_with_content)) {
                            $skip_content = $tagname;
                        } else {
                            if (($rm_tags == false && in_array($tagname, $tag_list)) || ($rm_tags == true && ! in_array($tagname, $tag_list))) {
                                $tagname = false;
                            } else {
                                /**
                                 * Convert body into div.
                                 */
                                if ($tagname == "body") {
                                    $tagname = "div";
                                    $attary = self::_body2div($attary, $message, $id);
                                }
                                if ($tagtype == 1) {
                                    if (isset($open_tags{$tagname})) {
                                        $open_tags{$tagname} ++;
                                    } else {
                                        $open_tags{$tagname} = 1;
                                    }
                                }
                                /**
                                 * This is where we run other checks.
                                 */
                                if (is_array($attary) && sizeof($attary) > 0) {
                                    $attary = self::_fixatts($tagname, $attary, $rm_attnames, $bad_attvals, $add_attr_to_tag, $message, $id);
                                }
                            }
                        }
                    }
                }
                if ($tagname != false && $skip_content == false) {
                    $trusted .= self::_tagprint($tagname, $attary, $tagtype);
                }
            }
            $curpos = $gt + 1;
        }
        $trusted .= substr($body, $curpos, strlen($body) - $curpos);
        if ($force_tag_closing == true) {
            foreach ($open_tags as $tagname => $opentimes) {
                while ($opentimes > 0) {
                    $trusted .= '</' . $tagname . '>';
                    $opentimes --;
                }
            }
            $trusted .= "\n";
        }
        return $trusted;
    }

    /**
     * This function changes the <body> tag into a <div> tag since we
     * can't really have a body-within-body.
     *
     * @param $attary an
     *            array of attributes and values of <body>
     * @param $message current
     *            message (for cid2http)
     * @param $id current
     *            message id (for cid2http)
     * @return a modified array of attributes to be set for <div>
     */
    private static function _body2div($attary, $message, $id)
    {
        $divattary = Array(
            'class' => "'bodyclass'"
        );
        $bgcolor = '#ffffff';
        $text = '#000000';
        $styledef = '';
        if (is_array($attary) && ! empty($attary)) {
            foreach ($attary as $attname => $attvalue) {
                $quotchar = substr($attvalue, 0, 1);
                $attvalue = str_replace($quotchar, "", $attvalue);
                switch ($attname) {
                    case 'background':
                        $attvalue = _cid2http($message, $id, $attvalue);
                        $styledef .= "background-image: url('$attvalue'); ";
                        break;
                    case 'bgcolor':
                        $styledef .= "background-color: $attvalue; ";
                        break;
                    case 'text':
                        $styledef .= "color: $attvalue; ";
                        break;
                }
            }
            if (strlen($styledef) > 0) {
                $divattary{"style"} = "\"$styledef\"";
            }
        }
        return $divattary;
    }

    /**
     * This function runs various checks against the attributes.
     *
     * @param $tagname String
     *            with the name of the tag.
     * @param $attary Array
     *            with all tag attributes.
     * @param $rm_attnames See
     *            description for _sanitize
     * @param $bad_attvals See
     *            description for _sanitize
     * @param $add_attr_to_tag See
     *            description for _sanitize
     * @param $message message
     *            object
     * @param $id message
     *            id
     * @return Array with modified attributes.
     */
    private static function _fixatts($tagname, $attary, $rm_attnames, $bad_attvals, $add_attr_to_tag, $message, $id)
    {
        if (! is_array($attary) || ! is_object($attary)) {
            return $attary;
        }
        while (list ($attname, $attvalue) = each($attary)) {
            /**
             * See if this attribute should be removed.
             */
            foreach ($rm_attnames as $matchtag => $matchattrs) {
                if (preg_match($matchtag, $tagname)) {
                    foreach ($matchattrs as $matchattr) {
                        if (preg_match($matchattr, $attname)) {
                            unset($attary{$attname});
                            continue;
                        }
                    }
                }
            }
            /**
             * Workaround for IE quirks
             */
            self::_fixIE_idiocy($attvalue);
            /**
             * Remove any backslashes, entities, and extraneous whitespace.
             */
            $oldattvalue = $attvalue;
            self::_defang($attvalue);
            if ($attname == 'style' && $attvalue !== $oldattvalue) {
                // entities are used in the attribute value. In 99% of the cases it's there as XSS
                // i.e.<div style="{ left:exp&#x0280;essio&#x0274;( alert('XSS') ) }">
                $attvalue = "idiocy";
                $attary{$attname} = $attvalue;
            }
            self::_unspace($attvalue);
            
            /**
             * Now let's run checks on the attvalues.
             * I don't expect anyone to comprehend this. If you do,
             * get in touch with me so I can drive to where you live and
             * shake your hand personally. :)
             */
            foreach ($bad_attvals as $matchtag => $matchattrs) {
                if (preg_match($matchtag, $tagname)) {
                    foreach ($matchattrs as $matchattr => $valary) {
                        if (preg_match($matchattr, $attname)) {
                            /**
                             * There are two arrays in valary.
                             * First is matches.
                             * Second one is replacements
                             */
                            list ($valmatch, $valrepl) = $valary;
                            $newvalue = preg_replace($valmatch, $valrepl, $attvalue);
                            if ($newvalue != $attvalue) {
                                $attary{$attname} = $newvalue;
                                $attvalue = $newvalue;
                            }
                        }
                    }
                }
            }
            if ($attname == 'style') {
                if (preg_match('/[\0-\37\200-\377]+/', $attvalue)) {
                    // 8bit and control characters in style attribute values can be used for XSS, remove them
                    $attary{$attname} = '"disallowed character"';
                }
                preg_match_all("/url\s*\((.+)\)/si", $attvalue, $aMatch);
                if (count($aMatch)) {
                    foreach ($aMatch[1] as $sMatch) {
                        // url value
                        $urlvalue = $sMatch;
                        self::_fix_url($attname, $urlvalue, $message, $id, "'");
                        $attary{$attname} = str_replace($sMatch, $urlvalue, $attvalue);
                    }
                }
            } /**
             * Use white list based filtering on attributes which can contain url's
             */
            else 
                if ($attname == 'href' || $attname == 'src' || $attname == 'background') {
                    self::_fix_url($attname, $attvalue, $message, $id);
                    $attary{$attname} = $attvalue;
                }
        }
        /**
         * See if we need to append any attributes to this tag.
         */
        foreach ($add_attr_to_tag as $matchtag => $addattary) {
            if (preg_match($matchtag, $tagname)) {
                $attary = array_merge($attary, $addattary);
            }
        }
        return $attary;
    }

    /**
     * This function looks for the next tag.
     *
     * @param $body String
     *            where to look for the next tag.
     * @param $offset Start
     *            looking from here.
     * @return false if no more tags exist in the body, or
     *         an array with the following members:
     *         - string with the name of the tag
     *         - array with attributes and their values
     *         - integer with tag type (1, 2, or 3)
     *         - integer where the tag starts (starting "<")
     *         - integer where the tag ends (ending ">")
     *         first three members will be false, if the tag is invalid.
     */
    private static function _getnxtag($body, $offset)
    {
        if ($offset > strlen($body)) {
            return false;
        }
        $lt = self::_findnxstr($body, $offset, "<");
        if ($lt == strlen($body)) {
            return false;
        }
        /**
         * We are here:
         * blah blah <tag attribute="value">
         * \---------^
         */
        $pos = self::_skipspace($body, $lt + 1);
        if ($pos >= strlen($body)) {
            return Array(
                false,
                false,
                false,
                $lt,
                strlen($body)
            );
        }
        /**
         * There are 3 kinds of tags:
         * 1.
         * Opening tag, e.g.:
         * <a href="blah">
         * 2. Closing tag, e.g.:
         * </a>
         * 3. XHTML-style content-less tag, e.g.:
         * <img src="blah" />
         */
        $tagtype = false;
        switch (substr($body, $pos, 1)) {
            case '/':
                $tagtype = 2;
                $pos ++;
                break;
            case '!':
                /**
                 * A comment or an SGML declaration.
                 */
                if (substr($body, $pos + 1, 2) == "--") {
                    $gt = strpos($body, "-->", $pos);
                    if ($gt === false) {
                        $gt = strlen($body);
                    } else {
                        $gt += 2;
                    }
                    return Array(
                        false,
                        false,
                        false,
                        $lt,
                        $gt
                    );
                } else {
                    $gt = self::_findnxstr($body, $pos, ">");
                    return Array(
                        false,
                        false,
                        false,
                        $lt,
                        $gt
                    );
                }
                break;
            default:
                /**
                 * Assume tagtype 1 for now.
                 * If it's type 3, we'll switch values
                 * later.
                 */
                $tagtype = 1;
                break;
        }
        $tag_start = $pos;
        $tagname = '';
        /**
         * Look for next [\W-_], which will indicate the end of the tag name.
         */
        $regary = self::_findnxreg($body, $pos, "[^\w\-_]");
        if ($regary == false) {
            return Array(
                false,
                false,
                false,
                $lt,
                strlen($body)
            );
        }
        list ($pos, $tagname, $match) = $regary;
        $tagname = strtolower($tagname);
        /**
         * $match can be either of these:
         * '>' indicating the end of the tag entirely.
         * '\s' indicating the end of the tag name.
         * '/' indicating that this is type-3 xhtml tag.
         *
         * Whatever else we find there indicates an invalid tag.
         */
        switch ($match) {
            case '/':
                /**
                 * This is an xhtml-style tag with a closing / at the
                 * end, like so: <img src="blah" />.
                 * Check if it's followed
                 * by the closing bracket. If not, then this tag is invalid
                 */
                if (substr($body, $pos, 2) == "/>") {
                    $pos ++;
                    $tagtype = 3;
                } else {
                    $gt = self::_findnxstr($body, $pos, ">");
                    $retary = Array(
                        false,
                        false,
                        false,
                        $lt,
                        $gt
                    );
                    return $retary;
                }
            case '>':
                return Array(
                    $tagname,
                    false,
                    $tagtype,
                    $lt,
                    $pos
                );
                break;
            default:
                /**
                 * Check if it's whitespace
                 */
                if (! preg_match('/\s/', $match)) {
                    /**
                     * This is an invalid tag! Look for the next closing ">".
                     */
                    $gt = self::_findnxstr($body, $lt, ">");
                    return Array(
                        false,
                        false,
                        false,
                        $lt,
                        $gt
                    );
                }
                break;
        }
        
        /**
         * At this point we're here:
         * <tagname attribute='blah'>
         * \-------^
         *
         * At this point we loop in order to find all attributes.
         */
        $attname = '';
        $atttype = false;
        $attary = Array();
        
        while ($pos <= strlen($body)) {
            $pos = self::_skipspace($body, $pos);
            if ($pos == strlen($body)) {
                /**
                 * Non-closed tag.
                 */
                return Array(
                    false,
                    false,
                    false,
                    $lt,
                    $pos
                );
            }
            /**
             * See if we arrived at a ">" or "/>", which means that we reached
             * the end of the tag.
             */
            $matches = Array();
            if (preg_match("%^(\s*)(>|/>)%s", substr($body, $pos), $matches)) {
                /**
                 * Yep.
                 * So we did.
                 */
                $pos += strlen($matches{1});
                if ($matches{2} == "/>") {
                    $tagtype = 3;
                    $pos ++;
                }
                return Array(
                    $tagname,
                    $attary,
                    $tagtype,
                    $lt,
                    $pos
                );
            }
            
            /**
             * There are several types of attributes, with optional
             * [:space:] between members.
             * Type 1:
             * attrname[:space:]=[:space:]'CDATA'
             * Type 2:
             * attrname[:space:]=[:space:]"CDATA"
             * Type 3:
             * attr[:space:]=[:space:]CDATA
             * Type 4:
             * attrname
             *
             * We leave types 1 and 2 the same, type 3 we check for
             * '"' and convert to "&quot" if needed, then wrap in
             * double quotes. Type 4 we convert into:
             * attrname="yes".
             */
            $regary = self::_findnxreg($body, $pos, "[^:\w\-_]");
            if ($regary == false) {
                /**
                 * Looks like body ended before the end of tag.
                 */
                return Array(
                    false,
                    false,
                    false,
                    $lt,
                    strlen($body)
                );
            }
            list ($pos, $attname, $match) = $regary;
            $attname = strtolower($attname);
            /**
             * We arrived at the end of attribute name.
             * Several things possible
             * here:
             * '>' means the end of the tag and this is attribute type 4
             * '/' if followed by '>' means the same thing as above
             * '\s' means a lot of things -- look what it's followed by.
             * anything else means the attribute is invalid.
             */
            switch ($match) {
                case '/':
                    /**
                     * This is an xhtml-style tag with a closing / at the
                     * end, like so: <img src="blah" />.
                     * Check if it's followed
                     * by the closing bracket. If not, then this tag is invalid
                     */
                    if (substr($body, $pos, 2) == "/>") {
                        $pos ++;
                        $tagtype = 3;
                    } else {
                        $gt = self::_findnxstr($body, $pos, ">");
                        $retary = Array(
                            false,
                            false,
                            false,
                            $lt,
                            $gt
                        );
                        return $retary;
                    }
                case '>':
                    $attary{$attname} = '"yes"';
                    return Array(
                        $tagname,
                        $attary,
                        $tagtype,
                        $lt,
                        $pos
                    );
                    break;
                default:
                    /**
                     * Skip whitespace and see what we arrive at.
                     */
                    $pos = self::_skipspace($body, $pos);
                    $char = substr($body, $pos, 1);
                    /**
                     * Two things are valid here:
                     * '=' means this is attribute type 1 2 or 3.
                     * \w means this was attribute type 4.
                     * anything else we ignore and re-loop. End of tag and
                     * invalid stuff will be caught by our checks at the beginning
                     * of the loop.
                     */
                    if ($char == "=") {
                        $pos ++;
                        $pos = self::_skipspace($body, $pos);
                        /**
                         * Here are 3 possibilities:
                         * "'" attribute type 1
                         * '"' attribute type 2
                         * everything else is the content of tag type 3
                         */
                        $quot = substr($body, $pos, 1);
                        if ($quot == "'") {
                            $regary = self::_findnxreg($body, $pos + 1, "\'");
                            if ($regary == false) {
                                return Array(
                                    false,
                                    false,
                                    false,
                                    $lt,
                                    strlen($body)
                                );
                            }
                            list ($pos, $attval, $match) = $regary;
                            $pos ++;
                            $attary{$attname} = "'" . $attval . "'";
                        } else 
                            if ($quot == '"') {
                                $regary = self::_findnxreg($body, $pos + 1, '\"');
                                if ($regary == false) {
                                    return Array(
                                        false,
                                        false,
                                        false,
                                        $lt,
                                        strlen($body)
                                    );
                                }
                                list ($pos, $attval, $match) = $regary;
                                $pos ++;
                                $attary{$attname} = '"' . $attval . '"';
                            } else {
                                /**
                                 * These are hateful.
                                 * Look for \s, or >.
                                 */
                                $regary = self::_findnxreg($body, $pos, "[\s>]");
                                if ($regary == false) {
                                    return Array(
                                        false,
                                        false,
                                        false,
                                        $lt,
                                        strlen($body)
                                    );
                                }
                                list ($pos, $attval, $match) = $regary;
                                /**
                                 * If it's ">" it will be caught at the top.
                                 */
                                $attval = preg_replace("/\"/s", "&quot;", $attval);
                                $attary{$attname} = '"' . $attval . '"';
                            }
                    } else 
                        if (preg_match("|[\w/>]|", $char)) {
                            /**
                             * That was attribute type 4.
                             */
                            $attary{$attname} = '"yes"';
                        } else {
                            /**
                             * An illegal character.
                             * Find next '>' and return.
                             */
                            $gt = self::_findnxstr($body, $pos, ">");
                            return Array(
                                false,
                                false,
                                false,
                                $lt,
                                $gt
                            );
                        }
                    break;
            }
        }
        /**
         * The fact that we got here indicates that the tag end was never
         * found.
         * Return invalid tag indication so it gets stripped.
         */
        return Array(
            false,
            false,
            false,
            $lt,
            strlen($body)
        );
    }

    /**
     * This function skips any whitespace from the current position within
     * a string and to the next non-whitespace value.
     *
     * @param $body the
     *            string
     * @param $offset the
     *            offset within the string where we should start
     *            looking for the next non-whitespace character.
     * @return the location within the $body where the next
     *         non-whitespace char is located.
     *        
     */
    private static function _skipspace($body, $offset)
    {
        preg_match('/^(\s*)/s', substr($body, $offset), $matches);
        if (sizeof($matches{1})) {
            $count = strlen($matches{1});
            $offset += $count;
        }
        return $offset;
    }

    /**
     * This function looks for the next character within a string.
     * It's
     * really just a glorified "strpos", except it catches if failures
     * nicely.
     *
     * @param $body The
     *            string to look for needle in.
     * @param $offset Start
     *            looking from this position.
     * @param $needle The
     *            character/string to look for.
     * @return location of the next occurance of the needle, or
     *         strlen($body) if needle wasn't found.
     */
    private static function _findnxstr($body, $offset, $needle)
    {
        $pos = strpos($body, $needle, $offset);
        if ($pos === FALSE) {
            $pos = strlen($body);
        }
        return $pos;
    }

    /**
     * This function takes a PCRE-style regexp and tries to match it
     * within the string.
     *
     * @param $body The
     *            string to look for needle in.
     * @param $offset Start
     *            looking from here.
     * @param $reg A
     *            PCRE-style regex to match.
     * @return Returns a false if no matches found, or an array
     *         with the following members:
     *         - integer with the location of the match within $body
     *         - string with whatever content between offset and the match
     *         - string with whatever it is we matched
     *        
     */
    private static function _findnxreg($body, $offset, $reg)
    {
        $matches = Array();
        $retarr = Array();
        preg_match("%^(.*?)($reg)%si", substr($body, $offset), $matches);
        if (! isset($matches{0}) || ! $matches{0}) {
            $retarr = false;
        } else {
            $retarr{0} = $offset + strlen($matches{1});
            $retarr{1} = $matches{1};
            $retarr{2} = $matches{2};
        }
        return $retarr;
    }

    /**
     * This function returns the final tag out of the tag name, an array
     * of attributes, and the type of the tag.
     * This function is called by
     * _sanitize internally.
     *
     * @param $tagname the
     *            name of the tag.
     * @param $attary the
     *            array of attributes and their values
     * @param $tagtype The
     *            type of the tag (see in comments).
     * @return a string with the final tag representation.
     */
    private static function _tagprint($tagname, $attary, $tagtype)
    {
        if ($tagtype == 2) {
            $fulltag = '</' . $tagname . '>';
        } else {
            $fulltag = '<' . $tagname;
            if (is_array($attary) && sizeof($attary)) {
                $atts = Array();
                while (list ($attname, $attvalue) = each($attary)) {
                    array_push($atts, "$attname=$attvalue");
                }
                $fulltag .= ' ' . join(" ", $atts);
            }
            if ($tagtype == 3) {
                $fulltag .= ' /';
            }
            $fulltag .= '>';
        }
        return $fulltag;
    }

    /**
     * fix <style>
     * Enter description here .
     * ..
     * 
     * @param
     *            $body
     * @param
     *            $pos
     * @param
     *            $message
     * @param
     *            $id
     */
    private static function _fixstyle($body, $pos, $message, $id)
    {
        // workaround for </style> in between comments
        $iCurrentPos = $pos;
        $content = '';
        $sToken = '';
        $bSucces = false;
        $bEndTag = false;
        for ($i = $pos, $iCount = strlen($body); $i < $iCount; ++ $i) {
            $char = $body{$i};
            switch ($char) {
                case '<':
                    $sToken = $char;
                    break;
                case '/':
                    if ($sToken == '<') {
                        $sToken .= $char;
                        $bEndTag = true;
                    } else {
                        $content .= $char;
                    }
                    break;
                case '>':
                    if ($bEndTag) {
                        $sToken .= $char;
                        if (preg_match('/\<\/\s*style\s*\>/i', $sToken, $aMatch)) {
                            $newpos = $i + 1;
                            $bSucces = true;
                            break 2;
                        } else {
                            $content .= $sToken;
                        }
                        $bEndTag = false;
                    } else {
                        $content .= $char;
                    }
                    break;
                case '!':
                    if ($sToken == '<') {
                        // possible comment
                        if (isset($body{$i + 2}) && substr($body, $i, 3) == '!--') {
                            $i = strpos($body, '-->', $i + 3);
                            if ($i === false) { // no end comment
                                $i = strlen($body);
                            }
                            $sToken = '';
                        }
                    } else {
                        $content .= $char;
                    }
                    break;
                default:
                    if ($bEndTag) {
                        $sToken .= $char;
                    } else {
                        $content .= $char;
                    }
                    break;
            }
        }
        if ($bSucces == FALSE) {
            return array(
                FALSE,
                strlen($body)
            );
        }
        /**
         * First look for general BODY style declaration, which would be
         * like so:
         * body {background: blah-blah}
         * and change it to .
         * bodyclass so we can just assign it to a <div>
         */
        $content = preg_replace("|body(\s*\{.*?\})|si", ".bodyclass\\1", $content);
        // first check for 8bit sequences and disallowed control characters
        if (preg_match('/[\16-\37\200-\377]+/', $content)) {
            $content = '<!-- style block removed by html filter due to presence of 8bit characters -->';
            return array(
                $content,
                $newpos
            );
        }
        // IE Sucks hard. We have a special function for it.
        self::_fixIE_idiocy($content);
        // remove @import line
        $content = preg_replace("/^\s*(@import.*)$/mi", "\n<!-- @import rules forbidden -->\n", $content);
        
        /**
         * Fix url('blah') declarations.
         */
        // translate ur\l and variations into url (IE parses that)
        // TODO check if the _fixIE_idiocy function already handles this.
        $content = preg_replace("/(\\\\)?u(\\\\)?r(\\\\)?l(\\\\)?/i", 'url', $content);
        preg_match_all("/url\s*\((.+)\)/si", $content, $aMatch);
        if (count($aMatch)) {
            $aValue = $aReplace = array();
            foreach ($aMatch[1] as $sMatch) {
                // url value
                $urlvalue = $sMatch;
                self::_fix_url('style', $urlvalue, $message, $id, "'");
                $aValue[] = $sMatch;
                $aReplace[] = $urlvalue;
            }
            $content = str_replace($aValue, $aReplace, $content);
        }
        
        /**
         * Remove any backslashes, entities, and extraneous whitespace.
         */
        $contentTemp = $content;
        self::_defang($contentTemp);
        self::_unspace($contentTemp);
        
        /**
         * Fix stupid css declarations which lead to vulnerabilities
         * in IE.
         *
         * Also remove "position" attribute, as it can easily be set
         * to "fixed" or "absolute" with "left" and "top" attributes
         * of zero, taking over the whole content frame. It can also
         * be set to relative and move itself anywhere it wants to,
         * displaying content in areas it shouldn't be allowed to touch.
         */
        $match = Array(
            '/\/\*.*\*\//',
            '/expression/i',
            '/behaviou*r/i',
            '/binding/i',
            '/include-source/i',
            '/javascript/i',
            '/script/i',
            '/position/i'
        );
        $replace = Array(
            '',
            'idiocy',
            'idiocy',
            'idiocy',
            'idiocy',
            'idiocy',
            'idiocy',
            ''
        );
        $contentNew = preg_replace($match, $replace, $contentTemp);
        if ($contentNew !== $contentTemp) {
            // insecure css declarations are used. From now on we don't care
            // anymore if the css is destroyed by _deent, _unspace or _unbackslash
            $content = $contentNew;
        }
        return array(
            $content,
            $newpos
        );
    }

    /**
     * Translate all dangerous Unicode or Shift_JIS characters which are accepted by
     * IE as regular characters.
     *
     * @param
     *            attvalue The attribute value before dangerous characters are translated.
     * @return attvalue Nothing, modifies a reference value.
     * @author Marc Groot Koerkamp.
     */
    private static function _fixIE_idiocy(&$attvalue)
    {
        // remove NUL
        $attvalue = str_replace("\0", "", $attvalue);
        // remove comments
        $attvalue = preg_replace("/(\/\*.*?\*\/)/", "", $attvalue);
        
        // IE has the evil habit of accepting every possible value for the attribute expression.
        // The table below contains characters which are parsed by IE if they are used in the "expression"
        // attribute value.
        $aDangerousCharsReplacementTable = array(
            array(
                '&#x029F;',
                '&#0671;' ,/* L UNICODE IPA Extension */
                              '&#x0280;',
                '&#0640;' ,/* R UNICODE IPA Extension */
                              '&#x0274;',
                '&#0628;' ,/* N UNICODE IPA Extension */
                              '&#xFF25;',
                '&#65317;' ,/* Unicode FULLWIDTH LATIN CAPITAL LETTER E */
                              '&#xFF45;',
                '&#65349;' ,/* Unicode FULLWIDTH LATIN SMALL LETTER E */
                              '&#xFF38;',
                '&#65336;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER X */
                              '&#xFF58;',
                '&#65368;',/* Unicode FULLWIDTH LATIN SMALL LETTER X */
                              '&#xFF30;',
                '&#65328;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER P */
                              '&#xFF50;',
                '&#65360;',/* Unicode FULLWIDTH LATIN SMALL LETTER P */
                              '&#xFF32;',
                '&#65330;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER R */
                              '&#xFF52;',
                '&#65362;',/* Unicode FULLWIDTH LATIN SMALL LETTER R */
                              '&#xFF33;',
                '&#65331;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER S */
                              '&#xFF53;',
                '&#65363;',/* Unicode FULLWIDTH LATIN SMALL LETTER S */
                              '&#xFF29;',
                '&#65321;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER I */
                              '&#xFF49;',
                '&#65353;',/* Unicode FULLWIDTH LATIN SMALL LETTER I */
                              '&#xFF2F;',
                '&#65327;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER O */
                              '&#xFF4F;',
                '&#65359;',/* Unicode FULLWIDTH LATIN SMALL LETTER O */
                              '&#xFF2E;',
                '&#65326;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER N */
                              '&#xFF4E;',
                '&#65358;',/* Unicode FULLWIDTH LATIN SMALL LETTER N */
                              '&#xFF2C;',
                '&#65324;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER L */
                              '&#xFF4C;',
                '&#65356;',/* Unicode FULLWIDTH LATIN SMALL LETTER L */
                              '&#xFF35;',
                '&#65333;',/* Unicode FULLWIDTH LATIN CAPITAL LETTER U */
                              '&#xFF55;',
                '&#65365;',/* Unicode FULLWIDTH LATIN SMALL LETTER U */
                              '&#x207F;',
                '&#8319;' ,/* Unicode SUPERSCRIPT LATIN SMALL LETTER N */
                              "\xEF\xBC\xA5", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER E */   // in unicode this is some Chinese char range
                              "\xEF\xBD\x85", /* Shift JIS FULLWIDTH LATIN SMALL LETTER E */
                              "\xEF\xBC\xB8", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER X */
                              "\xEF\xBD\x98", /* Shift JIS FULLWIDTH LATIN SMALL LETTER X */
                              "\xEF\xBC\xB0", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER P */
                              "\xEF\xBD\x90", /* Shift JIS FULLWIDTH LATIN SMALL LETTER P */
                              "\xEF\xBC\xB2", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER R */
                              "\xEF\xBD\x92", /* Shift JIS FULLWIDTH LATIN SMALL LETTER R */
                              "\xEF\xBC\xB3", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER S */
                              "\xEF\xBD\x93", /* Shift JIS FULLWIDTH LATIN SMALL LETTER S */
                              "\xEF\xBC\xA9", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER I */
                              "\xEF\xBD\x89", /* Shift JIS FULLWIDTH LATIN SMALL LETTER I */
                              "\xEF\xBC\xAF", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER O */
                              "\xEF\xBD\x8F", /* Shift JIS FULLWIDTH LATIN SMALL LETTER O */
                              "\xEF\xBC\xAE", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER N */
                              "\xEF\xBD\x8E", /* Shift JIS FULLWIDTH LATIN SMALL LETTER N */
                              "\xEF\xBC\xAC", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER L */
                              "\xEF\xBD\x8C", /* Shift JIS FULLWIDTH LATIN SMALL LETTER L */
                              "\xEF\xBC\xB5", /* Shift JIS FULLWIDTH LATIN CAPITAL LETTER U */
                              "\xEF\xBD\x95", /* Shift JIS FULLWIDTH LATIN SMALL LETTER U */
                              "\xE2\x81\xBF", /* Shift JIS FULLWIDTH SUPERSCRIPT N */
                              "\xCA\x9F", /* L UNICODE IPA Extension */
                              "\xCA\x80", /* R UNICODE IPA Extension */
                              "\xC9\xB4"
            ),  /* N UNICODE IPA Extension */
		array(
                'l',
                'l',
                'r',
                'r',
                'n',
                'n',
                'E',
                'E',
                'e',
                'e',
                'X',
                'X',
                'x',
                'x',
                'P',
                'P',
                'p',
                'p',
                'R',
                'R',
                'r',
                'r',
                'S',
                'S',
                's',
                's',
                'I',
                'I',
                'i',
                'i',
                'O',
                'O',
                'o',
                'o',
                'N',
                'N',
                'n',
                'n',
                'L',
                'L',
                'l',
                'l',
                'U',
                'U',
                'u',
                'u',
                'n',
                'n',
                'E',
                'e',
                'X',
                'x',
                'P',
                'p',
                'R',
                'r',
                'S',
                's',
                'I',
                'i',
                'O',
                'o',
                'N',
                'n',
                'L',
                'l',
                'U',
                'u',
                'n',
                'l',
                'r',
                'n'
            )
        );
        $attvalue = str_replace($aDangerousCharsReplacementTable[0], $aDangerousCharsReplacementTable[1], $attvalue);
        // Escapes are useful for special characters like "{}[]()'&. In other cases they are
        // used for XSS.
        $attvalue = preg_replace("/(\\\\)([a-zA-Z]{1})/", '$2', $attvalue);
    }

    /**
     * Kill any tabs, newlines, or carriage returns.
     * Our friends the
     * makers of the browser with 95% market value decided that it'd
     * be funny to make "java[tab]script" be just as good as "javascript".
     *
     * @param
     *            attvalue The attribute value before extraneous spaces removed.
     * @return attvalue Nothing, modifies a reference value.
     */
    private static function _unspace(&$attvalue)
    {
        if (strcspn($attvalue, "\t\r\n\0 ") != strlen($attvalue)) {
            $attvalue = str_replace(Array(
                "\t",
                "\r",
                "\n",
                "\0",
                " "
            ), Array(
                '',
                '',
                '',
                '',
                ''
            ), $attvalue);
        }
    }

    /**
     * set the image if enable show
     *
     * @param $attvalue String
     *            with attribute value to filter
     * @param $message message
     *            object
     * @param $id message
     *            id
     * @param $sQuote quoting
     *            characters around url's
     */
    private static function _fix_url($attname, &$attvalue, $message, $id, $sQuote = '"')
    {
        $attvalue = trim($attvalue);
        if ($attvalue && ($attvalue[0] == '"' || $attvalue[0] == "'")) {
            // remove the double quotes
            $sQuote = $attvalue[0];
            $attvalue = trim(substr($attvalue, 1, - 1));
        }
        $view_unsafe_images = TRUE;
        $secremoveimg = '';
        /**
         * Replace empty src tags with the blank image.
         * src is only used
         * for frames, images, and image inputs. Doing a replace should
         * not affect them working as should be, however it will stop
         * IE from being kicked off when src for img tags are not set
         */
        if ($attvalue == '') {
            $attvalue = '';
        } else {
            // first, disallow 8 bit characters and control characters
            if (preg_match('/[\0-\37\200-\377]+/', $attvalue)) {
                switch ($attname) {
                    case 'href':
                        $attvalue = $sQuote . 'http://invalid-stuff-detected.example.com' . $sQuote;
                        break;
                    default:
                        $attvalue = '';
                        break;
                }
            } else {
                $aUrl = parse_url($attvalue);
                if (isset($aUrl['scheme'])) {
                    switch (strtolower($aUrl['scheme'])) {
                        case 'mailto':
                        case 'http':
                        case 'https':
                        case 'ftp':
                            if ($attname != 'href') {
                                if ($view_unsafe_images == false) {
                                    $attvalue = $sQuote . $secremoveimg . $sQuote;
                                } else {
                                    if (isset($aUrl['path'])) {} else {
                                        $attvalue = '';
                                    }
                                }
                            } else {
                                $attvalue = $sQuote . $attvalue . $sQuote;
                            }
                            break;
                        case 'outbind':
                            /**
                             * "Hack" fix for Outlook using propriatary outbind:// protocol in img tags.
                             * One day MS might actually make it match something useful, for now, falling
                             * back to using cid2http, so we can grab the blank.png.
                             */
                            $attvalue = $sQuote . self::_cid2http($message, $id, $attvalue) . $sQuote;
                            break;
                        case 'cid':
                            /**
                             * Turn cid: urls into http-friendly ones.
                             */
                            $attvalue = $sQuote . self::_cid2http($message, $id, $attvalue) . $sQuote;
                            break;
                        default:
                            $attvalue = '123';
                            break;
                    }
                } else {
                    if (! (isset($aUrl['path']) && $aUrl['path'] == $secremoveimg)) {
                        // parse_url did not lead to satisfying result
                        $attvalue = '456';
                    }
                }
            }
        }
    }

    /**
     * This function converts cid: url's into the ones that can be viewed in
     * the browser.
     *
     * @param $message the
     *            message object
     * @param $id the
     *            message id
     * @param $cidurl the
     *            cid: url.
     * @return a string with a http-friendly url
     */
    private static function _cid2http($message, $id, $cidurl)
    {
        /**
         * Get rid of quotes.
         */
        $quotchar = substr($cidurl, 0, 1);
        if ($quotchar == '"' || $quotchar == "'") {
            $cidurl = str_replace($quotchar, "", $cidurl);
        } else {
            $quotchar = '';
        }
        $cidurl = substr(trim($cidurl), 4);
        $match_str = '/\{.*?\}\//';
        $str_rep = '';
        $cidurl = preg_replace($match_str, $str_rep, $cidurl);
        $linkurl = self::find_ent_id($cidurl, $message);
        /*
         * in case of non-safe cid links $httpurl should be replaced by a sort of
         * unsafe link image
         */
        /**
         * This is part of a fix for Outlook Express 6.x generating
         * cid URLs without creating content-id headers.
         * These images are
         * not part of the multipart/related html mail. The html contains
         * <img src="cid:{some_id}/image_filename.ext"> references to
         * attached images with as goal to render them inline although
         * the attachment disposition property is not inline.
         */
        if (empty($linkurl)) {
            if (preg_match('/{.*}\//', $cidurl)) {
                $cidurl = preg_replace('/{.*}\//', '', $cidurl);
                if (! empty($cidurl)) {
                    $linkurl = self::find_ent_id($cidurl, $message);
                }
            }
        }
        return $linkurl;
    }

    /**
     * This function checks attribute values for entity-encoded values
     * and returns them translated into 8-bit strings so we can run
     * checks on them.
     *
     * @param $attvalue A
     *            string to run entity check against.
     * @return Nothing, modifies a reference value.
     */
    private static function _defang(&$attvalue)
    {
        /**
         * Skip this if there aren't ampersands or backslashes.
         */
        if (strpos($attvalue, '&') === false && strpos($attvalue, '\\') === false) {
            return;
        }
        $m = false;
        do {
            $m = false;
            $m = $m || self::_deent($attvalue, '/\&#0*(\d+);*/s');
            $m = $m || self::_deent($attvalue, '/\&#x0*((\d|[a-f])+);*/si', true);
            $m = $m || self::_deent($attvalue, '/\\\\(\d+)/s', true);
        } while ($m == true);
        $attvalue = stripslashes($attvalue);
    }

    /**
     * Translates entities into literal values so they can be checked.
     *
     * @param $attvalue the
     *            by-ref value to check.
     * @param $regex the
     *            regular expression to check against.
     * @param $hex whether
     *            the entites are hexadecimal.
     * @return True or False depending on whether there were matches.
     */
    private static function _deent(&$attvalue, $regex, $hex = false)
    {
        $ret_match = false;
        preg_match_all($regex, $attvalue, $matches);
        if (is_array($matches) && sizeof($matches[0]) > 0) {
            $repl = Array();
            for ($i = 0; $i < sizeof($matches[0]); $i ++) {
                $numval = $matches[1][$i];
                if ($hex) {
                    $numval = hexdec($numval);
                }
                $repl{$matches[0][$i]} = chr($numval);
            }
            $attvalue = strtr($attvalue, $repl);
            return true;
        } else {
            return false;
        }
    }

    /**
     * unicode encode
     *
     * @param
     *            $name
     */
    public static function unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0) { // 两个字节的文字
                $str .= '\u' . base_convert(ord($c), 10, 16) . base_convert(ord($c2), 10, 16);
            } else {
                $str .= $c2;
            }
        }
        return $str;
    }

    /**
     * unicode decode
     *
     * @param unknown_type $name            
     */
    public static function unicode_decode($name)
    {
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (! empty($matches)) {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j ++) {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0) {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code) . chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                } else {
                    $name .= $str;
                }
            }
        }
        return $name;
    }
}