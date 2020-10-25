<?php

/**
 * api应用工具类
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 提供api curl的连接调用
 *
 * @package application.core.utils
 * @version $Id$
 * @author banyanCheung <banyan@ibos.com.cn>
 */

namespace application\core\utils;

use application\core\model\Log;
use application\core\utils\HttpClient\exception\ConnectFailedException;

class Api extends System
{

    /**
     * 默认的CURL选项
     *
     * @var array
     */
    protected $curlopt = array(
        CURLOPT_RETURNTRANSFER => true, // 返回页面内容
        CURLOPT_HEADER => false, // 不返回头部
        CURLOPT_ENCODING => "", // 处理所有编码
        CURLOPT_USERAGENT => "spider", // 
        CURLOPT_AUTOREFERER => true, // 自定重定向
        CURLOPT_CONNECTTIMEOUT => 15, // 链接超时时间
        CURLOPT_TIMEOUT => 60, // 超时时间
        CURLOPT_MAXREDIRS => 10, // 超过十次重定向后停止
        CURLOPT_SSL_VERIFYHOST => 0, // 不检查ssl链接
        CURLOPT_SSL_VERIFYPEER => false, //
        CURLOPT_VERBOSE => 1 //
    );

    public static function getInstance($className = __CLASS__)
    {
        return parent::getInstance($className);
    }

    /**
     * 设置curl选项
     *
     * @param array $opt
     */
    public function setOpt($opt)
    {
        if (!empty($opt)) {
            $this->curlopt = $opt + $this->curlopt;
        }
    }

    /**
     * 返回curl默认选项
     *
     * @return array
     */
    public function getOpt()
    {
        return $this->curlopt;
    }

    /**
     * 创建api链接
     *
     * @param string $url 链接地址
     * @param array $param 附件的参数
     * @return string 构造出来的url
     */
    public function buildUrl($url, $param = array())
    {
        $param = http_build_query($param);
        return $url . (strpos($url, '?') ? '&' : '?') . $param;
    }

    /**
     * 获取调用api结果
     *
     * @param string $url api地址
     * @param array $param 如果类型为post时，要提交的参数
     * @param string $type 发送的类型 get or post
     * @return mixed 成功时返回string、错误时返回array
     * @throws ConnectFailedException
     */
    public function fetchResult($url, $param = array(), $type = 'get')
    {
        $opt = $this->getOpt();
        if ($type == 'post') {
            $opt = array(
                    CURLOPT_POST => 1, // 是否post提交数据
                    CURLOPT_POSTFIELDS => $param, // post的值
                ) + $opt;
        } else {
            $url = $this->buildUrl($url, $param);
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, $opt);
        $result = curl_exec($ch);
        if ($result === false) {
            $curlErrorNo = curl_errno($ch);
            $curl_error = curl_error($ch);

            Log::write(array(
                'msg' => sprintf('Curl error no: %d, url: %s', $curlErrorNo, $url),
                'error' => ApiCode::getInstance()->getCurlMsg($curlErrorNo, $curl_error),
                'errno' => $curlErrorNo,
                'trace' => debug_backtrace(),
            ), 'action', 'application.core.utils.Api.fetchResult');

            return array(
                'error' => Ibos::lang('Network error', 'error', array('{code}' => $curlErrorNo)),
                'errno' => $curlErrorNo,
            );
        }
        curl_close($ch);
        return $result;
    }

