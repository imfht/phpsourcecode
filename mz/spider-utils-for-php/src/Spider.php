<?php

namespace ZV;

class Spider
{
    /**
     * @var string last redirect url
     */
    private $url = '';

    /**
     * connect/read timeout
     *
     * @var int
     */
    private $timeout = 10;
    /**
     * @var int record last response code
     */
    private $responseCode = -1;

    /**
     * @var array last response header
     */
    private $responseHeader = [];
    /**
     * response body
     *
     * @var string
     */
    private $body = '';

    /**
     * last curl error
     *
     * @var array
     */
    private $lastError = [];

    /**
     * set header
     *
     * @var array
     */
    private $header = [
        'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)',
    ];


    /**
     * spider constructor.
     *
     * @param string $url
     * @param array  $header
     */
    public function __construct($url = '', $header = [], $timeout = 0) {
        $this->init($url, $header, $timeout);
    }

    /**
     * @param string $url
     * @param array  $header
     */
    public function init($url, $header = [], $timeout = 0) {
        $this->url     = $url;
        $this->header  = array_merge($this->header, $header);
        $this->timeout = $timeout ?: 10;
        return $this;
    }

    /**
     * @return int
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     * @param int $responseCode
     */
    public function setResponseCode($responseCode) {
        $this->responseCode = $responseCode;
    }

    /**
     * @return array
     */
    public function getResponseHeader() {
        return $this->responseHeader;
    }

    /**
     * @param array $responseHeader
     */
    public function setResponseHeader($responseHeader) {
        $this->responseHeader = $responseHeader;
    }

    /**
     * @return array
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param array $header
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getLastError() {
        return $this->lastError;
    }


    /**
     * remove all html tag
     *
     * @param $html
     *
     * @return mixed
     */
    public static function noHtml($html) {
        return static::regReplace($html, ['<(*)>' => '']);
    }

    /**
     * convert html to text
     *
     * @param $html
     *
     * @return mixed|string
     */
    public static function html2txt($html) {
        $html = strtr($html, [
            '&nbsp;'  => ' ',
            '&rdquo;' => '”',
            '&ldquo;' => '“',
            //"\xA0" => ' ',
        ]);
        $html = pregReplace('/^[\s\t]+/is', ' ', $html);
        $html = pregReplace('#<?xml[\s\S]*?>#is', '', $html);
        $html = pregReplace('#<!--[\s\S]*?-->#is', '', $html);
        $html = pregReplace('#<!doc[\s\S]*?>#is', '', $html);
        $html = pregReplace('#<(head|script|iframe|frame|noscript|noframes|option|style)[\s\S]*?</\1>#is', '', $html);
        $html = pregReplace('#<(br|hr|li|ol|ul|dl|h\d|dd|dt|center|form|table|tr|marquee|div|pre|p|blockquote).*?>#is', "\n", $html);
        $html = static::strip_tags($html);
        // decode entities
        $html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');
        $html = pregReplace('#([\r\n]\s+[\r\n])+#is', "\n", $html);

        $html = str_replace(["\r", "\n\n"], "\n", $html);
        while (strpos($html, "\n\n") !== FALSE) {
            $html = str_replace("\n\n", "\n", $html);
        }
        return $html;
    }

