<?php

/**
 * Secure
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Support;
use Madphp\Config as Config;

class Secure
{
    /**
     * Random Hash for protecting URLs
     * @var string
     * @access protected
     */
    protected static $xssHash = '';

    /**
     * List of never allowed strings
     * @var array
     * @access protected
     */
    protected static $neverAllowedStr = array(
        'document.cookie'   => '[removed]',
        'document.write'    => '[removed]',
        '.parentNode'       => '[removed]',
        '.innerHTML'        => '[removed]',
        'window.location'   => '[removed]',
        '-moz-binding'      => '[removed]',
        '<!--'              => '&lt;!--',
        '-->'               => '--&gt;',
        '<![CDATA['         => '&lt;![CDATA[',
        '<comment>'         => '&lt;comment&gt;'
    );

    /**
     * List of never allowed regex replacement
     * @var array
     * @access protected
     */
    protected static $neverAllowedRegex = array(
        'javascript\s*:',
        'expression\s*(\(|&\#40;)', // CSS and IE
        'vbscript\s*:', // IE, surprise!
        'Redirect\s+302',
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
    );

    /**
     * replace safe string
     * @var array
     * @access protected
     */
    protected static $safeReplace = array(
        '%20' => '',
        '%27' => '',
        '%2527' => '',
        '*' => '',
        "'" => '',
        ';' => '',
        '{' => '',
        '}' => '',
        '\\' => '',
        '"' => '&quot;',
        '<' => '&lt;',
        '>' => '&gt;',
    );

    /**
     * Random Hash for Cross Site Request Forgery Protection Cookie
     *
     * @var string
     * @access protected
     */
    protected static $csrfHash           = '';
    /**
     * Expiration time for Cross Site Request Forgery Protection Cookie
     * Defaults to two hours (in seconds)
     *
     * @var int
     * @access protected
     */
    protected static $csrfExpire         = 7200;
    /**
     * Token name for Cross Site Request Forgery Protection Cookie
     *
     * @var string
     * @access protected
     */
    protected static $csrfTokenName     = 'csrfToken';
    /**
     * Cookie name for Cross Site Request Forgery Protection Cookie
     *
     * @var string
     * @access protected
     */
    protected static $csrfCookieName    = 'csrfHash';
    
    /**
     * Constructor
     */
    public function __construct() { }
    
    /**
     * Safe replace function
     * @param  $string
     * @return string
     */
    public static function safeReplace($string)
    {
        $string = str_replace(array_keys(self::$safeReplace), array_values(self::$safeReplace), $string);
        return $string;
    }

    /**
     * XSS Clean
     * @param   mixed   string or array
     * @param   bool
     * @return  string
     */
    public static function xssClean($data, $isImage = FALSE)
    {
        // Is the string an array?
        if (is_array($data)) {
            while (list($key) = each($data)) {
                $data[$key] = self::xssClean($data[$key]);
            }
            return $data;
        }

        // Remove Invisible Characters
        $data = remove_invisible_characters($data);

        // Validate Entities in URLs
        $data = self::validateEntities($data);

        // $data = rawurldecode($data);

        $data = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", "self::convertAttribute", $data);

        $data = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", "self::decodeEntity", $data);

        // Remove Invisible Characters Again!
        $data = remove_invisible_characters($data);

        /*
         * Convert all tabs to spaces
         *
         * This prevents strings like this: ja  vascript
         * NOTE: we deal with spaces between characters later.
         * NOTE: preg_replace was found to be amazingly slow here on
         * large blocks of data, so we use str_replace.
         */
        if (strpos($data, "\t") !== FALSE) {
            $data = str_replace("\t", ' ', $data);
        }
        
        // Capture converted string for later comparison
        $converted_string = $data;

        // Remove Strings that are never allowed
        $data = self::doNeverAllowed($data);

        /*
         * Makes PHP tags safe
         * Note: XML tags are inadvertently replaced too:
         * <?xml
         * But it doesn't seem to pose a problem.
         */
        if ($isImage === TRUE) {
            // Images have a tendency to have the PHP short opening and
            // closing tags every so often so we skip those and only
            // do the long opening tags.
            $data = preg_replace('/<\?(php)/i', "&lt;?\\1", $data);
        } else {
            $data = str_replace(array('<?', '?'.'>'),  array('&lt;?', '?&gt;'), $data);
        }

        /*
         * Compact any exploded words
         * This corrects words like:  j a v a s c r i p t
         * These words are compacted back to their correct state.
         */
        $words = array(
            'javascript', 'expression', 'vbscript', 'script', 'base64',
            'applet', 'alert', 'document', 'write', 'cookie', 'window'
        );

        foreach ($words as $word) {
            $temp = '';

            for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
                $temp .= substr($word, $i, 1)."\s*";
            }

            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto"
            $data = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', "self::compactExplodedWords", $data);
        }

        /*
         * Remove disallowed Javascript in links or img tags
         * We used to do some version comparisons and use of stripos for PHP5,
         * but it is dog slow compared to these simplified non-capturing
         * preg_match(), especially if the pattern exists in the string
         */
        do {
            $original = $data;

            if (preg_match("/<a/i", $data)) {
                $data = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", "self::jsLinkRemoval", $data);
            }

            if (preg_match("/<img/i", $data)) {
                $data = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", "self::jsImgRemoval", $data);
            }

            if (preg_match("/script/i", $data) OR preg_match("/xss/i", $data)) {
                $data = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $data);
            }
        } while ($original != $data);

        unset($original);

        // Remove evil attributes such as style, onclick and xmlns
        $data = self::removeEvilAttributes($data, $isImage);

        /*
         * Sanitize naughty HTML elements
         * If a tag containing any of the words in the list
         * below is found, the tag gets converted to entities.
         * So this: <blink>
         * Becomes: &lt;blink&gt;
         */
        $naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
        $data = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', "self::sanitizeNaughtyHtml", $data);

        /*
         * Sanitize naughty scripting elements
         *
         * Similar to above, only instead of looking for
         * tags it looks for PHP and JavaScript commands
         * that are disallowed.  Rather than removing the
         * code, it simply converts the parenthesis to entities
         * rendering the code un-executable.
         *
         * For example: eval('some code')
         * Becomes:     eval&#40;'some code'&#41;
         */
        $data = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $data);

        // Final clean up
        // This adds a bit of extra precaution in case
        // something got through the above filters
        $data = self::doNeverAllowed($data);

        /*
         * Images are Handled in a Special Way
         * - Essentially, we want to know that after all of the character
         * conversion is done whether any unwanted, likely XSS, code was found.
         * If not, we return TRUE, as the image is clean.
         * However, if the string post-conversion does not matched the
         * string post-removal of XSS, then it fails, as there was unwanted XSS
         * code found and removed/changed during processing.
         */

        if ($isImage === TRUE) {
            return ($data == $converted_string) ? TRUE: FALSE;
        }

        writeLog('debug', "XSS Filtering completed");
        return $data;
    }

    /**
     * Validate URL entities
     * Called by xssClean()
     * @param   string
     * @return  string
     */
    protected static function validateEntities($str)
    {
        // Protect GET variables in URLs
        // 901119URL5918AMP18930PROTECT8198
        $str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', self::xssHash()."\\1=\\2", $str);

        /*
         * Validate standard character entities
         * Add a semicolon if missing.  We do this to enable
         * the conversion of entities to ASCII later.
         */
        $str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);

        /*
         * Validate UTF16 two byte encoding (x00)
         * Just as above, adds a semicolon if missing.
         */
        $str = preg_replace('#(&\#x?)([0-9A-F]+);?#i', "\\1\\2;", $str);

        // Un-Protect GET variables in URLs
        $str = str_replace(self::xssHash(), '&', $str);

        return $str;
    }

    /**
     * Random Hash for protecting URLs
     * @return  string
     */
    public static function xssHash()
    {
        if (self::$xssHash == '') {
            mt_srand();
            self::$xssHash = md5(time() + mt_rand(0, 1999999999));
        }
        return self::$xssHash;
    }

    /**
     * Attribute Conversion
     * Used as a callback for XSS Clean
     * @param   array
     * @return  string
     */
    protected static function convertAttribute($match)
    {
        return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
    }

    /**
     * HTML Entity Decode Callback
     * Used as a callback for XSS Clean
     * @param   array
     * @return  string
     */
    protected static function decodeEntity($match)
    {
        $charset = Config::get('app', 'charset', 'utf-8');
        return self::entityDecode($match[0], strtoupper($charset));
    }

    /**
     * HTML Entities Decode
     * This function is a replacement for html_entity_decode()
     * The reason we are not using html_entity_decode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly.  html_entity_decode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     * @param   string
     * @param   string
     * @return  string
     */
    public static function entityDecode($str, $charset = 'UTF-8')
    {
        if (stristr($str, '&') === FALSE) {
            return $str;
        }

        $str = html_entity_decode($str, ENT_COMPAT, $charset);
        $str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
        return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
    }

    /**
     * Do Never Allowed
     * A utility function for xssClean()
     * @param   string
     * @return  string
     */
    protected static function doNeverAllowed($str)
    {
        $str = str_replace(array_keys(self::$neverAllowedStr), self::$neverAllowedStr, $str);

        foreach (self::$neverAllowedRegex as $regex) {
            $str = preg_replace('#'.$regex.'#is', '[removed]', $str);
        }
        return $str;
    }

    /**
     * Compact Exploded Words
     * Callback function for xssClean() to remove whitespace from
     * things like j a v a s c r i p t
     * @param   type
     * @return  type
     */
    protected static function compactExplodedWords($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
    }

    /**
     * JS Link Removal
     * Callback function for xssClean() to sanitize links
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on link-heavy strings
     *
     * @param   array
     * @return  string
     */
    protected static function jsLinkRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
                '',
                self::filterAttributes(str_replace(array('<', '>'), '', $match[1]))
            ),
            $match[0]
        );
    }

    /**
     * JS Image Removal
     *
     * Callback function for xssClean() to sanitize image tags
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings
     *
     * @param   array
     * @return  string
     */
    protected static function jsImgRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                '',
                self::filterAttributes(str_replace(array('<', '>'), '', $match[1]))
            ),
            $match[0]
        );
    }

    /**
     * Filter Attributes
     * Filters tag attributes for consistency and safety
     * @param   string
     * @return  string
     */
    protected static function filterAttributes($str)
    {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }
        return $out;
    }

    /*
     * Remove Evil HTML Attributes (like evenhandlers and style)
     *
     * It removes the evil attribute and either:
     *  - Everything up until a space
     *      For example, everything between the pipes:
     *      <a |style=document.write('hello');alert('world');| class=link>
     *  - Everything inside the quotes
     *      For example, everything between the pipes:
     *      <a |style="document.write('hello'); alert('world');"| class="link">
     *
     * @param string $str The string to check
     * @param boolean $isImage TRUE if this is an image
     * @return string The string with the evil attributes removed
     */
    protected static function removeEvilAttributes($str, $isImage)
    {
        // All javascript event handlers (e.g. onload, onclick, onmouseover), style, and xmlns
        $evil_attributes = array('on\w*', 'style', 'xmlns', 'formaction');

        if ($isImage === TRUE) {
            /*
             * Adobe Photoshop puts XML metadata into JFIF images, 
             * including namespacing, so we have to allow this for images.
             */
            unset($evil_attributes[array_search('xmlns', $evil_attributes)]);
        }

        do {
            $count = 0;
            $attribs = array();

            // find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            preg_match_all('/('.implode('|', $evil_attributes).')\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is', $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // find occurrences of illegal attribute strings without quotes
            preg_match_all('/('.implode('|', $evil_attributes).')\s*=\s*([^\s>]*)/is', $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // replace illegal attribute strings that are inside an html tag
            if (count($attribs) > 0) {
                $str = preg_replace('/(<?)(\/?[^><]+?)([^A-Za-z<>\-])(.*?)('.implode('|', $attribs).')(.*?)([\s><]?)([><]*)/i', '$1$2 $4$6$7$8', $str, -1, $count);
            }
        } while ($count);

        return $str;
    }

    /**
     * Sanitize Naughty HTML
     * Callback function for xssClean() to remove naughty HTML elements
     * @param   array
     * @return  string
     */
    protected static function sanitizeNaughtyHtml($matches)
    {
        // encode opening brace
        $str = '&lt;'.$matches[1].$matches[2].$matches[3];
        // encode captured opening or closing brace to prevent recursive vectors
        $str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

        return $str;
    }
    
    /**
     * csrf 防护初始化
     * 在 RequestBase 构造函数中调用，所以不能使用 Request 对象的方法
     * @return  void
     */
    public static function csrfInit()
    {
        // 是否开启 CSRF 防护
        if (Config::get('request', 'csrfProtection', TRUE) === TRUE) {
            // CSRF config
            foreach (array('csrfExpire', 'csrfTokenName', 'csrfCookieName') as $key) {
                if (FALSE !== ($val = Config::get('request', $key, FALSE))) {
                    self::$$key = $val;
                }
            }

            // 添加 cookie 前缀
            if (Config::get('request', 'cookiePrefix', null) !== null) {
                self::$csrfCookieName = Config::get('request', 'cookiePrefix').self::$csrfCookieName;
            }

            // 设置 CSRF hash
            self::csrfSetHash();
        }
    }
    
    /**
     * 跨站请求防护
     * 在 RequestBase 构造函数中调用，所以不能使用 Request 对象的方法
     * @return  void
     */
    public static function csrfVerify()
    {
        self::csrfInit();
        // 非 POST 请求设置 csrf hash cookie
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            self::csrfSetCookie();
            return;
        }

        // $_POST数组或$_COOKIE数组中不存在 csrf hash
        if (!isset($_POST[self::$csrfTokenName], $_COOKIE[self::$csrfCookieName])) {
            self::csrfShowError();
        }

        // $_POST数组和$_COOKIE数组中 csrf hash 值不匹配
        if ($_POST[self::$csrfTokenName] != $_COOKIE[self::$csrfCookieName]) {
            self::csrfShowError();
        }

        // 从 $_POST 数组中移除 csrf token
        unset($_POST[self::$csrfTokenName]);

        // 重新设置 csrf hash 值和 csrf cookie 值
        unset($_COOKIE[self::$csrfCookieName]);
        self::csrfSetHash();
        self::csrfSetCookie();

        writeLog('debug', 'CSRF token verified');
    }

    /**
     * Set Cross Site Request Forgery Protection Cookie
     * 在 RequestBase 构造函数中调用，所以不能使用 Request 对象的方法
     * @return  void
     */
    public static function csrfSetCookie()
    {
        writeLog('debug', "Secure::csrfSetCookie() Call.");

        $expire = time() + self::$csrfExpire;
        $secure_cookie = (Config::get('request', 'cookieSecure', TRUE) === TRUE) ? 1 : 0;

        if ($secure_cookie && (empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) === 'off')) {
            return FALSE;
        }

        setcookie(self::$csrfCookieName, self::$csrfHash, $expire, Config::get('request', 'cookiePath', '/'), Config::get('request', 'cookieDomain', ''), $secure_cookie);
        writeLog('debug', "CRSF cookie Set");
    }

    /**
     * Show CSRF Error
     *
     * @return  void
     */
    public static function csrfShowError()
    {
        throw new \Exception("The action you have requested is not allowed.");
    }

    /**
     * Set Cross Site Request Forgery Protection Cookie
     * 在 RequestBase 构造函数中调用，所以不能使用 Request 对象的方法
     * @return  string
     */
    protected static function csrfSetHash()
    {
        writeLog('info', "Secure::csrfSetHash(1) Call.");
        
        if (self::$csrfHash == '') {
            $cookieName = self::$csrfCookieName;
            // csrf cookie 存在且是32位md5字符串，csrf hash 的值取 csrf cookie 值
            if (isset($_COOKIE[$cookieName]) && preg_match('#^[0-9a-f]{32}$#iS', $_COOKIE[$cookieName]) === 1) {
                return self::$csrfHash = $_COOKIE[$cookieName];
            }
            return self::$csrfHash = md5(uniqid(rand(), TRUE));
        }
        return self::$csrfHash;
    }

    /**
     * 获取 CSRF Hash 值
     *
     * @return  string  self::$csrfHash
     */
    public static function getCsrfHash()
    {
        return self::$csrfHash;
    }
}

/* End of file Secure.php */