    //根据扩展名获取mime
    public function getFileMime($ext){
        $mimetypes = array(
            "323" => "text/h323",
            "acx" => "application/internet-property-stream",
            "ai" => "application/postscript",
            "aif" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "asf" => "video/x-ms-asf",
            "asr" => "video/x-ms-asf",
            "asx" => "video/x-ms-asf",
            "au" => "audio/basic",
            "avi" => "video/x-msvideo",
            "axs" => "application/olescript",
            "bas" => "text/plain",
            "bcpio" => "application/x-bcpio",
            "bin" => "application/octet-stream",
            "bmp" => "image/bmp",
            "c" => "text/plain",
            "cat" => "application/vnd.ms-pkiseccat",
            "cdf" => "application/x-cdf",
            "cer" => "application/x-x509-ca-cert",
            "class" => "application/octet-stream",
            "clp" => "application/x-msclip",
            "cmx" => "image/x-cmx",
            "cod" => "image/cis-cod",
            "cpio" => "application/x-cpio",
            "crd" => "application/x-mscardfile",
            "crl" => "application/pkix-crl",
            "crt" => "application/x-x509-ca-cert",
            "csh" => "application/x-csh",
            "css" => "text/css",
            "dcr" => "application/x-director",
            "der" => "application/x-x509-ca-cert",
            "dir" => "application/x-director",
            "dll" => "application/x-msdownload",
            "dms" => "application/octet-stream",
            "doc" => "application/msword",
            "docx" => "application/msword",
            "dot" => "application/msword",
            "dvi" => "application/x-dvi",
            "dxr" => "application/x-director",
            "eps" => "application/postscript",
            "etx" => "text/x-setext",
            "evy" => "application/envoy",
            "exe" => "application/octet-stream",
            "fif" => "application/fractals",
            "flr" => "x-world/x-vrml",
            "flv" => "video/x-flv",
            "f4v" => "application/octet-stream",
            "gif" => "image/gif",
            "gtar" => "application/x-gtar",
            "gz" => "application/x-gzip",
            "h" => "text/plain",
            "hdf" => "application/x-hdf",
            "hlp" => "application/winhlp",
            "hqx" => "application/mac-binhex40",
            "hta" => "application/hta",
            "htc" => "text/x-component",
            "htm" => "text/html",
            "html" => "text/html",
            "htt" => "text/webviewhtml",
            "ico" => "image/x-icon",
            "ief" => "image/ief",
            "iii" => "application/x-iphone",
            "ins" => "application/x-internet-signup",
            "isp" => "application/x-internet-signup",
            "jfif" => "image/pipeg",
            "jpe" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "js" => "application/javascript",
            "json" => "application/json",
            "latex" => "application/x-latex",
            "lha" => "application/octet-stream",
            "lsf" => "video/x-la-asf",
            "lsx" => "video/x-la-asf",
            "lzh" => "application/octet-stream",
            "m13" => "application/x-msmediaview",
            "m14" => "application/x-msmediaview",
            "m3u" => "audio/x-mpegurl",
            'm4a' => "audio/mp4",
            'm4v' => "audio/mp4",
            "man" => "application/x-troff-man",
            "mdb" => "application/x-msaccess",
            "me" => "application/x-troff-me",
            "mht" => "message/rfc822",
            "mhtml" => "message/rfc822",
            "mid" => "audio/mid",
            "mny" => "application/x-msmoney",
            "mov" => "video/quicktime",
            "movie" => "video/x-sgi-movie",
            "mp2" => "video/mpeg",
            "mp3" => "audio/mpeg",
            "mp4" => "video/mp4",
            "mp4v" => "video/mp4",
            "mpa" => "video/mpeg",
            "mpe" => "video/mpeg",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpp" => "application/vnd.ms-project",
            "mpv2" => "video/mpeg",
            "ms" => "application/x-troff-ms",
            "mvb" => "application/x-msmediaview",
            "nws" => "message/rfc822",
            "oda" => "application/oda",
            "ogg" => "audio/ogg",
            "oga" => "audio/ogg",
            "ogv" => "audio/ogg",
            "p10" => "application/pkcs10",
            "p12" => "application/x-pkcs12",
            "p7b" => "application/x-pkcs7-certificates",
            "p7c" => "application/x-pkcs7-mime",
            "p7m" => "application/x-pkcs7-mime",
            "p7r" => "application/x-pkcs7-certreqresp",
            "p7s" => "application/x-pkcs7-signature",
            "pbm" => "image/x-portable-bitmap",
            "pdf" => "application/pdf",
            "pfx" => "application/x-pkcs12",
            "pgm" => "image/x-portable-graymap",
            "pko" => "application/ynd.ms-pkipko",
            "pma" => "application/x-perfmon",
            "pmc" => "application/x-perfmon",
            "pml" => "application/x-perfmon",
            "pmr" => "application/x-perfmon",
            "pmw" => "application/x-perfmon",
            "png" => "image/png",
            "pnm" => "image/x-portable-anymap",
            "pot," => "application/vnd.ms-powerpoint",
            "ppm" => "image/x-portable-pixmap",
            "pps" => "application/vnd.ms-powerpoint",
            "ppt" => "application/vnd.ms-powerpoint",
            "pptx" => "application/vnd.ms-powerpoint",
            "prf" => "application/pics-rules",
            "ps" => "application/postscript",
            "pub" => "application/x-mspublisher",
            "qt" => "video/quicktime",
            "ra" => "audio/x-pn-realaudio",
            "ram" => "audio/x-pn-realaudio",
            "ras" => "image/x-cmu-raster",
            "rgb" => "image/x-rgb",
            "rmi" => "audio/mid",
            "roff" => "application/x-troff",
            "rtf" => "application/rtf",
            "rtx" => "text/richtext",
            "scd" => "application/x-msschedule",
            "sct" => "text/scriptlet",
            "setpay" => "application/set-payment-initiation",
            "setreg" => "application/set-registration-initiation",
            "sh" => "application/x-sh",
            "shar" => "application/x-shar",
            "sit" => "application/x-stuffit",
            "snd" => "audio/basic",
            "spc" => "application/x-pkcs7-certificates",
            "spl" => "application/futuresplash",
            "src" => "application/x-wais-source",
            "sst" => "application/vnd.ms-pkicertstore",
            "stl" => "application/vnd.ms-pkistl",
            "stm" => "text/html",
            "svg" => "image/svg+xml",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc",
            "swf" => "application/x-shockwave-flash",
            "t" => "application/x-troff",
            "tar" => "application/x-tar",
            "tcl" => "application/x-tcl",
            "tex" => "application/x-tex",
            "texi" => "application/x-texinfo",
            "texinfo" => "application/x-texinfo",
            "tgz" => "application/x-compressed",
            "tif" => "image/tiff",
            "tiff" => "image/tiff",
            "tr" => "application/x-troff",
            "trm" => "application/x-msterminal",
            "tsv" => "text/tab-separated-values",
            "txt" => "text/plain",
            "uls" => "text/iuls",
            "ustar" => "application/x-ustar",
            "vcf" => "text/x-vcard",
            "vrml" => "x-world/x-vrml",
            "wav" => "audio/wav",
            "wcm" => "application/vnd.ms-works",
            "wdb" => "application/vnd.ms-works",
            "webm" => "video/webm",
            "webmv" => "video/webm",
            "wks" => "application/vnd.ms-works",
            "wmf" => "application/x-msmetafile",
            "wps" => "application/vnd.ms-works",
            "wri" => "application/x-mswrite",
            "wrl" => "x-world/x-vrml",
            "wrz" => "x-world/x-vrml",
            "xaf" => "x-world/x-vrml",
            "xbm" => "image/x-xbitmap",
            "xla" => "application/vnd.ms-excel",
            "xlc" => "application/vnd.ms-excel",
            "xlm" => "application/vnd.ms-excel",
            "xls" => "application/vnd.ms-excel",
            "xlsx" => "application/vnd.ms-excel",
            "xlt" => "application/vnd.ms-excel",
            "xlw" => "application/vnd.ms-excel",
            "xof" => "x-world/x-vrml",
            "xpm" => "image/x-xpixmap",
            "xwd" => "image/x-xwindowdump",
            "z" => "application/x-compress",
            "zip" => "application/zip"
        );
        //代码 或文本浏览器输出
        $text = array('oexe','inc','inf','csv','log','asc','tsv');
        $code = array("abap","abc","as","ada","adb","htgroups","htpasswd","conf","htaccess","htgroups",
            "htpasswd","asciidoc","asm","ahk","bat","cmd","c9search_results","cpp","c","cc","cxx","h","hh","hpp",
            "cirru","cr","clj","cljs","CBL","COB","coffee","cf","cson","Cakefile","cfm","cs","css","curly","d",
            "di","dart","diff","patch","Dockerfile","dot","dummy","dummy","e","ejs","ex","exs","elm","erl",
            "hrl","frt","fs","ldr","ftl","gcode","feature",".gitignore","glsl","frag","vert","go","groovy",
            "haml","hbs","handlebars","tpl","mustache","hs","hx","html","htm","xhtml","erb","rhtml","ini",
            "cfg","prefs","io","jack","jade","java","js","jsm","json","jq","jsp","jsx","jl","tex","latex",
            "ltx","bib","lean","hlean","less","liquid","lisp","ls","logic","lql","lsl","lua","lp","lucene",
            "Makefile","GNUmakefile","makefile","OCamlMakefile","make","md","markdown","mask","matlab",
            "mel","mc","mush","mysql","nc","nix","m","mm","ml","mli","pas","p","pl","pm","pgsql","php","phtml",
            "ps1","praat","praatscript","psc","proc","plg","prolog","properties","proto","py","r","Rd",
            "Rhtml","rb","ru","gemspec","rake","Guardfile","Rakefile","Gemfile","rs","sass","scad","scala",
            "scm","rkt","scss","sh","bash",".bashrc","sjs","smarty","tpl","snippets","soy","space","sql",
            "styl","stylus","svg","tcl","tex","txt","textile","toml","twig","ts","typescript","str","vala",
            "vbs","vb","vm","v","vh","sv","svh","vhd","vhdl","xml","rdf","rss","log",
            "wsdl","xslt","atom","mathml","mml","xul","xbl","xaml","xq","yaml","yml","htm",
            "xib","storyboard","plist","csproj");
        if (array_key_exists($ext,$mimetypes)){
            return $mimetypes[$ext];
        }else{
            if(in_array($ext,$text) || in_array($ext,$code)){
                return "text/plain";
            }
            return 'application/octet-stream';
        }
    }
}
