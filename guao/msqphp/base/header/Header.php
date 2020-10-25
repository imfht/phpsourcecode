<?php declare (strict_types = 1);
namespace msqphp\base\header;

use msqphp\core\traits;

final class Header
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new HeaderException($message);
    }

    public static function header(string $header, bool $replace = true, int $code = null): void
    {
        header($header, $replace, $code);
    }

    public static function cache(int $time): void
    {
        exit('not complete');
    }

    // 没有缓存
    public static function noCache(): void
    {
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }

    // 状态码
    public static function status(int $code): void
    {
        static $status_info = [
            //  Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            //  Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            //  Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', //  1.
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            //  306 is deprecated but reserved
            307 => 'Temporary Redirect',
            //  Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            //  Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded',
        ];
        isset($status_info[$code]) || static::exception($code . 'html状态码暂未支持');
        header('Status:' . $code . ' ' . $status_info[$code]);
    }

    /**
     * 网页编码类型
     * @param   string  $type     类型
     * @param   string  $charset  语言
     * @return  void
     */
    public static function type(string $type, string $charset = 'utf-8'): void
    {
        $type           = strtolower($type);
        static $headers = [
            'ez'          => 'application/andrew-inset',
            'hqx'         => 'application/mac-binhex40',
            'cpt'         => 'application/mac-compactpro',
            'doc'         => 'application/msword',
            'bin'         => 'application/octet-stream',
            'dms'         => 'application/octet-stream',
            'lha'         => 'application/octet-stream',
            'lzh'         => 'application/octet-stream',
            'exe'         => 'application/octet-stream',
            'final class' => 'application/octet-stream',
            'so'          => 'application/octet-stream',
            'dll'         => 'application/octet-stream',
            'oda'         => 'application/oda',
            'pdf'         => 'application/pdf',
            'ai'          => 'application/postscript',
            'eps'         => 'application/postscript',
            'ps'          => 'application/postscript',
            'smi'         => 'application/smil',
            'smil'        => 'application/smil',
            'mif'         => 'application/vnd.mif',
            'xls'         => 'application/vnd.ms-excel',
            'ppt'         => 'application/vnd.ms-powerpoint',
            'wbxml'       => 'application/vnd.wap.wbxml',
            'wmlc'        => 'application/vnd.wap.wmlc',
            'wmlsc'       => 'application/vnd.wap.wmlscriptc',
            'bcpio'       => 'application/x-bcpio',
            'vcd'         => 'application/x-cdlink',
            'pgn'         => 'application/x-chess-pgn',
            'cpio'        => 'application/x-cpio',
            'csh'         => 'application/x-csh',
            'dcr'         => 'application/x-director',
            'dir'         => 'application/x-director',
            'dxr'         => 'application/x-director',
            'dvi'         => 'application/x-dvi',
            'spl'         => 'application/x-futuresplash',
            'gtar'        => 'application/x-gtar',
            'hdf'         => 'application/x-hdf',
            'js'          => 'application/x-javascript',
            'skp'         => 'application/x-koan',
            'skd'         => 'application/x-koan',
            'skt'         => 'application/x-koan',
            'skm'         => 'application/x-koan',
            'latex'       => 'application/x-latex',
            'nc'          => 'application/x-netcdf',
            'cdf'         => 'application/x-netcdf',
            'sh'          => 'application/x-sh',
            'shar'        => 'application/x-shar',
            'swf'         => 'application/x-shockwave-flash',
            'sit'         => 'application/x-stuffit',
            'sv4cpio'     => 'application/x-sv4cpio',
            'sv4crc'      => 'application/x-sv4crc',
            'tar'         => 'application/x-tar',
            'tcl'         => 'application/x-tcl',
            'tex'         => 'application/x-tex',
            'texinfo'     => 'application/x-texinfo',
            'texi'        => 'application/x-texinfo',
            't'           => 'application/x-troff',
            'tr'          => 'application/x-troff',
            'roff'        => 'application/x-troff',
            'man'         => 'application/x-troff-man',
            'me'          => 'application/x-troff-me',
            'ms'          => 'application/x-troff-ms',
            'ustar'       => 'application/x-ustar',
            'src'         => 'application/x-wais-source',
            'xhtml'       => 'application/xhtml+xml',
            'xht'         => 'application/xhtml+xml',
            'zip'         => 'application/zip',
            'au'          => 'audio/basic',
            'snd'         => 'audio/basic',
            'mid'         => 'audio/midi',
            'midi'        => 'audio/midi',
            'kar'         => 'audio/midi',
            'mpga'        => 'audio/mpeg',
            'mp2'         => 'audio/mpeg',
            'mp3'         => 'audio/mpeg',
            'aif'         => 'audio/x-aiff',
            'aiff'        => 'audio/x-aiff',
            'aifc'        => 'audio/x-aiff',
            'm3u'         => 'audio/x-mpegurl',
            'ram'         => 'audio/x-pn-realaudio',
            'rm'          => 'audio/x-pn-realaudio',
            'rpm'         => 'audio/x-pn-realaudio-plugin',
            'ra'          => 'audio/x-realaudio',
            'wav'         => 'audio/x-wav',
            'pdb'         => 'chemical/x-pdb',
            'xyz'         => 'chemical/x-xyz',
            'bmp'         => 'image/bmp',
            'gif'         => 'image/gif',
            'ief'         => 'image/ief',
            'jpeg'        => 'image/jpeg',
            'jpg'         => 'image/jpeg',
            'jpe'         => 'image/jpeg',
            'png'         => 'image/png',
            'tiff'        => 'image/tiff',
            'tif'         => 'image/tiff',
            'djvu'        => 'image/vnd.djvu',
            'djv'         => 'image/vnd.djvu',
            'wbmp'        => 'image/vnd.wap.wbmp',
            'ras'         => 'image/x-cmu-raster',
            'pnm'         => 'image/x-portable-anymap',
            'pbm'         => 'image/x-portable-bitmap',
            'pgm'         => 'image/x-portable-graymap',
            'ppm'         => 'image/x-portable-pixmap',
            'rgb'         => 'image/x-rgb',
            'xbm'         => 'image/x-xbitmap',
            'xpm'         => 'image/x-xpixmap',
            'xwd'         => 'image/x-xwindowdump',
            'igs'         => 'model/iges',
            'iges'        => 'model/iges',
            'msh'         => 'model/mesh',
            'mesh'        => 'model/mesh',
            'silo'        => 'model/mesh',
            'wrl'         => 'model/vrml',
            'vrml'        => 'model/vrml',
            'css'         => 'text/css',
            'html'        => 'text/html',
            'htm'         => 'text/html',
            'asc'         => 'text/plain',
            'txt'         => 'text/plain',
            'rtx'         => 'text/richtext',
            'rtf'         => 'text/rtf',
            'sgml'        => 'text/sgml',
            'sgm'         => 'text/sgml',
            'tsv'         => 'text/tab-separated-values',
            'wml'         => 'text/vnd.wap.wml',
            'wmls'        => 'text/vnd.wap.wmlscript',
            'etx'         => 'text/x-setext',
            'xsl'         => 'text/xml',
            'xml'         => 'text/xml',
            'mpeg'        => 'video/mpeg',
            'mpg'         => 'video/mpeg',
            'mpe'         => 'video/mpeg',
            'qt'          => 'video/quicktime',
            'mov'         => 'video/quicktime',
            'mxu'         => 'video/vnd.mpegurl',
            'avi'         => 'video/x-msvideo',
            'movie'       => 'video/x-sgi-movie',
            'ice'         => 'x-conference/x-cooltalk',
            'json'        => 'application/json',
        ];
        isset($headers[$type]) || static::exception($type . 'header类型暂未支持');
        header('Content-Type:' . $headers[$type] . '; charset=' . $charset);
    }

    /**
     * 下载文件头
     *
     * @param   string  $filepath  文件路径
     * @param   string  $filename  文件名
     * @param   string  $type      文件类型
     *
     * @return  void
     */
    public static function download(string $filepath, string $filename = '', string $type = ''): void
    {
        !is_readable($file) || static::exception('无法发送下载头,原因:' . (string) $file . '无法读取');

        static::type($type ?: pathinfo($filepath, PATHINFO_EXTENSION));

        header('Accept-Ranges:bytes');

        header('Accept-Length:' . filesize($filepath));

        header('Content-Disposition:attachment;filename=' . ($filename ?: pathinfo($filepath, PATHINFO_FILENAME)));
    }

}
