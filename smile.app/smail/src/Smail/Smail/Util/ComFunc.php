<?php
namespace Smail\Util;

class ComFunc
{

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
    public static function parseAddress($address, $max = 0)
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
    public static function parseArray($read, &$i)
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
     * Parses a string in an imap response.
     * String starts with " or { which means it
     * can handle double quoted strings and literal strings
     *
     * @param
     *            $read
     * @param
     *            $i
     */
    public static function parseString($read, &$i)
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
     * Encode lines with 8bit-characters to quote-printable
     *
     * @param unknown_type $line            
     */
    public static function quoted_printable_encode($line)
    {
        $qp_table = array(
            '=00',
            '=01',
            '=02',
            '=03',
            '=04',
            '=05',
            '=06',
            '=07',
            '=08',
            '=09',
            '=0A',
            '=0B',
            '=0C',
            '=0D',
            '=0E',
            '=0F',
            '=10',
            '=11',
            '=12',
            '=13',
            '=14',
            '=15',
            '=16',
            '=17',
            '=18',
            '=19',
            '=1A',
            '=1B',
            '=1C',
            '=1D',
            '=1E',
            '=1F',
            '_',
            '!',
            '"',
            '#',
            '$',
            '%',
            '&',
            "'",
            '(',
            ')',
            '*',
            '+',
            ',',
            '-',
            '.',
            '/',
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            ':',
            ';',
            '<',
            '=3D',
            '>',
            '=3F',
            '@',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '[',
            '\\',
            ']',
            '^',
            '=5F',
            '',
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '{',
            '|',
            '}',
            '~',
            '=7F',
            '=80',
            '=81',
            '=82',
            '=83',
            '=84',
            '=85',
            '=86',
            '=87',
            '=88',
            '=89',
            '=8A',
            '=8B',
            '=8C',
            '=8D',
            '=8E',
            '=8F',
            '=90',
            '=91',
            '=92',
            '=93',
            '=94',
            '=95',
            '=96',
            '=97',
            '=98',
            '=99',
            '=9A',
            '=9B',
            '=9C',
            '=9D',
            '=9E',
            '=9F',
            '=A0',
            '=A1',
            '=A2',
            '=A3',
            '=A4',
            '=A5',
            '=A6',
            '=A7',
            '=A8',
            '=A9',
            '=AA',
            '=AB',
            '=AC',
            '=AD',
            '=AE',
            '=AF',
            '=B0',
            '=B1',
            '=B2',
            '=B3',
            '=B4',
            '=B5',
            '=B6',
            '=B7',
            '=B8',
            '=B9',
            '=BA',
            '=BB',
            '=BC',
            '=BD',
            '=BE',
            '=BF',
            '=C0',
            '=C1',
            '=C2',
            '=C3',
            '=C4',
            '=C5',
            '=C6',
            '=C7',
            '=C8',
            '=C9',
            '=CA',
            '=CB',
            '=CC',
            '=CD',
            '=CE',
            '=CF',
            '=D0',
            '=D1',
            '=D2',
            '=D3',
            '=D4',
            '=D5',
            '=D6',
            '=D7',
            '=D8',
            '=D9',
            '=DA',
            '=DB',
            '=DC',
            '=DD',
            '=DE',
            '=DF',
            '=E0',
            '=E1',
            '=E2',
            '=E3',
            '=E4',
            '=E5',
            '=E6',
            '=E7',
            '=E8',
            '=E9',
            '=EA',
            '=EB',
            '=EC',
            '=ED',
            '=EE',
            '=EF',
            '=F0',
            '=F1',
            '=F2',
            '=F3',
            '=F4',
            '=F5',
            '=F6',
            '=F7',
            '=F8',
            '=F9',
            '=FA',
            '=FB',
            '=FC',
            '=FD',
            '=FE',
            '=FF'
        );
        // are there "forbidden" characters in the string?
        for ($i = 0; $i < strlen($line) && ord($line[$i]) <= 127; $i ++);
        if ($i < strlen($line)) { // yes, there are. So lets encode them!
            $from = $i;
            for ($to = strlen($line) - 1; ord($line[$to]) <= 127; $to --);
            // lets scan for the start and the end of the to be encoded _words_
            for (; $from > 0 && $line[$from] != ' '; $from --);
            if ($from > 0)
                $from ++;
            for (; $to < strlen($line) && $line[$to] != ' '; $to ++);
            // split the string into the to be encoded middle and the rest
            $begin = substr($line, 0, $from);
            $middle = substr($line, $from, $to - $from);
            $end = substr($line, $to);
            // ok, now lets encode $middle...
            $newmiddle = "";
            for ($i = 0; $i < strlen($middle); $i ++)
                $newmiddle .= $qp_table[ord($middle[$i])];
                // now we glue the parts together...
            $line = $begin . '=?ISO-8859-15?Q?' . $newmiddle . '?=' . $end;
        }
        return $line;
    }

