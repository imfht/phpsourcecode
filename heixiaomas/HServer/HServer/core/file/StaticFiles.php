<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/25
 * Time: 11:41
 */

namespace HServer\core\file;

use Workerman\Connection\TcpConnection;

use Workerman\Protocols\Http;

class StaticFiles
{
    /**
     * @var TcpConnection
     */
    protected $connection;

    public $path;
    public static $mimes = array(
        'html' => 'text/html',
        'htm' => 'text/html',
        'shtml' => 'text/html',
        'css' => 'text/css',
        'xml' => 'text/xml',
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'atom' => 'application/atom+xml',
        'rss' => 'application/rss+xml',
        'mml' => 'text/mathml',
        'txt' => 'text/plain',
        'jad' => 'text/vnd.sun.j2me.app-descriptor',
        'wml' => 'text/vnd.wap.wml',
        'htc' => 'text/x-component',
        'png' => 'image/png',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ico' => 'image/x-icon',
        'jng' => 'image/x-jng',
        'bmp' => 'image/x-ms-bmp',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'webp' => 'image/webp',
        'jar' => 'application/java-archive',
        'war' => 'application/java-archive',
        'ear' => 'application/java-archive',
        'hqx' => 'application/mac-binhex40',
        'doc' => 'application/msword',
        'pdf' => 'application/pdf',
        'ps' => 'application/postscript',
        'eps' => 'application/postscript',
        'ai' => 'application/postscript',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wmlc' => 'application/vnd.wap.wmlc',
        'kml' => 'application/vnd.google-earth.kml+xml',
        'kmz' => 'application/vnd.google-earth.kmz',
        '7z' => 'application/x-7z-compressed',
        'cco' => 'application/x-cocoa',
        'jardiff' => 'application/x-java-archive-diff',
        'jnlp' => 'application/x-java-jnlp-file',
        'run' => 'application/x-makeself',
        'pl' => 'application/x-perl',
        'pm' => 'application/x-perl',
        'prc' => 'application/x-pilot',
        'pdb' => 'application/x-pilot',
        'rar' => 'application/x-rar-compressed',
        'rpm' => 'application/x-redhat-package-manager',
        'sea' => 'application/x-sea',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'tcl' => 'application/x-tcl',
        'tk' => 'application/x-tcl',
        'der' => 'application/x-x509-ca-cert',
        'pem' => 'application/x-x509-ca-cert',
        'crt' => 'application/x-x509-ca-cert',
        'xpi' => 'application/x-xpinstall',
        'xhtml' => 'application/xhtml+xml',
        'zip' => 'application/zip',
        'bin' => 'application/octet-stream',
        'exe' => 'application/octet-stream',
        'dll' => 'application/octet-stream',
        'deb' => 'application/octet-stream',
        'dmg' => 'application/octet-stream',
        'eot' => 'application/octet-stream',
        'iso' => 'application/octet-stream',
        'img' => 'application/octet-stream',
        'msi' => 'application/octet-stream',
        'msp' => 'application/octet-stream',
        'msm' => 'application/octet-stream',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'kar' => 'audio/midi',
        'mp3' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'm4a' => 'audio/x-m4a',
        'ra' => 'audio/x-realaudio',
        '3gpp' => 'video/3gpp',
        '3gp' => 'video/3gpp',
        'mp4' => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mov' => 'video/quicktime',
        'webm' => 'video/webm',
        'flv' => 'video/x-flv',
        'm4v' => 'video/x-m4v',
        'mng' => 'video/x-mng',
        'asx' => 'video/x-ms-asf',
        'asf' => 'video/x-ms-asf',
        'wmv' => 'video/x-ms-wmv',
        'avi' => 'video/x-msvideo',
    );

    public function __construct($connection)
    {
        $this->path = __DIR__ . "/../../../app/static";
        $this->connection = $connection;
        if (!is_dir($this->path)) {
            throw new \RuntimeException("static path error");
        }
    }

    public function invoke()
    {

        $s_path = $_SERVER['REQUEST_URI'];
        if ($s_path === "/") {
            $s_path = "/index.html";
        }
        $url_info = parse_url('http://' . $_SERVER['HTTP_HOST'] . $s_path);
        $path = isset($url_info['path']) ? $url_info['path'] : '/';
        $path_info = pathinfo($path);
        $file_extension = isset($path_info['extension']) ? $path_info['extension'] : '';
        if ($file_extension !== '') {
            $file_path = $this->path . $path;

            if (is_file($file_path)) {
                $file_path = realpath($file_path);
                $info = stat($file_path);
                $modified_time = $info ? date('D, d M Y H:i:s', $info['mtime']) . ' ' . date_default_timezone_get() : '';
                if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $info) {
                    if ($modified_time === $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
                        return false;
                    }
                }
//              $file_size = filesize($file_path);
                $file_info = pathinfo($file_path);
                $extension = isset($file_info['extension']) ? $file_info['extension'] : '';
//              $file_name = isset($file_info['filename']) ? $file_info['filename'] : '';
//              Http::header("Content-Length:$file_size");
                if (isset(static::$mimes[$extension])) {
                    Http::header("Content-Type: " . static::$mimes[$extension] . ";charset=utf-8");
                }
                $this->connection->send(file_get_contents($file_path));
                return true;
            }
        }

        return false;
    }
}