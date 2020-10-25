<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-04 00:28:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-30 12:27:21
 */
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace().'\\frontend'; ?>;


use Yii;
use yii\helpers\Url;
use yii\web\Controller;


/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="www.ai.com/api",
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="店滴AI接口文档",
 *     description="店滴AI接口文档",
 *         @SWG\Contact(
 *             name="王春生",
 *             email="2192138785@qq.com"
 *         )),
 * )
 */
class DocController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                // 'restUrls' => Url::to(['doc/json-schema']),
                'restUrl' => [
                    ['url' => Url::to(['doc/json-base']), 'name' => '商城接口'],
                ],
                'view' => '@frontend/views/apidoc/index'
            ],
            'json-base' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                'scanDir' => [
                    Yii::getAlias('@<?= $generator->getControllerNamespace().'\\api'; ?>'),
                    Yii::getAlias('@frontend/controllers')
                ],
                'cacheKey' => '<?= $generator->getApiCachekey(); ?>',
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
