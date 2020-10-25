<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/6
 * Time: 16:57
 */

namespace util;

use umeng\ios\IOSBroadcast;

require_once(VENDOR_PATH . 'umeng/ios/IOSBroadcast.php');
require_once(VENDOR_PATH . 'umeng/ios/IOSFilecast.php');
require_once(VENDOR_PATH . 'umeng/ios/IOSGroupcast.php');
require_once(VENDOR_PATH . 'umeng/ios/IOSUnicast.php');
require_once(VENDOR_PATH . 'umeng/ios/IOSCustomizedcast.php');
require_once(VENDOR_PATH . 'umeng/android/AndroidBroadcast.php');
require_once(VENDOR_PATH . 'umeng/android/AndroidFilecast.php');
require_once(VENDOR_PATH . 'umeng/android/AndroidGroupcast.php');
require_once(VENDOR_PATH . 'umeng/android/AndroidUnicast.php');
require_once(VENDOR_PATH . 'umeng/android/AndroidCustomizedcast.php');


class Umeng
{
    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $timestamp = NULL;
    protected $validation_token = NULL;

    /**
     * 构造函数
     * Umeng constructor.
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret)
    {
        $this->appkey = $key;
        $this->appMasterSecret = $secret;
        $this->timestamp = strval(time());
    }

    public function sendUmengNotify($param, $device)
    {
        // 推送类型：1广播推送 2单播推送 3组播推送 4文件播推送 5自定义推送
        $type = isset($param['type']) ? (int)$param['type'] : 1;
        // 提示文字
        $ticker = isset($param['ticker']) ? $param['ticker'] : '';
        // 推送标题
        $title = isset($param['title']) ? $param['title'] : "";
        // 副标题
        $subtitle = isset($param['subtitle']) ? $param['subtitle'] : "";
        // 推送内容
        $body = isset($param['body']) ? $param['body'] : "";
        // 自定义参数
        $extras = isset($param['extra']) ? $param['extra'] : [];

        switch ($type) {
            case 1:
                // 广播
                if ($device == 1) {
                    // 苹果
                    $result = $this->sendIOSBroadcast($title, $subtitle, $body, $extras);
                } else if ($device == 2) {
                    // 安卓
                    $result = $this->sendAndroidBroadcast($title, $body, $ticker, $extras);
                }
                break;
            case 2:
                // 单播
                // 设备TOKEN
                $device_tokens = isset($param['device_tokens']) ? $param['device_tokens'] : '';
                if ($device == 1) {
                    // 苹果
                    $result = $this->sendIOSUnicast($title, $subtitle, $body, $device_tokens, $extras);
                } else if ($device == 2) {
                    // 安卓
                    $result = $this->sendAndroidUnicast($title, $body, $ticker, $device_tokens, $extras);
                }
                break;
            case 3:
                // 文件播
                // 文件内容(\n分隔)
                $content = isset($param['content']) ? $param['content'] : '';
                if ($device == 1) {
                    // 苹果
                    $result = $this->sendIOSFilecast($title, $subtitle, $body, $content);
                } else if ($device == 2) {
                    // 安卓
                    $result = $this->sendAndroidFilecast($title, $body, $ticker, $content);
                }
                break;
            case 4:
                // 组播
                if ($device == 1) {
                    // 苹果
                    $tag = isset($param['tag']) ? $param['tag'] : '';
                    $result = $this->sendIOSGroupcast($title, $subtitle, $body, $tag);
                } else if ($device == 2) {
                    // 安卓
                    $result = $this->sendAndroidGroupcast($title, $body, $ticker);
                }
                break;
            case 5:
                // 自定义播
                // 别名
                $alias = isset($param['alias']) ? $param['alias'] : "";
                // 别名类型
                $alias_type = isset($param['alias_type']) ? $param['alias_type'] : "daxuequan";
                if ($device == 1) {
                    // 苹果
                    $result = $this->sendIOSCustomizedcast($title, $subtitle, $body, $alias, $alias_type, $extras);
                } else if ($device == 2) {
                    // 安卓
                    $result = $this->sendAndroidCustomizedcast($title, $body, $ticker, $alias, $alias_type, $extras);
                }
                break;
        }
        return $result;
    }


    function sendAndroidBroadcast($title, $body, $ticker, $extras = [])
    {
        try {
            $brocast = new \AndroidBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey", $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $brocast->setPredefinedKeyValue("mipush", true);
            $brocast->setPredefinedKeyValue("mi_activity", "com.university.link.app.unmeng.PushSystemActivity");
            $brocast->setPredefinedKeyValue("ticker", $ticker);
            $brocast->setPredefinedKeyValue("title", $title);
            $brocast->setPredefinedKeyValue("text", $body);
            $brocast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "true");
            $brocast->setPredefinedKeyValue("after_open", "go_activity");
            $brocast->setPredefinedKeyValue("activity", "com.university.link.app.unmeng.OpenActivityActionActivity");
            // [optional]Set extra fields
            $brocast->setExtraField("extra", $extras);
//            print("Sending broadcast notification, please wait...\r\n");
            $result = $brocast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendAndroidUnicast($title, $body, $ticker, $device_tokens, $extras = [])
    {
        try {
            $unicast = new \AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", $device_tokens);
            $unicast->setPredefinedKeyValue("ticker", $ticker);
            $unicast->setPredefinedKeyValue("title", $title);
            $unicast->setPredefinedKeyValue("text", $body);
            $unicast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set extra fields
            $unicast->setExtraField("extra", $extras);
//            print("Sending unicast notification, please wait...\r\n");
            $result = $unicast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendAndroidFilecast($title, $body, $ticker, $content)
    {
        try {
            $filecast = new \AndroidFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $filecast->setPredefinedKeyValue("ticker", $ticker);
            $filecast->setPredefinedKeyValue("title", $title);
            $filecast->setPredefinedKeyValue("text", $body);
            $filecast->setPredefinedKeyValue("after_open", "go_app");  //go to app
//            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents($content);
//            print("Sending filecast notification, please wait...\r\n");
            $result = $filecast->send();
//            print("Sent SUCCESS\r\
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendAndroidGroupcast($title, $body, $ticker)
    {
        try {
            /*
              *  Construct the filter condition:
              *  "where":
              *	{
              *		"and":
              *		[
                *			{"tag":"test"},
                *			{"tag":"Test"}
              *		]
              *	}
              */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => "test"
                        ),
                        array(
                            "tag" => "Test"
                        )
                    )
                )
            );

            $groupcast = new \AndroidGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("ticker", $ticker);
            $groupcast->setPredefinedKeyValue("title", $title);
            $groupcast->setPredefinedKeyValue("text", $body);
            $groupcast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $groupcast->setPredefinedKeyValue("production_mode", "true");
