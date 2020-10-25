<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once "ErrorCase.php";
class Recorder
{
    private static $data;
    private $inc;
    private $error;

    private $CI = null;

    public function __construct()
    {
        $this->error = new ErrorCase();

        //-------读取配置文件
        //$incFileContents = file(dirname(dirname(__FILE__)) . "/" . "comm/inc.php");
        //$incFileContents = $incFileContents[1];
        //$this->inc = json_decode($incFileContents);
        //if (empty($this->inc)) {
        //    $this->error->showError("20001");
        //}

        //读取配置
        $this->CI = &get_instance();
        $qc_config = $this->CI->config->item('qc');
        $this->inc = array(
            'appid' => $qc_config['appid'],
            'appkey' => $qc_config['appkey'],
            'callback' => $qc_config['callback'],
            'scope' => 'get_user_info',
            'errorReport' => true,
            'storageType' => 'file',
        );
        $this->inc = json_decode(json_encode($this->inc));

        if (empty($_SESSION['QC_userData'])) {
            self::$data = array();
        } else {
            self::$data = $_SESSION['QC_userData'];
        }
    }

    public function write($name, $value)
    {
        self::$data[$name] = $value;
        $_SESSION['QC_userData'] = self::$data;
    }

    public function read($name)
    {
        if (empty(self::$data[$name])) {
            return null;
        } else {
            return self::$data[$name];
        }
    }

    public function readInc($name)
    {
        if (empty($this->inc->$name)) {
            return null;
        } else {
            return $this->inc->$name;
        }
    }

    public function delete($name)
    {
        unset(self::$data[$name]);
        $_SESSION['QC_userData'] = self::$data;
    }

    public function __destruct()
    {

    }
}
