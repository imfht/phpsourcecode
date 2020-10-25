<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 处理微信操作
 */
class WechatController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();
    }

	public function distribute()
	{
        $this->bcItemPush('群发问卷');

        if( IS_GET ){ //访问页面
			$this->display();
        }else{ //提交问卷分发请求
            /* 检查字段完整性 */
            $checkList = array('subscribers', 'questionnaireID', 'title', 'content');
            $fieldList = checkFilled($checkList, I('post.'));
            $completed = $fieldList && I('data.cover', '', '', $_FILES)['name'];
            if( !$completed ){
                $this->error('请补充完整群发配置');
            }

            /* 配置信息完整,开始群发问卷 */

            $resApi = A('Weixin/Resource', 'Api');

            /* 上传图片素材 */
            $file = I('data.cover', '', '', $_FILES);
            $file['type'] == 'image/jpeg' OR $this->error('封面图片仅支持1M一下的jpg图片');
            $sep = DIRECTORY_SEPARATOR;
            $filePath = "{$_SERVER['DOCUMENT_ROOT']}{$sep}Public{$sep}Upload{$sep}{$file['name']}";
            move_uploaded_file($file['tmp_name'], $filePath);
            $file_mediaID = $resApi->uploadFile($filePath, 'image');
            unlink($filePath);

            /* 计算问卷链接地址 */
            $host = C('settings.weixin_domain');
            $appID = C('settings.weixin_AppID');
            $redirectURL = "http://{$host}/Weixin/Interview/index.html?questionnaireID=$fieldList[questionnaireID]";
            $redirectURL = urlencode($redirectURL);
            $link = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appID}&redirect_uri={$redirectURL}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

            $content = I('post.content')."<br/><br/><br/><span style='font-weight:bolder;color:orange;'>点击下方的 '阅读原文' 立即答题</span><br/><br/>";
            $news_mediaID = $resApi->uploadNews(I('post.title'), $file_mediaID, $content, $link); //上传图文消息
            $newsMsg = createMsg('mpnews', $news_mediaID); //创建消息体

            $msgApi = A('Weixin/Message', 'Api');
            $msgApi->sendToIDs($fieldList['subscribers'], $newsMsg);

        	$this->success('问卷分发成功');
        }
	}
}
?>