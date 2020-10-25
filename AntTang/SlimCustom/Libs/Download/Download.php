<?php
/**
 * 下载工具类
 * 
 * @package     Download.php
 * @author      Jing <tangjing3321@gmail.com>
 * @version     1.0
 * @date        2018年3月27日
 */

namespace SlimCustom\Libs\Download;

class Download
{
    
    /**
     * 根据输入参数生成下载目录
     * 
     * @var integer
     */
    const DIR_GENERATION_BY_ARGS = 1;
    
    /**
     * 根据下载路由信息生成下载目录
     * @var integer
     */
    const DIR_GENERATION_BY_URL = 2;
    
    /**
     * 下载连接
     * 
     * @var string
     */
    private $url;
    
    /**
     * 下载指定目录
     * 
     * @var string
     */
    private $targetDir;
    
    /**
     * 是否显示进度信息
     * 
     * @var boolean
     */
    private $displayProgress;
    
    /**
     * 目录生成规则
     * 
     * @var integer
     */
    private $dirGenerationRule;
    
    /**
     * curl进度回调闭包
     * 
     * @var \Closure
     */
    private $curlProgressCallbackClosure;
    
    /**
     * 初始化
     * 
     * @param string $url
     * @param string $targetDir
     * @param string $displayProgress
     * @param integer $dirGenerationRule
     * @return boolean
     */
    public function __construct($url, $targetDir, $displayProgress = false, $dirGenerationRule = self::DIR_GENERATION_BY_ARGS, \Closure $curlProgressCallback = null)
    {
        $this->url = $url;
        $this->targetDir = rtrim($targetDir, '/');
        $this->displayProgress = $displayProgress;
        $this->dirGenerationRule = $dirGenerationRule;
        if ($curlProgressCallback) {
            $this->bindCurlProgressCallback($curlProgressCallback);
        }
    }
    
    /**
     * 下载启动
     * 
     * @return boolean
     */
    public function run()
    {
        $fileInfo = $this->generateNewFile($this->url, $this->targetDir, $this->dirGenerationRule);
        $fileInfo['remote_file_size'] = remoteFileSzie($this->url);
        cache()->put('download.rate.number.' . md5($this->url), $fileInfo, 60*24*7);
        // 未下载或者未完成，开始下载
        if ($fileInfo['new_file_size'] < $fileInfo['remote_file_size']) {
            return $this->curlDownload($fileInfo['new_file_name'], $fileInfo['new_file_size']);
        }
        // 下载完成显示100%进度条
        if ($fileInfo['new_file_size'] == $fileInfo['remote_file_size']) {
            $basename = basename($fileInfo['new_file_name']);
            $downloadSizem = $fileInfo['remote_file_size'] / (1024*1024);
            $totalSizem = $downloadSizem;
            $printStr = sprintf("progress: [%-50s] %d%% %.2fm/%.2fm %s \r", str_repeat('#', 50), 100, $downloadSizem, $totalSizem, $basename);
            cache()->put('download.rate.printStr.' . md5($this->url), $printStr, 60*24*7);
            if ($this->displayProgress) {
                echo $printStr . PHP_EOL;
            }
            return true;
        }
        return true;
    }
    
    /**
     * 是否下载成功
     * 
     * @return boolean
     */
    public function isSuccess()
    {
        $fileInfo = $this->generateNewFile($this->url, $this->targetDir, $this->dirGenerationRule);
        $fileInfo['remote_file_size'] = remoteFileSzie($this->url);
        if ($fileInfo['new_file_size'] < $fileInfo['remote_file_size']) {
            return false;
        }
        return $this->run();
    }
    
    /**
     * 绑定一个curl下载进度回调闭包
     *
     * @param \Closure $closure
     * @return Download
     */
    public function bindCurlProgressCallback(\Closure $closure)
    {
        $this->curlProgressCallbackClosure = $closure;
        return $this;
    }
    
    /**
     * curl下载
     * 
     * @param string $newFileName
     * @param integer $offset
     * @return boolean
     */
    private function curlDownload($newFileName, $offset)
    {
        $curl = curl_init();
        $newfileHandle = fopen($newFileName, "ab");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_FILE, $newfileHandle);
        curl_setopt($curl, CURLOPT_PROXY, '');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 阻止对证书的合法性的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_NOPROGRESS, false);
        $download = $this;
        $basename = basename($newFileName);
        curl_setopt($curl, CURLOPT_PROGRESSFUNCTION , function ($resource, $dltotal, $dlnow, $ultotal, $ulnow) use ($download, $basename){
            return $download->curlProgressCallback($resource, $dltotal, $dlnow, $ultotal, $ulnow, $basename);
        });
        curl_setopt($curl, CURLOPT_RESUME_FROM, $offset);
        //curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curlSuccess = curl_exec($curl);
        fclose($newfileHandle);
        curl_close($curl);
        return $curlSuccess ? true : false;
    }
    
    /**
     * 生成目录，并且返回下载文件信息
     * 
     * @param string $url
     * @param string $targetDir
     * @param boolean $dirGenerationRule
     * @return boolean|number[]|string[]
     */
    private function generateNewFile($url, $targetDir, $dirGenerationRule)
    {
        $uri = parse_url($url);
        $path = explode('/', $uri['path']);
        // 选择目录生成规则
        switch ($dirGenerationRule) {
            case self::DIR_GENERATION_BY_ARGS:
                $newFileName = $targetDir . '\\' . $path[count($path) - 1];
                $path = '';
                break;
            case self::DIR_GENERATION_BY_URL:
                unset($path[count($path) - 1]);
                $path = implode('/', $path);
                $newFileName = $targetDir . $uri['path'];
                break;
            default:
                unset($path[count($path) - 1]);
                $path = implode('/', $path);
                $newFileName = $targetDir . $uri['path'];
                break;
        }
        // 创建下载目录
        filesystem()->makeDirectory($targetDir . $path, 0777, true, true);
        return [
            'new_file_name' => $newFileName,
            'new_file_size' => intval(@filesize($newFileName)),
        ];
    }
    
    /**
     * curl下载进度回调方法
     * 
     * @param resource $resource
     * @param integer $dltotal
     * @param integer $dlnow
     * @param integer $ultotal
     * @param integer $ulnow
     * @return mixed|number
     */
    private function curlProgressCallback($resource, $dltotal, $dlnow, $ultotal, $ulnow, $basename)
    {
        // 自定义处理
        if ($this->curlProgressCallbackClosure instanceof \Closure) {
            return call_user_func_array($this->curlProgressCallbackClosure, [$resource, $dltotal, $dlnow, $ultotal, $ulnow, $basename]);
        }
        // 默认处理
        else {
            if (is_resource($resource) && $dltotal) {
                $curlinfo = curl_getinfo($resource);
                $fileInfo = cache()->get('download.rate.number.' . md5($curlinfo['url']), []);
                $rate = ($dlnow + $fileInfo['new_file_size']) / $fileInfo['remote_file_size'] * 100;
                $downloadSizem = ($dlnow + $fileInfo['new_file_size']) / (1024*1024);
                $totalSizem = $fileInfo['remote_file_size'] / (1024*1024);
                $speed = $curlinfo['speed_download'];
                $printStr = sprintf("progress: [%-50s] %d%% %.2fm/%.2fm %d/kb %s \r", str_repeat('#', $rate/100*50), $rate, $downloadSizem, $totalSizem, $speed/1024, $basename);
                cache()->put('download.rate.printStr.' . md5($curlinfo['url']), $printStr, 60*24*7);
                if ($this->displayProgress) {
                    echo $printStr . PHP_EOL;
                }
            }
        }
        return 0;
    }
}