    /**
     * alias for strip_tag / fix strip_tag unicode bug
     *
     * @param        $text
     * @param string $tags
     *
     * @return mixed
     */
    public static function strip_tags($text, $tags = '') {
        preg_match_all('/<([\w\-\.]+)[\s]*\/?[\s]*>/si', strtolower(trim($tags)), $tags);
        $tags     = array_unique($tags[1]);
        $searches = [];
        static $block_set = [
            'head'     => 1,
            'script'   => 1,
            'iframe'   => 1,
            'frame'    => 1,
            'noscript' => 1,
            'noframes' => 1,
            'option'   => 1,
            'style'    => 1,
        ];
        //注释
        $searches[] = '#<!--[\s\S]*?-->#is';
        //ie 判断
        $searches[] = '#<\!--[if[^\]]*?\]>[\S\s]<\!\[endif\]-->#is';
        if (is_array($tags) && count($tags) > 0) {
            $line_tags = $block_tags = '';
            foreach ($tags as $tag) {
                if (!$tag) {
                    continue;
                }
                if (isset($block_set[$tag])) {
                    unset($block_set[$tag]);
                }
                $line_tags .= $tag . '|';
            }
            $block_set  = array_keys($block_set);
            $block_tags = implode('|', $block_set);
            if ($block_tags) {
                $searches[] = '#<(' . $block_tags . ')\b[\s\S]*?</\1>#is';
            }
            if ($line_tags) {
                $line_tags  = substr($line_tags, 0, -1);
                $searches[] = '#<(?!(?:' . $line_tags . ')|\/(?:' . $line_tags . ')\b)[^>]*?>#si';
            }
            return pregReplace($searches, '', $text);
        } else {
            $searches[] = '#<(' . implode('|', $block_set) . ')\b[\s\S]*?</\1>#is';
            $searches[] = '#<\/?[^>]*?>#si';
            return pregReplace($searches, '', $text);
        }
    }

    /**
     * cut string from $start to $end
     *
     * @param        $html
     * @param string $start
     * @param string $end
     *
     * @return string
     */
    public static function cut($html, $start = '', $end = '') {
        if ($start) {
            $html = stristr($html, $start, FALSE);
            $html = substr($html, strlen($start));
        }
        $end && $html = stristr($html, $end, TRUE);
        return $html;
    }
    //
    /*
    */
    /**
     * mask match string:
     *
     * spider::maskMatch('123abc123', '123(*)123') = abc
     * spider::maskMatch('abc123', '(*)123') = abc
     * spider::maskMatch('123abcabc', '(*)abc') = 123
     * spider::maskMatch('123abcdef', '(*)abc', true) = 123abc
     *
     * @param            $html
     * @param            $pattern
     * @param bool|false $returnFull
     *
     * @return string
     */
    public static function maskMatch($html, $pattern, $returnFull = FALSE) {
        $part = explode('(*)', $pattern);
        if (count($part) == 1) {
            return '';
        } else {
            if ($part[0] && $part[1]) {
                $res = static::cut($html, $part[0], $part[1]);
                if ($res) {
                    return $returnFull ? $part[0] . $res . $part[1] : $res;
                }
            } else {
                //pattern=xxx(*)
                if ($part[0]) {
                    if (strpos($html, $part[0]) !== FALSE) {
                        $html = explode($part[0], $html);
                        if ($html[1]) {
                            return $returnFull ? $part[0] . $html[1] : $html[1];
                        }
                    }
                } elseif ($part[1]) {
                    //pattern=(*)xxx
                    if (strpos($html, $part[1]) !== FALSE) {
                        $html = explode($part[1], $html);
                        if ($html[0]) {
                            return $returnFull ? $html[0] . $part[1] : $html[0];
                        }
                    }
                }
            }
            return '';
        }
    }

    //
    /*
        //replace single mode
    */
    /**
     * replace by array replace_from  => replace_to (support reg & str & mask)
     *
     *  example :
     * spider::regReplace('abcdefg', 'e(*)') = abcd
     * spider::regReplace('abcdefg', array('#e.+$#is'=> 'hij')) = abcdhij
     * spider::regReplace('abcd123', array('#\d+#s'=> '')) = abcd
     * spider::regReplace('abcd123', array('cd'=> 'dc')) = abdc123
     * //replace multi pattern
     * spider::regReplace('abcd123', array(
     * 'cd'=> 'dc',
     * '1(*)'=> '321',
     * '#\d+#s'=> '111',
     * )) = abdc111
     *
     * @param $html
     * @param $patterns
     *
     * @return mixed
     */
    public static function regReplace($html, $patterns) {
        if (!is_array($patterns)) {
            $patterns = [$patterns => ''];
        }
        foreach ($patterns as $search => $replace) {
            // mask mastch replace
            if (strpos($search, '(*)') !== FALSE) {
                while ($searchhtml = static::maskMatch($html, $search, TRUE)) {
                    if ($searchhtml) {
                        $html = str_replace($searchhtml, $replace, $html);
                        continue;
                    }
                    break;
                }
            } elseif (preg_match('/^([\#\/\|\!\@]).+\\1([ismSMI]+)?$/is', $search)) {
                //regexp replace
                $html = pregReplace($search, $replace, $html);
            } else {
                //str replace
                $html = str_replace($search, $replace, $html);
            }
        }
        return $html;
    }