//            print("Sending groupcast notification, please wait...\r\n");
            $result = $groupcast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendAndroidCustomizedcast($title, $body, $ticker, $alias, $alias_type, $extras = [])
    {
        try {
            $customizedcast = new \AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $customizedcast->setPredefinedKeyValue("mipush", true);
            $customizedcast->setPredefinedKeyValue("mi_activity", "com.university.link.app.unmeng.PushSystemActivity");
            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $alias);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", $alias_type);
            $customizedcast->setPredefinedKeyValue("ticker", $ticker);
            $customizedcast->setPredefinedKeyValue("title", $title);
            $customizedcast->setPredefinedKeyValue("text", $body);
            $customizedcast->setPredefinedKeyValue("after_open", "go_activity");
            $customizedcast->setPredefinedKeyValue("activity", "com.university.link.app.unmeng.OpenActivityActionActivity");
            $customizedcast->setExtraField("extra", $extras);
//            print("Sending customizedcast notification, please wait...\r\n");
            $result = $customizedcast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendAndroidCustomizedcastFileId($param)
    {
        try {
            $customizedcast = new \AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->uploadContents("aa" . "\n" . "bb");
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", "xx");
            $customizedcast->setPredefinedKeyValue("ticker", "Android customizedcast ticker");
            $customizedcast->setPredefinedKeyValue("title", "Android customizedcast title");
            $customizedcast->setPredefinedKeyValue("text", "Android customizedcast text");
            $customizedcast->setPredefinedKeyValue("after_open", "go_app");
//            print("Sending customizedcast notification, please wait...\r\n");
            $result = $customizedcast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendIOSBroadcast($title, $subtitle, $body, $extras = [])
    {
        try {
            $brocast = new \IOSBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey", $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $brocast->setPredefinedKeyValue("alert", [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
            ]);
            $brocast->setPredefinedKeyValue("badge", 1);
            $brocast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $brocast->setCustomizedField("extra", $extras);
            $result = $brocast->send();
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendIOSUnicast($title, $subtitle, $body, $device_tokens, $extras = [])
    {
        try {
            $unicast = new \IOSUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", $device_tokens);
            $unicast->setPredefinedKeyValue("alert", [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
            ]);
            $unicast->setPredefinedKeyValue("badge", 1);
            $unicast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $unicast->setCustomizedField("extra", $extras);
            $result = $unicast->send();
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendIOSFilecast($title, $subtitle, $body, $content)
    {
        try {
            $filecast = new \IOSFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $filecast->setPredefinedKeyValue("alert", [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
            ]);
            $filecast->setPredefinedKeyValue("badge", 1);
            $filecast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $filecast->setPredefinedKeyValue("production_mode", "false");
//            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents($content);
//            print("Sending filecast notification, please wait...\r\n");
            $result = $filecast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendIOSGroupcast($title, $subtitle, $body, $tag)
    {
        try {
            /*
              *  Construct the filter condition:
              *  "where":
              *	{
              *		"and":
              *		[
                *			{"tag":"iostest"}
              *		]
              *	}
              */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => $tag,
                        )
                    )
                )
            );

            $groupcast = new \IOSGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("alert", [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
            ]);
            $groupcast->setPredefinedKeyValue("badge", 1);
            $groupcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $groupcast->setPredefinedKeyValue("production_mode", "false");
//            print("Sending groupcast notification, please wait...\r\n");
            $result = $groupcast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }

    function sendIOSCustomizedcast($title, $subtitle, $body, $alias, $alias_type, $extras = [])
    {
        try {
            $customizedcast = new \IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $alias);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", $alias_type);
            $customizedcast->setPredefinedKeyValue("alert", [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
            ]);
            $customizedcast->setPredefinedKeyValue("badge", 1);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $customizedcast->setCustomizedField("extra", $extras);
//            print("Sending customizedcast notification, please wait...\r\n");
            $result = $customizedcast->send();
//            print("Sent SUCCESS\r\n");
            return $result;
        } catch (Exception $e) {
//            print("Caught exception: " . $e->getMessage());
        }
        return null;
    }
}