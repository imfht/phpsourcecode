<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Zjcaptcha Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 * @since       2.5
 * @author      Zjmainstay
 * @website     http://www.zjmainstay.cn
 */
class PlgCaptchaZjcaptcha extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Gets the challenge HTML
     *
     * @param   string  $name   The name of the field.
     * @param   string  $id     The id of the field.
     * @param   string  $class  The class of the field. This should be passed as
     *                          e.g. 'class="required"'.
     *
     * @return  string  The HTML to be embedded in the form.
     *
     * @since  2.5
     */
    public function onDisplay($name, $id = 'dynamic_zjcaptcha_1', $class = '')
    {
        defined('JURI_PLUGIN_ZJCAPTCHA') or define('JURI_PLUGIN_ZJCAPTCHA',JURI::root().'plugins/captcha/zjcaptcha/core');
            $input			= $id;
            $inputWidth		= $this->params->get('inputWidth','60px');
            $imgWidth		= $this->params->get('imgWidth','50px');
            $imgHeight		= $this->params->get('imgHeight','25px');
            $_SESSION['imgWidth']  = (int)$imgWidth;
            $_SESSION['imgHeight'] = (int)$imgHeight;
            $jquery			= $this->params->get('jquery')? '<script type="text/javascript" src="'.JURI_PLUGIN_ZJCAPTCHA .'/jquery-1.6.2.min.js"></script>':'';
            $img 			= JURI_PLUGIN_ZJCAPTCHA . '/createcode.php?0';
            $validateUrl	= JURI_PLUGIN_ZJCAPTCHA . '/checkcode.php';
            $zjcaptcha 		= <<<YZM
        <input name="{$input}" id="mod-captcha-{$input}" size="6" class="zjcaptcha" style="width:{$inputWidth}" type="text"/>
        <img class="{$input}-img" style="height:{$imgHeight};width:{$imgWidth};" src="{$img}" onclick="this.src=this.src.substring(0,this.src.indexOf('?')+1)+Math.random();return false;" />
        <div class="yzmtips" style="color:red"></div>
        {$jquery}
        <script>
        (function($){
        $(document).ready(function(){
            $("form").submit(function(){
                if($(this).find(".zjcaptcha").size()) {
                    if($(this).data("lock")) return true;
                    $(this).data("lock", true);
                    var obj = $(this);
                    $.ajax({
                        url:'$validateUrl',
                        type:'POST',
                        data:{code:$.trim($("input[name={$input}]").val())},
                        dataType:'json',
                        async:false,
                        success:function(result){
                            $(this).data("lock", null);
                            if(result.status == 1){
                                obj.submit();		//验证码正确提交表单
                            }else{
                                $(".{$input}-img").click();
                                $(".yzmtips").html('验证码错误！');
                                setTimeout(function(){
                                    $(".yzmtips").empty();
                                },3000);
                            }
                        },
                        error:function(msg){
                            $(this).data("lock", null);
                            $(".yzmtips").html('Error:'+msg.toSource());
                        }
                    });
                    return false;
                }
            })
        });
        })(jQuery);
        </script>
YZM;
        return $zjcaptcha;
    }
    
    public function onCheckAnswer($code) {
        return true;
    }
}