    /**
     * sowe new webbrowsers send things like &auml; instead of � in their
     * requests.
     * It's bad, but now I have to convert them to normal
     * characters...
     *
     * @param unknown_type $text            
     */
    public static function html2text($text)
    {
        $text = str_replace("&auml;", "�", $text);
        $text = str_replace("&ouml;", "�", $text);
        $text = str_replace("&uuml;", "�", $text);
        $text = str_replace("&Auml;", "�", $text);
        $text = str_replace("&Ouml;", "�", $text);
        $text = str_replace("&Uuml;", "�", $text);
        $text = str_replace("&szlig;", "�", $text);
        return $text;
    }

    /**
     * wrap the text
     *
     * @param unknown_type $text            
     * @param unknown_type $wrap            
     * @param unknown_type $break            
     */
    public static function textwrap($text, $wrap = 80, $break = "\n")
    {
        $len = strlen($text);
        if ($len > $wrap) {
            $h = ''; // massaged text
            $lastWhite = 0; // position of last whitespace char
            $lastChar = 0; // position of last char
            $lastBreak = 0; // position of last break
                            // while there is text to process
            while ($lastChar < $len) {
                $char = substr($text, $lastChar, 1); // get the next character
                                                     // if we are beyond the wrap boundry and there is a place to break
                if (($lastChar - $lastBreak > $wrap) && ($lastWhite > $lastBreak)) {
                    $h .= substr($text, $lastBreak, ($lastWhite - $lastBreak)) . $break;
                    $lastChar = $lastWhite + 1;
                    $lastBreak = $lastChar;
                }
                // You may wish to include other characters as valid whitespace...
                if ($char == ' ' || $char == chr(13) || $char == chr(10)) {
                    $lastWhite = $lastChar; // note the position of the last whitespace
                }
                $lastChar = $lastChar + 1; // advance the last character position by one
            }
            $h .= substr($text, $lastBreak); // build line
        } else {
            $h = $text; // in this case everything can fit on one line
        }
        return $h;
    }

    public static function uudecode($data)
    {
        $d = explode("\n", $data);
        $u = "";
        for ($i = 0; $i < count($d) - 1; $i ++) {}
        $u .= self::uudocode_line($d[$i]);
        return $u;
    }

    /**
     * decodes a block of 7bit-data in uuencoded format to it's original
     * 8bit format.
     * The headerline containing filename and permissions doesn't have to
     * be included.
     *
     * @param unknown_type $line            
     * @return returns the 8bit data as a string
     */
    public static function uudocode_line($line)
    {
        $data = substr($line, 1);
        $length = ord($line[0]) - 32;
        $decoded = "";
        for ($i = 0; $i < (strlen($data) >> 2); $i ++) {
            $pack = substr($data, $i << 2, 4);
            $upack = "";
            $bitmaske = 0;
            for ($o = 0; $o < 4; $o ++) {
                $g = ((ord($pack[3 - $o]) - 32));
                if ($g == 64)
                    $g = 0;
                $bitmaske = $bitmaske | ($g << (6 * $o));
            }
            $schablone = 255;
            for ($o = 0; $o < 3; $o ++) {
                $c = ($bitmaske & $schablone) >> ($o << 3);
                $schablone = ($schablone << 8);
                $upack = chr($c) . $upack;
            }
            $decoded .= $upack;
        }
        $decoded = substr($decoded, 0, $length);
        return $decoded;
    }

