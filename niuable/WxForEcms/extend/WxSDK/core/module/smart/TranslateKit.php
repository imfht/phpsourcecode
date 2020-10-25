<?php
namespace WxSDK\core\module\smart;
use WxSDK\core\common\IApp;
use WxSDK\Request;
use WxSDK\core\model\Model;
use WxSDK\Url;
use WxSDK\resource\Config;
use WxSDK\core\utils\Tool;

/**
 * 语音翻译
 * @author Wang Wei
 *
 */
class TranslateKit
{
    /**
     * 发送语音文件
     * @param IApp $app
     * @param string $filename
     * @param string $voiceId
     * @param string $lang
     */
    public static function sendVoice(IApp $app, string $filename, string $voiceId, $lang = 'zh_CN'){
        $t = str_replace("VOICE_ID", $voiceId, Config::$smart_send_voice);
        $t = str_replace("LANG", $lang, $t);
        $media = Tool::createMediaData($filename);
        $request = new Request($app, new Model($media, TRUE), new Url($t));
        return $request->run();
    }
    /**
     * 获取语音识别的结果
     * @param IApp $app
     * @param string $voiceId
     * @param string $lang
     */
    public static function getVoiceMeaning(IApp $app, string $voiceId, $lang = 'zh_CN'){
        $t = str_replace("VOICE_ID", $voiceId, Config::$smart_get_voice_meaning);
        $t = str_replace("LANG", $lang, $t);
        $request = new Request($app, new Model(), new Url($t));
        return $request->run();
    }
    
    public static function translate(IApp $app, string $content, $langFrom = 'zh_CN', $langTo='en_US'){
        $t = str_replace("LFROM", $langFrom, Config::$smart_translate_content);
        $t = str_replace("LTO", $langTo, $t);
        $request = new Request($app, new Model($content), new Url($t));
        return $request->run();
    }
}

