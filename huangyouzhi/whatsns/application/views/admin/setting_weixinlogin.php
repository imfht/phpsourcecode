<!--{template header}-->
<div style="width:100%; height:15px;color:#000;margin:0px 0px 10px;">
    <div style="float:left;"><a href="index.php?admin_main/stat{$setting['seo_suffix']}" target="main"><b>控制面板首页</b></a>&nbsp;&raquo;&nbsp;qq互联设置</div>
</div>
<!--{if isset($message)}-->
<!--{eval $type=isset($type)?$type:'correctmsg'; }-->
<table class="table">
    <tr>
        <td class="{$type}">{$message}</td>
    </tr>
</table>
<!--{/if}-->
<form action="index.php?admin_setting/weixinlogin{$setting['seo_suffix']}" method="post">
    <table class="table">
        <tr class="header">
            <td colspan="2">参数设置</td>
        </tr>
        <tr>
            <td colspan="2">配置微信开放平台网页微信登录参数前请先到<strong><a style="text-decoration:underline;color:blue;" target="_blank" href="https://open.weixin.qq.com">微信.开放平台</a></strong>申请网站应用,申请通过后填写应用相关参数，需审核通过开启网页登录。</td>
        </tr>
        <tr>
            <td width="45%"><b>是否开启微信网页登录：</b><br><span class="smalltxt">启用后可以在登录页面看到微信登录图标</span></td>
            <td>
                <input type="radio" value="1" name="wechat_open" {if $setting['wechat_open']}checked{/if}/>是&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="0" name="wechat_open" {if !$setting['wechat_open']}checked{/if}/>否
            </td>
        </tr>
        <tr>
            <td width="45%"><b>AppID: </b><br><span class="smalltxt">微信开放平台网站应用-当前问答的APP ID值</span></td>
            <td><input type="text" size="60" value="{$setting['wechat_appid']}" name="wechat_appid" /></td>
        </tr>
        <tr>
            <td width="45%"><b>AppSecret: </b><br><span class="smalltxt">微信开放平台网站应用-当前问答的AppSecret值</span></td>
            <td><input type="text" size="60" value="{$setting['wechat_appSecret']}" name="wechat_appSecret"></td>
        </tr>


    </table>
    <br>
    <center><input type="submit" class="btn btn-success" name="submit" value="提 交"></center><br>
</form>
<br>
<!--{template footer}-->