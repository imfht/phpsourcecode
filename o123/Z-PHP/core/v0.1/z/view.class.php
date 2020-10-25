<?php
namespace z;

class view
{
    const
    ENCODE_PREFIX = 'z-php-encode.',
    ENCODE_END_CHAR = '.',
    OPTIONS = LIBXML_NSCLEAN + LIBXML_PARSEHUGE + LIBXML_NOBLANKS + LIBXML_NOERROR + LIBXML_HTML_NODEFDTD + LIBXML_ERR_FATAL + LIBXML_COMPACT;
    private static $TAG, $PRE, $SUF, $TPL, $DISPLAY_TPL, $CACHE, $FILE, $PARAMS, $RUN, $CHANGED, $LOCK;
    private static $REPLACE = [];
    private static function replaceEncode($html)
    {
        $i = 0;
        $pre = preg_quote(self::$PRE);
        $suf = preg_quote(self::$SUF);
        $preg0 = "/({$pre}|<\?=)([\s\S]+)({$suf}|\?>)/U";
        $preg1 = '/<\?php([\s\S]+)\?>/U';
        $html = preg_replace_callback($preg0, function ($match) {
            $code = trim($match[2]);
            $i = array_push(self::$REPLACE, "<?php echo {$code};?>") - 1;
            $encode = self::ENCODE_PREFIX . $i . self::ENCODE_END_CHAR;
            return $encode;
        }, $html);

        $html = preg_replace_callback($preg1, function ($match) use (&$i) {
            $code = trim($match[1]);
            $i = array_push(self::$REPLACE, "<?php {$code}?>") - 1;
            $encode = self::ENCODE_PREFIX . $i . self::ENCODE_END_CHAR;
            return $encode;
        }, $html);
        return $html;
    }
    private static function replaceDecode($html)
    {
        $prefix = preg_quote(self::ENCODE_PREFIX);
        $endchar = preg_quote(self::ENCODE_END_CHAR);
        $html = preg_replace_callback("/{$prefix}(\d+){$endchar}/", function ($match) {
            return self::$REPLACE[$match[1]] ?? '';
        }, $html);
        $html = preg_replace('/<\?php\s}\?><\?php\selse/', '<?php }else', $html);
        $html = preg_replace_callback('/(<\?php\s}\?>){2,}/', function ($match) {
            $len = strlen($match[0]) / 9;
            return '<?php ' . str_repeat('}', (int) $len) . '?>';
        }, $html);
        self::$REPLACE = [];
        return $html;
    }
    private static function getTplInfo($name)
    {
        $name = trim($name, '/');
        $info = pathinfo($name);
        if (isset($info['extension'])) {
            $info['fullname'] = $name;
        } else {
            $info['basename'] .= TPL_EXT;
            $info['fullname'] = $name . TPL_EXT;
        }
        return $info;
    }
    public static function GetTpl(string $name, $F = false)
    {
        if (!$name) {
            $file = ROUTE['ctrl'] . '/' . ROUTE['act'] . TPL_EXT;
            $file = P_THEME_ . $file;
        } elseif (isset(self::$FILE[$name])) {
            $file = self::$FILE[$name];
        } elseif (IsFullPath($name)) {
            $info = pathinfo($name);
            $file = isset($info['extension']) ? $name : $name . TPL_EXT;
        } else {
            $info = self::GetTplInfo($name);
            $arr = explode('/', $info['fullname']);
            if (defined($arr[0]) && $file = rtrim(constant($arr[0]), '/')) {
                unset($arr[0]);
                foreach ($arr as $k => $v) {
                    $file .= '/' . (defined($v) ? constant($v) : $v);
                }
            } else {
                switch (count($arr)) {
                    case 1:
                        $file = P_THEME_ . ROUTE['ctrl'] . "/{$info['fullname']}";
                        break;
                    default:
                        $file = P_THEME_ . $info['fullname'];
                }
            }
        }
        if (!is_file($file)) {
            throw new \Exception("file not fond: {$file}");
        }
        self::$FILE[$name] = $file;
        return $file;
    }
    private static function getBlock($file, $key)
    {
        $time = filemtime($file);
        $time > self::$CHANGED && self::$CHANGED = $time;
        $html = self::replaceEncode(file_get_contents($file));
        $html = '<?xml encoding="UTF-8">' . $html;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html, self::OPTIONS);
        2 < $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] && debug::setMsg(1140, $file);
        $nodes = $dom->getElementsByTagName(self::$TAG['template']);
        if ($nodes->length) {
            foreach ($nodes as $k => $v) {
                $name = $v->getAttribute('name');
                $name && self::$TPL[$key][$name] = $v;
            }
        } else {
            throw new \Exception("template error: {$file}");
        }
        return $dom;
    }
    private static function setNodes($name, $key, $parent, $dom)
    {
        if ($parent->attributes->length > 2 && $attrs = self::setAttrs($parent->attributes, $name)) {
            $node = self::$TPL[$key][$name]->cloneNode(true);
            $code = implode(';', $attrs) . ';?';
            $new = $dom->createProcessingInstruction('php', $code);
            $parent->parentNode->insertBefore($new, $parent);
        } else {
            $node = self::$TPL[$key][$name];
        }
        $nodes = $node->childNodes;
        foreach ($nodes as $n) {
            if (self::$TAG['import'] !== $n->nodeName) {
                $new = $dom->importNode($n, true);
                $parent->parentNode->insertBefore($new, $parent);
            }
        }
        $parent->parentNode->removeChild($parent);
    }
    private static function compressHtml($html, $compress)
    {
        switch ($compress) {
            case 1:
                $preg = '/<!--(?!\[|\<if\s)[\S\s]*-->/U';
                $html = preg_replace($preg, '', $html);
                break;
            case 2:
                $preg = ['/<!--(?!\[|\<if\s)[\S\s]*-->|[\n\r]+/U', '/>\s+</U', '/\s{2,}/'];
                $replace = ['', '><', ' '];
                $html = preg_replace($preg, $replace, $html);
                break;
        }
        return $html;
    }
    private static function setAttrs($attrs, $name)
    {
        foreach ($attrs as $attr) {
            if (':' === $attr->name[0]) {
                $n = ltrim($attr->name, ':');
                $v = $attr->value ?: $n;
                $key = '$' . $n;
                $sets[] = "{$key} = {$v} ?? null";
            }
        }
        return $sets ?? false;
    }
    private static function replaceTemplate($dom, $file = null)
    {
        $imports = $dom->getElementsByTagName(self::$TAG['import']);
        if ($imports->length) {
            foreach ($imports as $v) {
                $name = $v->getAttribute('name');
                $f = $v->getAttribute('file');
                $tpl = $f ? self::GetTpl($f) : $file;
                $key = md5($tpl);
                isset(self::$TPL[$key]) || $D = self::getBlock($tpl, $key);
                if (!isset(self::$TPL[$key][$name])) {
                    throw new \Exception("template tagName '{$name}' not exits : {$tpl}");
                }
                self::setNodes($name, $key, $v, $dom);
                self::replaceTemplate($dom);
            }
        }
    }
    private static function getRun($file)
    {
        $arr = explode('/', $file);
        $len = count($arr);
        $name = $arr[$len - 1];
        $run[0] = $arr[$len - 2];
        $run[1] = explode('.', $name)[0];
        return $run;
    }
    public static function Fetch(string $name = '')
    {
        if (self::$DISPLAY_TPL && self::$RUN) {
            $tpl = self::$DISPLAY_TPL;
            $run = self::$RUN;
        } else {
            $tpl = self::GetTpl($name, true);
            $run = self::getRun($tpl);
        }
        2 < $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] && debug::setMsg(1140, $tpl);
        isset(self::$TAG) || self::$TAG = [
            'php' => $GLOBALS['ZPHP_CONFIG']['VIEW']['php_tag'] ?? 'php',
            'import' => $GLOBALS['ZPHP_CONFIG']['VIEW']['import_tag'] ?? 'import',
            'template' => $GLOBALS['ZPHP_CONFIG']['VIEW']['template_tag'] ?? 'template',
        ];
        $run_path = P_RUN_ . THEME;
        $run_file = $run_path . '/' . $run[0] . '-' . $run[1] . '.php';
        $run_time = is_file($run_file) ? filemtime($run_file) : 0;
        if (1 < $GLOBALS['ZPHP_CONFIG']['DEBUG']['level'] || !$run_time) {
            if (!file_exists($run_path) && !MakeDir($run_path, 0755, true)) {
                throw new \Exception("file can not write: {$run_path}");
            }
            self::$PRE = $GLOBALS['ZPHP_CONFIG']['VIEW']['prefix'] ?? '<{';
            self::$SUF = $GLOBALS['ZPHP_CONFIG']['VIEW']['suffix'] ?? '}>';
            self::$CHANGED = filemtime($tpl);
            $flag = '<meta flag="ZPHP-UTF-8" http-equiv="Content-Type" content="text/html; charset=utf-8">';
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $html = '<!DOCTYPE html>' . $flag . self::replaceEncode(file_get_contents($tpl));
            $dom->loadHTML($html, self::OPTIONS);
            self::replaceTemplate($dom, $tpl);
            if (!$run_time || self::$CHANGED > $run_time) {
                if ($compress = $GLOBALS['ZPHP_CONFIG']['VIEW']['compress'] ?? 0) {
                    self::compressCss($dom, $compress[1] ?? $compress);
                    self::compressJavaScript($dom, $compress[2] ?? $compress);
                }
                self::replacePHP($dom);
                $html = $dom->saveHTML();
                $html = str_replace($flag, '', $html);
                $html = self::replaceDecode($html);
                $html = self::compressHtml($html, $compress[0] ?? $compress);
                if (false === file_put_contents($run_file, $html, LOCK_EX)) {
                    throw new \Exception("file can not write:{$run_file}");
                }
            }
        }
        self::$PARAMS && extract(self::$PARAMS);
        self::$TPL = null;
        ob_start() && require $run_file;
        $html = ob_get_contents();
        ob_end_clean();

        if (self::$CACHE) {
            if ($func = self::$LOCK) {
                $func(self::$CACHE[1], $html);
            } elseif (false === file_put_contents(self::$CACHE[1], $html, LOCK_EX)) {
                throw new \Exception('file can not write: ' . self::$CACHE[1]);
            }
        }
        return $html;
    }

    public static function GetCache($time, $name = '', $flag = 0)
    {
        $tpl = self::GetTpl($name, true);
        $run = self::getRun($tpl);
        $cache = self::getCacheFile($flag, $run);
        $html_time = is_file($cache[1]) ? filemtime($cache[1]) : 0;
        if ($html_time + $time >= TIME) {
            return ReadFileSH($cache[1]);
        } else {
            file_exists($dir = dirname($cache[1])) || MakeDir($dir, 0755, true);
            self::$DISPLAY_TPL = $tpl;
            self::$CACHE = $cache;
            self::$RUN = $run;
            switch ($GLOBALS['ZPHP_CONFIG']['DB']['cache_mod'] ?? 0) {
                case 1:
                    $redis = cache::Redis();
                    if ($lock = cache::Rlock($redis, md5($cache[1]))) {
                        self::$LOCK = function ($file, $html) use ($redis, $lock) {
                            if (false === file_put_contents($file, $html, LOCK_EX)) {
                                throw new \Exception('file can not write: ' . $file);
                            }
                            $redis->del($lock);
                        };
                        return false;
                    }
                    break;
                case 2:
                    $mem = cache::Memcached();
                    if ($lock = cache::Mlock($mem, md5($cache[1]))) {
                        self::$LOCK = function ($file, $html) use ($mem, $lock) {
                            if (false === file_put_contents($file, $html, LOCK_EX)) {
                                throw new \Exception('file can not write: ' . $file);
                            }
                            $mem->delete($lock);
                        };
                        return false;
                    }
                    break;
                default:
                    if ('WINDOWS' === ZPHP_OS) {
                        $lock_path = P_CACHE . 'lock_file/';
                        $lock_file = $lock_path . md5($cache[1]);
                        file_exists($lock_path) || MakeDir($lock_path, 0755, true);
                        if (!$h = fopen($lock_file, 'w')) {
                            throw new \Exception('file can not write: ' . $lock_file);
                        }
                        if (flock($h, LOCK_EX)) {
                            clearstatcache(true, $cache[1]);
                            if (!is_file($cache[1]) || filemtime($cache[1]) < TIME) {
                                self::$LOCK = function ($file, $html) use ($h) {
                                    if (false === file_put_contents($file, $html, LOCK_EX)) {
                                        throw new \Exception('file can not write: ' . $file);
                                    }
                                    flock($h, LOCK_UN);
                                    fclose($h);
                                };
                                return false;
                            }
                            flock($h, LOCK_UN);
                        }
                        fclose($h);
                    } else {
                        if (!$h = fopen($cache[1], 'w')) {
                            throw new \Exception('file can not write: ' . $cache[1]);
                        }
                        if (flock($h, LOCK_EX | LOCK_NB)) {
                            self::$LOCK = function ($file, $html) use ($h) {
                                fwrite($h, $html);
                                flock($h, LOCK_UN);
                                fclose($h);
                            };
                            return false;
                        }
                    }
            }
            return ReadFileSH($file);
        }
    }
    private static function compressJavaScript($dom, $compress)
    {
        switch ($compress) {
            case 1:
                $preg = '/\/\*[\s\S]*\*\/|(?<!:|"|\')\/\/.*[\r\n]/U';
                $replace = '';
                break;
            case 2:
                $preg = ['/\/\*[\s\S]*\*\/|(?<!:|"|\')\/\/.*[\r\n]|[\n\r]+/U', '/\s*([\,\;\:\{\}\[\]\(\)\=])\s*/', '/\s{2,}/'];
                $replace = ['', '$1', ' '];
                break;
            default:
                return false;
        }
        $tags = $dom->getElementsByTagName('script');
        if ($tags->length) {
            foreach ($tags as $k => $v) {
                $v->textContent = preg_replace($preg, $replace, $v->textContent);
            }
        }
    }
    private static function compressCss($dom, $compress)
    {
        switch ($compress) {
            case 1:
                $preg = '/\/\*[\s\S]*\*\//U';
                $replace = '';
                break;
            case 2:
                $preg = ['/\/\*[\s\S]*\*\/|[\n\r]+/U', '/\s*([\,\;\:\{\}\[\]\(\)\=])\s*/', '/\s{2,}/'];
                $replace = ['', '$1', ' '];
                break;
            default:
                return false;
        }
        $tags = $dom->getElementsByTagName('style');
        if ($tags->length) {
            foreach ($tags as $k => $v) {
                $v->textContent = preg_replace($preg, $replace, $v->textContent);
            }
        }
    }
    private static function replacePHP($dom)
    {
        $tags = $dom->getElementsByTagName(self::$TAG['php']);
        for ($i = $tags->length - 1; 0 <= $i; --$i) {
            $t = $tags[$i];
            $parent = $t->parentNode;
            if ($t->attributes->length) {
                $a = $t->attributes[0];
                switch ($a->name) {
                    case 'default':
                        $code = 'default:?';
                        $dd = '';
                        break;
                    case 'break':
                    case 'continue':
                        $code = '';
                        $dd = isset($a->value) ? "{$a->name} {$a->value};?" : "{$a->name};?";
                        break;
                    case 'case':
                        $code = 'default' === $a->value ? 'default:?' : "case {$a->value}:?";
                        $dd = 'break' === $t->attributes[1]->name ? 'break;?' : '';
                        break;
                    default:
                        $code = $a->value ? "{$a->name}({$a->value}){?" : "{$a->name}{?";
                        $dd = '}?';
                }
                if ($code) {
                    $new = $dom->createProcessingInstruction('php', $code);
                    $parent->insertBefore($new, $t);
                }
                foreach ($t->childNodes as $c) {
                    $new = $c->cloneNode(true);
                    $parent->insertBefore($new, $t);
                }
                if ($dd) {
                    $new = $dom->createProcessingInstruction('php', $dd);
                    $parent->insertBefore($new, $t);
                }
            } else {
                $new = $dom->createProcessingInstruction('php', "{$t->nodeValue};?");
                $parent->insertBefore($new, $t);
            }
            $parent->removeChild($t);
        }
    }
    private static function getCacheFile($flag, $run)
    {
        if (!$flag) {
            $html_path = P_HTML_ . THEME . '/' . $run[0];
            $html_file = "{$html_path}/{$run[1]}.html";
        } else {
            if (is_array($flag)) {
                foreach ($flag as $k) {
                    $query[$k] = ROUTE['query'][$k] ?? '';
                }
            } else {
                $query = ROUTE['query'];
            }
            $html_path = P_HTML_ . THEME . "/{$run[0]}/{$run[1]}";
            $html_file = "{$html_path}/" . md5(serialize($query)) . '.html';
        }
        return [$html_path, $html_file];
    }
    public static function Display(string $name = '')
    {
        $html = self::Fetch($name);
        echo $html;
    }
    public static function Assign($key, $val)
    {
        self::$PARAMS[$key] = $val;
    }
    public static function GetParams()
    {
        return self::$PARAMS;
    }
}