    //match
    /*
    */
    /**
     * match string from pattern
     *
     *
     * $url = 'http://www.sogou.com/web?query='.urlencode($key).'&ie=utf8';
     * $html = spider::fetch_url($url, '', array('Referer'=>'http://www.sogou.com/'));
     *
     * #useage 1
     * // get title by regexp
     * $list = spider::match($html, array('listblock' => array('title' => '/<title>(.*?)<\/title>/is',)));
     * // get title by mask match
     * $list = spider::match($html, array('listblock' => array('title2' => '<title>(*)</title>',)));
     *
     *
     * #useage 2
     *
     * $keywordlist = spider::match($html, array('list'=>array(
     * 'cut' => '相关搜索</caption>(*)</tr></table>',
     * 'pattern' => '#id="sogou_\d+_\d+">(?<key>[^>]*?)</a>#is',
     * )));
     * $newarr = array();
     * foreach($keywordlist['list'] as $key=>$val){
     * $newarr[$val['key']] = array('key'=>$val['key']);
     * }
     *
     * @param       $html
     * @param       $patterns
     * @param array $option
     *
     * @return array
     */
    public static function match($html, $patterns) {
        $resultList = [];
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }
        //pre process =replace
        if (isset($patterns['_replace'])) {
            if (!is_array($patterns['_replace'])) {
                $patterns['_replace'] = [$patterns['_replace'] => ''];
            }
            $html = static::regReplace($html, $patterns['_replace']);
            unset($patterns['_replace']);
        }
        $extractor = NULL;

