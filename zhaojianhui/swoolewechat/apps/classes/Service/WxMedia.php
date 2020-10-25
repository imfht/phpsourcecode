<?php
namespace App\Service;

class WxMedia
{
    /**
     * @var \App\Model\WxMedia
     */
    private $wxMediaModel;
    /**
     * 素材类别
     * @var array
     */
    private $mediaTypeList = ['image','imagetemp','voice','voicetemp','video','videotemp','thumb','thumbtemp'];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->wxMediaModel = model('WxMedia');
    }

    /**
     * 保存普通素材
     * @param array $formData
     */
    public function saveNormal($formData = [])
    {
        $this->wxMediaModel->start();
        try{
            if (empty($formData['mediaType']) || !in_array($formData['mediaType'], $this->mediaTypeList)){
                throw new \Exception('请选择正确的素材类别');
            }
            if (empty($formData['title'])){
                throw new \Exception('请输入素材标题');
            }
            switch ($formData['mediaType']){
                case 'image':
                case 'imagetemp':
                    //上传文件到七牛
                    $uploader = new \App\Service\SysUploader([
                        "pathFormat" => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                        "allowFiles" => ['gif','jpeg','jpg','png'],
                    ]);

                    $result = $uploader->upByPath($formData['filePath']);
                    if (isset($result['state']) || $result['state'] != 'SUCCESS'){
                        throw new \Exception($result['state']);
                    }
                    $formData['remoteUrl'] = $result['url'];
                    $uploader->saveDataToTable('image');
                    //上传到微信
                    if ($formData['mediaType'] == 'image'){
                        $wxUpRs = \Swoole::$php->easywechat->material->uploadImage($formData['filePath']);
                    }else{
                        $wxUpRs = \Swoole::$php->easywechat->material_temporary->uploadImage($formData['filePath']);
                    }
                    if (!isset($wxUpRs['media_id']) || empty($wxUpRs['media_id'])){
                        throw new \Exception('上传到微信失败');
                    }
                    isset($wxUpRs['media_id']) && $formData['wxMediaId'] = $wxUpRs['media_id'];
                    isset($wxUpRs['url']) && $formData['wxRemoteUrl'] = $wxUpRs['url'];
                    break;
            }
            $formData['wxUploadTime'] = time();
            $formData['addUserId'] = \Swoole::$php->user->getUid();
            $formData['addTime'] = time();
            $this->wxMediaModel->put($formData);
            $this->wxMediaModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxMediaModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 保存图文素材
     */
    public function saveArticle()
    {

    }

    /**
     * 检查线上数据
     * @param $mediaType
     */
    private function checkOnlineMedia($mediaType)
    {

    }
    /**
     * 同步图文素材列表
     * @param $type
     */
    public function syncOnline($mediaType)
    {
        if (!in_array($mediaType, $this->mediaTypeList)){
            throw new \Exception('无效的素材类别');
        }
        if (strpos($mediaType, 'temp') !== false){
            throw new \Exception('暂不支持临时素材的同步');
        }
        $offset = 0;
        $stepCount = 10;
        $count = 0;
        do{
            $lists = \Swoole::$php->easywechat->material->lists($mediaType, $offset, $stepCount);
            //$lists = $lists->toArray();
            $count = isset($lists['item_count']) && $lists['item_count'] ? (int) $lists['item_count'] : 0;
            $offset += $count;
        }while($count != 0 && $count == $stepCount);

        return true;
    }
}