<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class plugin_markdown {
    /**
     * [插件:正文markdown解析]
     * @param [type] $content  [参数]
     */
    public static function HOOK($content,&$resource=null) {
        plugin::init(__CLASS__);
        if($resource['markdown']==="1"||$resource['markdown']===true){
            $content = plugin_download::markdown($content);

            plugin::library('Parsedown');
            $Parsedown = new Parsedown();
            $Parsedown->setBreaksEnabled(true);
            $content = str_replace(array(
                '#--' . iPHP_APP . '.Markdown--#',
                '#--' . iPHP_APP . '.PageBreak--#',
            ), array('', '@--' . iPHP_APP . '.PageBreak--@'), $content);
            $content = $Parsedown->text($content);
            $content = str_replace('@--' . iPHP_APP . '.PageBreak--@', '#--' . iPHP_APP . '.PageBreak--#', $content);
        }
        return $content;
    }
}