        foreach ($patterns as $key => $val) {
            $value = NULL;
            if (!is_array($val)) {
                $val = [$val];
            }
            if (isset($val['pattern'])) {
                //pre process
                $matchHtml = static::matchPreProcess($html, $val);
                //support multi pattern
                if (!is_array($val['pattern'])) {
                    $val['pattern'] = [$val['pattern']];
                }
                //regexp match it
                foreach ($val['pattern'] as $pattern) {
                    if (strpos($pattern, '(*)') === FALSE) {
                        $value = static::regMatch($matchHtml, $pattern);
                        if ($value) {
                            if (is_string($value)) {
                                static::matchProcess($value, $val['process']);
                            } elseif (is_array($value)) {
                                // process each field
                                foreach ($value as &$data) {
                                    foreach ($data as $value_field => &$valueItem) {
                                        if (isset($val['process'][$value_field])) {
                                            static::matchProcess($valueItem, $val['process'][$value_field]);
                                        }
                                    }
                                }
                                unset($valueItem, $data);
                            }
                            break;
                        }
                    } else {
                        // match field by maskMatch

                        $value = static::maskMatch($matchHtml, $pattern);

                        if ($value) {
                            static::matchProcess($value, $val['process']);
                            break;
                        }
                    }
                }
            } elseif (isset($val['selector'])) {

            } else {
                //multi mask match pattern
                foreach ($val as &$patternArray) {
                    if (!is_array($patternArray) || !isset($patternArray['pattern'])) {
                        $patternArray = [
                            ['pattern' => [$patternArray]]
                        ];
                    }
                    $findValue = FALSE;
                    foreach ($patternArray as $patterInfo) {
                        if (!isset($patterInfo['pattern'])) {
                            continue;
                        }
                        //pre process
                        $matchHtml = static::matchPreProcess($html, $val);
                        //not html to match then match next pattern
                        if (!$matchHtml) {
                            continue;
                        }

                        foreach ($patterInfo['pattern'] as $pattern) {
                            // string match
                            $value = static::strMatch($html, $pattern);
                            if ($value) {
                                $findValue = TRUE;
                                // when find processor
                                static::matchProcess($value, $patterInfo['process'] ?? FALSE);
                                break;
                            }
                            //or match next pattern
                        }
                    }
                    if ($findValue) {
                        break;
                    }
                }
            }
            $resultList[$key] = $value;
        }
        return $resultList;
    }

    /**
     * after match value process
     *
     * @param $value
     * @param $process
     */
    private static function matchProcess(&$value, $process) {
        if ($process) {
            if (!is_array($process)) {
                $process = [$process];
            }
            foreach ($process as $index => $processor) {
                if ($processor instanceof \Closure) {
                    $value = $processor($value);
                } else {
                    $param  = explode('|', $processor);
                    $method = $param[0];
                    $param  = array_slice($param, 1);
                    if ($param) {
                        foreach ($param as &$val) {
                            if (strpos($val, '_VALUE_') !== FALSE) {
                                $val = strtr($val, ['_VALUE_' => $value]);
                            }
                        }
                    } else {
                        $param = [$value];
                    }
                    unset($val);
                    $value = call_user_func_array($method, $param);
                }
            }
        }
    }

    /**
     * before match value process
     *
     * @param $html
     * @param $patternInfo
     *
     * @return mixed|string
     */
    private static function matchPreProcess($html, &$patternInfo) {
        $matchHtml = $html;
        // cut it short and run faster
        if (isset($patternInfo['cut'])) {
            // support multi patterns
            if (!is_array($patternInfo['cut'])) {
                $patternInfo['cut'] = [$patternInfo['cut']];
            }
            // until find match html
            foreach ($patternInfo['cut'] as $pattern) {
                $matchHtml = static::maskMatch($html, $pattern);
                if ($matchHtml) {
                    break;
                }
            }
        }
        //replace html
        if (isset($patternInfo['_replace'])) {
            if (!is_array($patternInfo['_replace'])) {
                $patternInfo['_replace'] = [$patternInfo['_replace'] => ''];
            }
            $matchHtml = static::regReplace($matchHtml, $patternInfo['_replace']);
        }
        return $matchHtml;
    }


    /**
     * string match
     *
     * spider::strMatch('123', '1(*)3') = 2
     * spider::strMatch('123', '1(\d+)3') = 2
     *
     * @param $str
     * @param $pattern
     * @param $dom
     * @param $option
     *
     * @return mixed|string
     */
    public static function strMatch($str, $pattern) {
        $value = '';
        //array mask pattern
        if (strpos($pattern, '(*)') !== FALSE) {
            $value = static::maskMatch($str, $pattern);
        } elseif (strpos($pattern, '(') !== FALSE) {
            //has reg match field
            preg_match_all($pattern, $str, $value);
            //return first match group
            $value = $value[1][0] ?? FALSE;
        }
        return $value;
    }

    /**
     * match by regexp
     *
     * @param     $html
     * @param     $reg
     * @param int $returnIndex
     *
     * @return array|bool
     */
    public static function regMatch($html, $reg, $returnIndex = -1) {
        $list = [];
        preg_match_all($reg, $html, $list);
        // has group name
        if (strpos($reg, '(?<') !== FALSE) {
            static::filterList($list);
            if ($returnIndex == -1) {
                return $list;
            } else {
                return $list[$returnIndex];
            }
        } else {
            return $list[1][0] ?? FALSE;
        }
    }

    /**
     * filter number index in list
     *
     * @param $list
     */
    private static function filterList(&$list) {
        foreach ($list as $key => $val) {
            if (is_numeric($key)) {
                unset($list[$key]);
            }
        }
        $keys = array_keys($list);
        foreach ($keys as $idx => $key) {
            if (is_numeric($key)) continue;
            foreach ($list[$key] as $index => $value) {
                $list[$index][$key] = $value;
            }
            unset($list[$key]);
        }
    }

    /**
     * relative path to absolute
     *
     * @param $baseUrl
     * @param $targetUrl
     *
     * @return string
     */
    public static function absUrl($baseUrl, $targetUrl) {
        if (!$targetUrl) {
            return '';
        }
        $baseInfo = parse_url($baseUrl);
        // start with //
        if (strpos($targetUrl, '//') === 0) {
            $targetUrl = $baseInfo['scheme'] . ':' . $targetUrl;
        }
        $targetInfo = parse_url($targetUrl);
        if (isset($targetInfo['scheme'])) {
            return $targetUrl;
        }
        $url = $baseInfo['scheme'] . '://' . $baseInfo['host'];
        if (!isset($targetInfo['path'])) {
            $targetInfo['path'] = '';
        }
        if (substr($targetInfo['path'], 0, 1) == '/') {
            $path = $targetInfo['path'];
        } else {
            //fixed only ?
            if (empty($targetInfo['path'])) {
                $path = ($baseInfo['path']);
            } else {
                // fix dirname
                if (substr($baseInfo['path'], -1) == '/') {
                    $path = $baseInfo['path'] . $targetInfo['path'];
                } else {
                    $path = (dirname($baseInfo['path']) . '/') . $targetInfo['path'];
                }
            }
        }
        $rst       = [];
        $pathArray = explode('/', $path);
        if (!$pathArray[0]) {
            $rst[] = '';
        }
        foreach ($pathArray as $key => $dir) {
            if ($dir == '..') {
                if (end($rst) == '..') {
                    $rst[] = '..';
                } elseif (!array_pop($rst)) {
                    $rst[] = '..';
                }
            } elseif (strlen($dir) > 0 && $dir != '.') {
                $rst[] = $dir;
            }
        }
        if (!end($pathArray)) {
            $rst[] = '';
        }
        $url .= implode('/', $rst);
        $url = str_replace('\\', '/', $url);
        $url = str_ireplace('&amp;', '&', $url);
        return $url . (isset($targetInfo['query']) ? '?' . $targetInfo['query'] : '');
    }


    /**
     * HTTP GET
     *
     * @param       $url
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function get($timeout = -1) {
        return $this->fetch($this->url, '', $this->header, $timeout > 0 ? $timeout : $this->timeout);
    }

    /**
     * HTTP POST
     *
     * @param       $url
     * @param       $post
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function post($post, $timeout = -1) {
        return $this->fetch($this->url, $post, $this->header, $timeout > 0 ? $timeout : $this->timeout);
    }

    /**
     * HTTP PUT
     *
     * @param       $url
     * @param       $post
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function put($post, $timeout = -1) {
        return $this->fetch($this->url, $post, array_merge($this->header, ['method' => 'PUT']), $timeout);
    }

    /**
     * HTTP DELETE
     *
     * @param       $url
     * @param       $post
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function delete($post, $timeout = -1) {
        return $this->fetch($this->url, $post, array_merge($this->header, ['method' => 'DELETE']), $timeout);
    }

    /**
     * HTTP PATCH
     *
     * @param       $url
     * @param       $post
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function patch($post, $timeout = -1) {
        return $this->fetch($this->url, $post, array_merge($this->header, ['method' => 'PATCH']), $timeout);
    }

    /**
     * HTTP DELETE
     *
     * @param       $url
     * @param       $post
     * @param array $headers
     * @param int   $timeout
     * @param int   $deep
     *
     * @return $this
     * @throws Exception
     */
    public function head($timeout = -1) {
        return $this->fetch($this->url, '', array_merge($this->header, ['method' => 'HEAD']), $timeout);
    }

    /**
     * fetch url
     *
     * @param        $url
     * @param string $post
     * @param array  $headers
     * @param int    $timeout
     * @param int    $deep
     *
     * @return $this
     * @throws Exception
     */
    public function fetch($url, $post = '', $headers = [], $timeout = -1) {
        // reset variable
        $this->responseCode = -1;
        $this->lastError    = [];
        $this->setBody('');


        $timeout = $timeout > 0 ? $timeout : $this->timeout;
        if (!is_array($headers)) {
            $headers = [];
        }
        // headers
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : $this->header['User-Agent'];
        $urlInfo    = parse_url($url);
        $host       = $urlInfo['host'];
        $https      = $urlInfo['scheme'] == 'https' ? TRUE : FALSE;
        $charset    = '';
        $defHeaders = [
            'Accept'          => '*/*',
            'User-Agent'      => $user_agent,
            'Accept-Encoding' => 'gzip, deflate',
            'Host'            => $host,
            //'Connection'      => 'Close',
            //'Accept-Language' => 'zh-cn',
        ];
        //charset support
        if ($headers['charset'] ?? FALSE) {
            $charset = $headers['charset'];
        }
        unset($headers['charset']);
        // merge headers
        $defHeaders = array_change_key_case($defHeaders);
        if (is_array($headers) && $headers) {
            $headers = array_change_key_case($headers);
            foreach ($headers as $key => $val) {
                $defHeaders[$key] = $val;
            }
        }

        static $ch;
        if (is_null($ch)) {
            $ch = curl_init();
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        if ($https) {
            // ssl request host by ip(PHP5.5+)
            if ($defHeaders['host'] && preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $urlInfo['host'])) {
                $port = $urlInfo['port'] ?: 443;
                curl_setopt($ch, CURLOPT_RESOLVE, [
                    $defHeaders['host'] . ":" . $port . ":" . $host,
                ]);
                //replace host
                $new_url = pregReplace('#' . preg_quote($urlInfo['host']) . '#is', $defHeaders['host'], $url, 1);
                curl_setopt($ch, CURLOPT_URL, $new_url);
            }
            // ssl cert
            if (isset($defHeaders[CURLOPT_SSLCERT])) {
                $ssl_verifypeer = 1;
                // use certificate ：cert & key pem
                curl_setopt($ch, CURLOPT_SSLCERT, realpath($defHeaders[CURLOPT_SSLCERT]));
                curl_setopt($ch, CURLOPT_SSLKEY, realpath($defHeaders[CURLOPT_SSLKEY]));
                if (isset($defHeaders[CURLOPT_SSLCERTTYPE])) {
                    curl_setopt($ch, CURLOPT_SSLCERTTYPE, $defHeaders[CURLOPT_SSLCERTTYPE]);
                }
                if (isset($defHeaders[CURLOPT_SSLKEYTYPE])) {
                    curl_setopt($ch, CURLOPT_SSLKEYTYPE, $defHeaders[CURLOPT_SSLKEYTYPE]);
                }
                // unset ssl index
                unset($defHeaders[CURLOPT_SSLCERTTYPE], $defHeaders[CURLOPT_SSLCERT], $defHeaders[CURLOPT_SSLKEYTYPE], $defHeaders[CURLOPT_SSLKEY]);
            } elseif (isset($defHeaders[CURLOPT_SSLCERT])) {
                $ssl_verifypeer = 1;
                // single cert mode
                curl_setopt($ch, CURLOPT_SSLCERT, realpath($defHeaders[CURLOPT_SSLCERT]));
                // unset ssl index
                unset($defHeaders[CURLOPT_SSLCERT]);
            } else {
                $ssl_verifypeer = 0;
            }
            // support cainfo
            if (isset($defHeaders[CURLOPT_CAINFO])) {
                curl_setopt($ch, CURLOPT_CAINFO, realpath($defHeaders[CURLOPT_CAINFO]));
                unset($defHeaders[CURLOPT_CAINFO]);
            }
            // support capath
            if (isset($defHeaders[CURLOPT_CAPATH])) {
                curl_setopt($ch, CURLOPT_CAPATH, realpath($defHeaders[CURLOPT_CAPATH]));
                unset($defHeaders[CURLOPT_CAPATH]);
            }
            //
            if (isset($defHeaders[CURLOPT_SSL_VERIFYPEER])) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $defHeaders[CURLOPT_SSL_VERIFYPEER]);
                unset($defHeaders[CURLOPT_SSL_VERIFYPEER]);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verifypeer);
            }
            // check verifyhost
            if (isset($defHeaders[CURLOPT_SSL_VERIFYHOST])) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $defHeaders[CURLOPT_SSL_VERIFYHOST]);
                unset($defHeaders[CURLOPT_SSL_VERIFYHOST]);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
        }
        // off safe upload
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            @curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // support method
        if (isset($defHeaders['method']) && $defHeaders['method']) {
            switch (strtoupper($defHeaders['method'])) {
                case 'HEAD':
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                    curl_setopt($ch, CURLOPT_NOBODY, 1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
                    break;
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    $post_is_array = is_array($post);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_is_array ? json_encode($post, 320) : $post);
                    if ($post_is_array) {
                        curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type: application/json');
                    }
                    break;
            }
            unset($defHeaders['method']);
        } else {
            $post && curl_setopt($ch, CURLOPT_POST, 1);
            // post
            if ($post) {
                //find out post file use multipart/form-data
                $is_multi_part = 0;
                if (is_array($post)) {
                    $is_curl_file = version_compare(phpversion(), '5.5.0') >= 0 && class_exists('CURLFile') ? TRUE : FALSE;
                    foreach ($post as $index => $value) {
                        if ($value[0] == '@') {
                            if ($is_curl_file) {
                                // upload file by @file;mime
                                @list($path, $mime) = explode(';', substr($value, 1), 2);
                                $post[$index] = new \CURLFile(realpath($path), $mime ?: '');
                            }
                            $is_multi_part = 2;
                        }
                    }
                } else {
                    //is string
                    $is_multi_part = 1;
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $is_multi_part ? $post : http_build_query($post));
            }
        }

        // out ip for header set
        if (isset($defHeaders['ip'])) {
            curl_setopt($ch, CURLOPT_INTERFACE, $defHeaders['ip']);
            unset($defHeaders['ip']);
        }

        // gzip compress
        if (isset($defHeaders['accept-encoding'])) {
            curl_setopt($ch, CURLOPT_ENCODING, $defHeaders['accept-encoding']);
            unset($defHeaders['cccept-encoding']);
        }


        // proxy
        /*
            'proxy' =>array(
                'type' => '', //HTTP or SOCKET
                'host' => 'ip:port',
                'auth' => 'BASIC:user:pass',
            );
            'proxy' => 'http://1.1.1.1:80',
            'proxy' => 'socks5://1.1.1.1:80',
        */

        if (isset($defHeaders['auth']) && $defHeaders['auth']) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $defHeaders['auth']);
        }

        if (isset($defHeaders['proxy']) && $defHeaders['proxy']) {
            if (is_array($defHeaders['proxy'])) {
                $proxy_type = strtoupper($defHeaders['proxy']['type']) == 'SOCKET' ? CURLPROXY_SOCKS5 : CURLPROXY_HTTP;
                curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
                list($proxy_host, $proxy_port) = explode(':', $defHeaders['proxy']['host']);
                $proxy_port = $proxy_port ?: $defHeaders['proxy']['port'];
                curl_setopt($ch, CURLOPT_PROXY, $proxy_host);
                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);

                // proxy auth
                if ($headers['proxy']['auth']) {
                    list($auth_type, $auth_user, $auth_pass) = explode(':', $headers['proxy']['auth']);
                    $auth_type = $auth_type == 'NTLM' ? CURLAUTH_NTLM : CURLAUTH_BASIC;
                    curl_setopt($ch, CURLOPT_PROXYAUTH, $auth_type);
                    $user = "" . $auth_user . ":" . $auth_pass . "";
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user);
                }
            } else {
                // proxy by string
                if (strpos($defHeaders['proxy'], '://') === FALSE) {
                    // auto fill http proxy
                    $defHeaders['proxy'] = 'http://' . $defHeaders['proxy'];
                }
                curl_setopt($ch, CURLOPT_PROXY, $defHeaders['proxy']);
            }
        }
        unset($defHeaders['proxy'], $defHeaders['auth']);

        // set version 1.0
        // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        // max redirect times
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        // build curl headers
        $header_array = [];
        foreach ($defHeaders as $key => $val) {
            $key            = implode('-', array_map(function ($item) {
                return ucfirst($item);
            }, explode('-', $key)));
            $header_array[] = $key . ': ' . $val;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->lastError = [curl_errno($ch), curl_strerror(curl_errno($ch))];
        }
        //for debug request header
        //@formatter:off
        //print_r($header_array);$info = curl_getinfo($ch, CURLINFO_HEADER_OUT );print_r($info);echo is_array($post) ? http_build_query($post) : $post;//exit;
        //@formatter:on
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->setResponseCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $this->url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        // split header/body
        $header = substr($data, 0, $header_size);
        $data   = substr($data, $header_size);
        //extract last response header,by lower case
        $this->setResponseHeader(static::extractHeader($header));
        $header = explode("\r\n\r\n", trim($header));
        $header = array_pop($header);
        // match charset & convert charset
        if (!$charset) {
            preg_match('#Content-Type:\s*([\w/]+)(;\s+charset\s*=\s*([\w-]+))?#is', $header, $charsetmatch);
            if (isset($charsetmatch[3])) {
                $charset = $charsetmatch[3];
            }
        }
        $body = static::covertHtmlCharset($data, $charset);
        $this->setBody($body);

        // reuse ch
        static $unsetValues = [
            CURLOPT_HEADERFUNCTION   => NULL,
            CURLOPT_WRITEFUNCTION    => NULL,
            CURLOPT_READFUNCTION     => NULL,
            CURLOPT_PROGRESSFUNCTION => NULL,
        ];
        curl_setopt_array($ch, $unsetValues);
        curl_reset($ch);
        return $this;
    }

    /**
     * extract last response header
     *
     * @param $header
     *
     * @return array
     */
    private static function extractHeader($header) {
        $lines  = explode("\n", $header);
        $result = [];
        foreach ($lines as $line) {
            @list($key, $val) = explode(":", $line, 2);
            $key = trim(strtolower($key));
            switch ($key) {
                case 'set-cookie':
                    if (!isset($result['cookie'])) {
                        $result['cookie'] = [];
                    }
                    $result['cookie'][] = $val;
                    break;
                default:
                    if ($key && $val) {
                        $result[$key] = trim($val);
                    }
                    break;
            }
        }
        return $result;
    }

    /**
     * convert html charset (detect html charset)
     *
     * @param        $html
     * @param        $charset
     * @param string $tocharset
     *
     * @return string
     */
    private static function covertHtmlCharset($html, $charset, $tocharset = 'utf-8') {
        if ($charset == 'bin') {
            return $html;
        }
        $detectCharset = '';
        //html file
        if ($charset) {
            // charset is set
            $detectCharset = $charset;
        } else {
            if (stripos($html, '<meta') !== FALSE) {
                if (strpos($html, 'charset=') !== FALSE) {
                    $head = static::maskMatch(strtolower($html), '(*)</head>');
                    if ($head) {
                        $head = strtolower($head);
                        $head = static::regReplace($head, [
                            '<script(*)/script>' => '',
                            '<style(*)/style>'   => '',
                            '<link(*)>'          => '',
                            "\r"                 => '',
                            "\n"                 => '',
                            "\t"                 => '',
                            " "                  => '',
                            "'"                  => ' ',
                            "\""                 => ' ',
                        ]);
                        preg_match_all('/charset\s*?=\s*?([\-\w]+)/', $head, $matches);
                    } else {
                        preg_match_all('/<meta[^>]*?content=("|\'|).*?\bcharset=([\w\-]+)\b/is', $html, $matches);
                    }

                    if (isset($matches[1][0]) && !empty($matches[1][0])) {
                        $detectCharset = $matches[1][0];
                    }
                }
            }
            //xml file
            if (stripos($html, '<?xml') !== FALSE) {
                //<?xml version="1.0" encoding="UTF-8"
                if (stripos($html, 'encoding=') !== FALSE) {
                    $head = static::maskMatch($html, '<' . '?xml(*)?' . '>');
                    preg_match_all('/encoding=["\']?([-\w]+)/is', $head, $matches);
                    if (isset($matches[1][0]) && !empty($matches[1][0])) {
                        $detectCharset = $matches[1][0];
                    }
                }
            }
        }
        // alias
        if (in_array(strtolower($detectCharset), ['gb2312', 'iso-8859-1'])) {
            $detectCharset = 'gbk';
        }
        if ($detectCharset) {
            return mb_convert_encoding($html, $tocharset, $detectCharset);
        } else {
            return $html;
        }
    }

    /**
     * get response by json
     *
     * @param string $type array|object
     *
     * @return mixed
     */
    public function getJson($type = 'array') {
        return json_decode($this->body, $type == 'array' ? 1 : 0);
    }

    /**
     * get response by xml
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getXml($type = 'array') {
        libxml_disable_entity_loader(TRUE);
        $xmlData = simplexml_load_string($this->body, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xmlData), $type == 'array' ? 1 : 0);
    }
}

?>