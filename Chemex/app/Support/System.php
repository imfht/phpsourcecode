<?php


namespace App\Support;


use Exception;
use Illuminate\Support\Facades\Http;

class System
{
    /**
     * 检查WebSSH服务是否启动
     * @param $url
     * @return int|mixed
     */
    public static function checkWebSSHServiceStatus($url)
    {
        try {
            $response = Http::get($url);
            return $response->status();
        } catch (Exception $e) {
            return $e->getCode();
        }
    }

    /**
     * 检查WebSSH服务是否被安装
     * @return int
     */
    public static function checkWebSSHServiceInstalled()
    {
        $result = exec('which wssh', $outputs);
        if (empty($result)) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * 比较两个语义化版本的大小
     * @param $old
     * @param $new
     * @param string $delimiter
     * @return int
     */
    public static function diffVersion($old, $new, $delimiter = '.')
    {
        $old = explode($delimiter, $old);
        $new = explode($delimiter, $new);
        $res = $old[0] <=> $new[0];
        if ($res == 0) {
            $res = $old[1] <=> $new[1];
            if ($res == 0) {
                return $old[2] <=> $new[2];
            }
            return $res;
        }
        return $res;
    }
}
