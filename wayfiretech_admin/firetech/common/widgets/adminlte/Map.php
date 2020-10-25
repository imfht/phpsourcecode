<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 08:19:56
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-03 11:29:00
 */

namespace common\widgets\adminlte;

use common\helpers\MapHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use Faker\Provider\Uuid;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class Map extends InputWidget
{
    /**
     * 默认地址
     *
     * @var bool
     */
    public $defaultSearchAddress = '北京';

    public $theme = '@common/widgets/adminlte/views/selectmap/index.php';

    /**
     * 秘钥.
     *
     * @var string
     */
    public $secret_key = '';

    /**
     * 类型.
     *
     * 默认高德
     *
     * amap 高德
     * tencent 腾讯
     * baidu 高德
     *
     * @var string
     */
    public $type = 'amap';

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function run()
    {
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        try {
            if ($value && !is_array($value)) {
                $value = json_decode($value, true);
                empty($value) && $value = unserialize($value);
                empty($value) && $value = [];
            }
        } catch (\Exception $e) {
            $value = [];
        }

        $lng_lats = $this->getCity();
        
        // 显示地址
        $address = empty($value) ? '' : implode(',', [$value['lng'] ?? '', $value['lat'] ?? '']);

        $defaultValue = [
            'lng' => $value['lng'] ?? $lng_lats['lng'],
            'lat' => $value['lat'] ?? $lng_lats['lat'],
        ];

        // 注册js
        $this->registerViewJs();

        return $this->render($this->theme, [
            'name' => $name,
            'value' => $defaultValue,
            'type' => $this->type,
            'secret_key' => $this->secret_key,
            'address' => $address,
            'defaultSearchAddress' => $this->defaultSearchAddress,
            'boxId' => Uuid::uuid(),
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function registerViewJs()
    {
        $view = $this->view;
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        switch ($this->type) {
            case 'baidu':
                $view->registerJsFile($http_type.'api.map.baidu.com/api?v=2.0&ak='.$this->secret_key);
                break;
            case 'amap':
                $view->registerJsFile($http_type.'webapi.amap.com/maps?v=1.4.11&plugin=AMap.ToolBar,AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder&key='.$this->secret_key);
                $view->registerJsFile($http_type.'webapi.amap.com/ui/1.0/main.js?v=1.0.11');
                break;
            case 'tencent':
                $view->registerJsFile($http_type.'map.qq.com/api/js?v=2.exp&libraries=place&key='.$this->secret_key);
                break;
        }

        $view->registerCss(<<<Css
    #container {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
    }

    .search {
        position: absolute;
        width: 400px;
        top: 0;
        left: 50%;
        padding: 5px;
        margin-left: -200px;
    }
Css
        );
    }

    public function getCity()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        
        $getIp = MapHelper::get_client_ip();    
        
        if(!$this->secret_key){
            
            throw new \yii\web\HttpException(402,'请在公司参数中设置百度地图secret_key');
        }
        
        $content = file_get_contents($http_type."api.map.baidu.com/location/ip?ak=".$this->secret_key."&ip={$getIp}&coor=bd09ll");
        $json = json_decode($content);
      
        $this->defaultSearchAddress = $json->{'content'}->{'address'};
        
        return [
            'lng' => $json->{'content'}->{'point'}->{'x'} ?? '108.953228',
            'lat' =>  $json->{'content'}->{'point'}->{'y'} ?? '34.266552',
        ];
    }
}