    /**
     * fetch the mime type of the file
     *
     * @param unknown_type $filepath            
     */
    public static function mime_type($filepath)
    {
        if (function_exists('mime_content_type')) {
            $type = mime_content_type($filepath);
            return $type;
        }
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
        );
        $ext = strtolower(array_pop(explode('.', $filepath)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * generate random string
     *
     * @param unknown_type $size            
     * @param unknown_type $chars            
     * @param unknown_type $flags            
     */
    public static function generateRandomString($size, $chars, $flags = 0)
    {
        if ($flags & 0x1) {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($flags & 0x2) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($flags & 0x4) {
            $chars .= '0123456789';
        }
        if (($size < 1) || (strlen($chars) < 1)) {
            return '';
        }
        $String = '';
        $j = strlen($chars) - 1;
        while (strlen($String) < $size) {
            $String .= $chars{mt_rand(0, $j)};
        }
        return $String;
    }

    /**
     * function clean_crlf - change linefeeds and newlines to legal characters
     *
     * The SMTP format only allows CRLF as line terminators.
     * This function replaces illegal teminators with the correct terminator.
     *
     * @param
     *            string &$s string to clean linefeeds on
     *            
     * @return void
     */
    public static function clean_crlf(&$s)
    {
        $s = str_replace("\r\n", "\n", $s);
        $s = str_replace("\r", "\n", $s);
        $s = str_replace("\n", "\r\n", $s);
        return strlen($s);
    }

    /**
     * convert the charset of the text
     *
     * @param unknown_type $str            
     * @param unknown_type $to_encoding            
     * @param unknown_type $from_encoding            
     * @param unknown_type $default_charset            
     */
    public static function sm_mb_convert_encoding($str, $to_encoding, $from_encoding)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $to_encoding, $from_encoding);
        }
        return $str;
    }

    public static function quoteimap($str)
    {
        // FIXME use this performance improvement (not changing because this is STABLE branch): return str_replace(array('\\', '"'), array('\\\\', '\\"'), $str);
        return preg_replace("/([\"\\\\])/", "\\\\$1", $str);
    }

    /**
     * 转化为可读大小
     *
     * @param
     *            $bytes
     */
    public static function show_readable_size($bytes)
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

    private static function parseEmail(&$body, $Email_RegExp_Match)
    {
        $sbody = $body;
        $addresses = array();
        /* Find all the email addresses in the body */
        while (preg_match('/' . $Email_RegExp_Match . '/i', $sbody, $regs)) {
            $addresses[$regs[0]] = $regs[0];
            $start = strpos($sbody, $regs[0]) + strlen($regs[0]);
            $sbody = substr($sbody, $start);
        }
        /* Return number of unique addresses found */
        return count($addresses);
    }

    /**
     * Undocumented - complain, then patch.
     */
    private static function replaceBlock(&$in, $replace, $start, $end)
    {
        $begin = substr($in, 0, $start);
        $end = substr($in, $end, strlen($in) - $end);
        $in = $begin . $replace . $end;
    }

