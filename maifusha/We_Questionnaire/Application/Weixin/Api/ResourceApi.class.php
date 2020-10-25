<?php 
namespace Weixin\Api;
use Weixin\Api\BaseApi;

/**
 * 资源管理Api，上传素材文件、图文消息等等
 * Attention：以公众平台的机制，素材文件上传到微信服务器三天后即会失效
 */
class ResourceApi extends BaseApi
{
    /**
     * 上传图文消息资源至公众平台
     * @param string $title  图文消息标题
     * @param string $mediaID  图文消息封面图的media_id
     * @param string $content  图文消息正文内容
     * @param string $link  图文消息的链接地址
     * @param string $digest  图文消息的摘要
     * @param string $author  图文消息的作者
     * @param string $showCover  图文消息的正文是否显示封面图片
     * @return string  图文消息上传后获得的media_id
     */
    public function uploadNews($title, $mediaID, $content, $link, $digest='', $author='', $showCover=1)
    {
        $interface = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$this->getAccessToken()}";
        $data = <<<data
                        {
                           "articles": [
                                 {
                                    "thumb_media_id":"$mediaID",
                                    "author":"$author",
                                    "title":"$title",
                                    "content_source_url":"$link",
                                    "content":"$content",
                                    "digest":"$digest",
                                    "show_cover_pic":"$showCover"
                                 }
                           ]
                        }
data;

        $responseSeq = httpPost($interface, $data);

        try {
            $result = $this->responseValidate($responseSeq);
        } catch ( \Exception $e ) {
            $errcode = $e->getCode();
            $errmsg = $e->getMessage();

            E("图文消息上传失败 <br /> 返回消息: $errmsg <br /> 错误码: $errcode");
        }

        return $result['media_id'];
    }

    /**
	 * 上传文件至公众平台, 这里暂时不考虑缩略图的上传
	 * @param string $filePath  上传文件路径
     * @type string $type  素材类型, 可取值image voice video thumb
	 * @return string  媒体文件上传后获得的media_id
	 */
	public function uploadFile($filePath, $type)
	{
        $interface = "http://file.api.weixin.qq.com/cgi-bin/media/upload";

		$data = array(
			'access_token' => $this->getAccessToken(),
			'type' => $type,
		);

		$responseSeq = httpPost($interface, $data, $filePath);
		try {
			$result = $this->responseValidate($responseSeq);
		} catch ( \Exception $e ) {
			$errcode = $e->getCode();
			$errmsg = $e->getMessage();

			$this->error("文件上传至公众平台发生错误 <br /> 文件路径: $filePath <br /> 返回消息: $errmsg <br /> 错误码: $errcode");
		}

		return $result['media_id'];
	}

	/**
	 * 从公众平台下载多媒体文件
	 * @param int $mediaID  多媒体文件的media_id
	 * @param string $dir  文件存放目录
	 */
	public function downloadFile($mediaID, $dir)
	{
        $interface = "http://file.api.weixin.qq.com/cgi-bin/media/get";

        //TODO: 后续完成下载素材文件功能
	}

}
?>