    /**
     * Finds first occurrence of 8bit data in the string
     *
     * Function finds first 8bit symbol or html entity that represents 8bit character.
     * Search start is defined by $offset argument. Search ends at $maxlength position.
     * If $maxlength is not defined or bigger than provided string, search ends when
     * string ends.
     *
     * Check returned data type in order to avoid confusion between bool(false)
     * (not found) and int(0) (first char in the string).
     * 
     * @param string $haystack            
     * @param integer $offset            
     * @param integer $maxlength            
     * @return mixed integer with first 8bit character position or boolean false
     * @since 1.5.2 and 1.4.7
     */
    private static function strpos_8bit($haystack, $offset = 0, $maxlength = false)
    {
        $ret = false;
        if ($maxlength === false || strlen($haystack) < $maxlength) {
            $maxlength = strlen($haystack);
        }
        for ($i = $offset; $i < $maxlength; $i ++) {
            /* rh7-8 compatibility. don't use full 8bit range in regexp */
            if (preg_match('/[\200-\237]|\240|[\241-\377]/', $haystack[$i])) {
                /* we have 8bit char. stop here and return position */
                $ret = $i;
                break;
            } elseif ($haystack[$i] == '&') {
                $substring = substr($haystack, $i);
                /**
                 * 1.
                 * look for "&#(decimal number);" where decimal_number is bigger than 127
                 * 2. look for "&x(hexadecimal number);", where hex number is bigger than x7f
                 * 3. look for any html character entity that is not 7bit html special char. Use
                 * own sq_get_html_translation_table() function with 'utf-8' character set in
                 * order to get all html entities.
                 */
                if ((preg_match('/^&#(\d+);/', $substring, $match) && $match[1] > 127) || (preg_match('/^&x([0-9a-f]+);/i', $substring, $match) && $match[1] > "\x7f") || (preg_match('/^&([a-z]+);/i', $substring, $match) && ! in_array($match[0], get_html_translation_table(HTML_SPECIALCHARS)) && in_array($match[0], sq_get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'utf-8')))) {
                    $ret = $i;
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     * Parses a body and converts all found URLs to clickable links.
     *
     * @param
     *            string body the body to process, by ref
     * @return void
     */
    public static function parseUrl(&$body)
    {
        $url_parser_poss_ends = array(
            ' ',
            "\n",
            "\r",
            '<',
            '>',
            ".\r",
            ".\n",
            '.&nbsp;',
            '&nbsp;',
            ')',
            '(',
            '&quot;',
            '&lt;',
            '&gt;',
            '.<',
            ']',
            '[',
            '{',
            '}',
            "\240",
            ', ',
            '. ',
            ",\n",
            ",\r"
        );
        $url_tokens = array(
            'http://',
            'https://',
            'ftp://',
            'telnet:',
            'mailto:',
            'gopher://',
            'news://'
        );
        $IP_RegExp_Match = '\\[?[0-9]{1,3}(\\.[0-9]{1,3}){3}\\]?';
        $Host_RegExp_Match = '(' . $IP_RegExp_Match . '|[0-9a-z]([-.]?[0-9a-z])*\\.[a-z][a-z]+)';
        $Email_RegExp_Match = '[0-9a-z]([-_.+]?[0-9a-z])*(%' . $Host_RegExp_Match . ')?@' . $Host_RegExp_Match;
        $Mailto_Email_RegExp = '[0-9a-z%]([-_.+%]?[0-9a-z])*(%' . $Host_RegExp_Match . ')?@' . $Host_RegExp_Match;
        $MailTo_PReg_Match = '/((?:' . $Mailto_Email_RegExp . ')*)((?:\?(?:to|cc|bcc|subject|body)=[^\s\?&=,()]+)?(?:&amp;(?:to|cc|bcc|subject|body)=[^\s\?&=,()]+)*)/i';
        $start = 0;
        $blength = strlen($body);
        while ($start < $blength) {
            $target_token = '';
            $target_pos = $blength;
            /* Find the first token to replace */
            foreach ($url_tokens as $the_token) {
                $pos = strpos(strtolower($body), $the_token, $start);
                if (is_int($pos) && $pos < $target_pos) {
                    $target_pos = $pos;
                    $target_token = $the_token;
                }
            }
            /* Look for email addresses between $start and $target_pos */
            $check_str = substr($body, $start, $target_pos - $start);
            if (self::parseEmail($check_str, $Email_RegExp_Match)) {
                self::replaceBlock($body, $check_str, $start, $target_pos);
                $blength = strlen($body);
                $target_pos = strlen($check_str) + $start;
            }
            // rfc 2368 (mailto URL)
            if ($target_token == 'mailto:') {
                $target_pos += 7; // skip mailto:
                $end = $blength;
                $mailto = substr($body, $target_pos, $end - $target_pos);
                if (preg_match($MailTo_PReg_Match, $mailto, $regs) && $regs[0] != '') {
                    $mailto_before = $target_token . $regs[0];
                    /**
                     * '+' characters in a mailto URI don't need to be percent-encoded.
                     * However, when mailto URI data is transported via HTTP, '+' must
                     * be percent-encoded as %2B so that when the HTTP data is
                     * percent-decoded, you get '+' back and not a space.
                     */
                    $mailto_params = str_replace("+", "%2B", $regs[10]);
                    if ($regs[1]) { // if there is an email addr before '?', we need to merge it with the params
                        $to = 'to=' . str_replace("+", "%2B", $regs[1]);
                        if (strpos($mailto_params, 'to=') > - 1) // already a 'to='
                            $mailto_params = str_replace('to=', $to . '%2C%20', $mailto_params);
                        else {
                            if ($mailto_params) // already some params, append to them
                                $mailto_params .= '&amp;' . $to;
                            else
                                $mailto_params .= '?' . $to;
                        }
                    }
                    $url_str = preg_replace(array(
                        '/to=/i',
                        '/(?<!b)cc=/i',
                        '/bcc=/i'
                    ), array(
                        'send_to=',
                        'send_to_cc=',
                        'send_to_bcc='
                    ), $mailto_params);
                    $target_pos += strlen($target_pos) - 7;
                }
            } elseif ($target_token != '') { /* If there was a token to replace, replace it */
                /* Find the end of the URL */
                $end = $blength;
                foreach ($url_parser_poss_ends as $val) {
                    $enda = strpos($body, $val, $target_pos);
                    if (is_int($enda) && $enda < $end) {
                        $end = $enda;
                    }
                }
                /* make sure that there are no 8bit chars between $target_pos and suspected end of URL */
                if (! is_bool($first8bit = self::strpos_8bit($body, $target_pos, $end))) {
                    $end = $first8bit;
                }
                /* Extract URL */
                $url = substr($body, $target_pos, $end - $target_pos);
                /* Needed since lines are not passed with \n or \r */
                while (preg_match('/[,.]$/', $url)) {
                    $url = substr($url, 0, - 1);
                    $end --;
                }
                /* Replace URL with HyperLinked Url, requires 1 char in link */
                if ($url != '' && $url != $target_token) {
                    $url_str = "<a href=\"$url\" target=\"_blank\">$url</a>";
                    self::replaceBlock($body, $url_str, $target_pos, $end);
                    $target_pos += strlen($url_str);
                } else {
                    // Not quite a valid link, skip ahead to next chance
                    $target_pos += strlen($target_token);
                }
            }
            /* Move forward */
            $start = $target_pos;
            $blength = strlen($body);
        }
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
    public static function smWordWrap(&$line, $wrap, $charset = null)
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
            $line_len = strlen($beginning_spaces) + sq_strlen($words[$i], $charset) + 2;
            if (isset($words[$i + 1]))
                $line_len += sq_strlen($words[$i + 1], $charset);
            $i ++;
            /* Add more words (as long as they fit) */
            while ($line_len < $wrap && $i < count($words)) {
                $line .= ' ' . $words[$i];
                $i ++;
                if (isset($words[$i]))
                    $line_len += sq_strlen($words[$i], $charset) + 1;
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
     * 显示邮件优先级
     *
     * @param
     *            $priority
     */
    public static function getPriorityStr($priority)
    {
        $priority_level = substr($priority, 0, 1);
        switch ($priority_level) {
            /* Check for a higher then normal priority. */
            case '1':
            case '2':
                $priority_string = '高';
                break;
            /* Check for a lower then normal priority. */
            case '4':
            case '5':
                $priority_string = '低';
                break;
            /* Check for a normal priority. */
            case '3':
            default:
                $priority_level = '3';
                $priority_string = '正常';
                break;
        }
        return $priority_string;
    }
    public static function user_strcasecmp($a, $b)
    {
        return strnatcasecmp($a, $b);
    }
}