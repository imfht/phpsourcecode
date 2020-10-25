<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lxj\Yii2\Tars;

use Tars\core\Request as TarsRequest;
use yii\base\InvalidConfigException;

class Request
{
    protected static $config;

    /**
     * @var \yii\web\Request
     */
    protected $yii2Request;

    /**
     * Make a request.
     *
     * @param TarsRequest $tarsRequest
     * @return Request
     * @throws \yii\base\InvalidConfigException
     */
    public static function make(TarsRequest $tarsRequest)
    {
        return new static($tarsRequest);
    }

    /**
     * Request constructor.
     * @param TarsRequest $tarsRequest
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(TarsRequest $tarsRequest)
    {
        $this->createYii2Request($tarsRequest);
    }

    /**
     * @param $tarsRequest
     * @throws \yii\base\InvalidConfigException
     */
    protected function createYii2Request($tarsRequest)
    {
        $app = Util::app();

        if (self::$config) {
            $requestConfig = self::$config;
        } elseif (isset($app->components['request'])) {
            $requestConfig = $app->components['request'];
            if (!is_array($requestConfig)) {
                throw new InvalidConfigException('Invalid request config:' . gettype($requestConfig));
            }
            self::$config = $requestConfig;
        } else {
            $requestConfig = [];
        }

        $requestConfig['class'] = Yii2Request::class;

        $this->yii2Request = \Yii::createObject($requestConfig);

        $this->yii2Request->setTarsRequest($tarsRequest);
    }

    /**
     * @return \yii\web\Request
     */
    public function toYii2()
    {
        return $this->getYii2Request();
    }

    /**
     * @return \yii\web\Request
     */
    public function getYii2Request()
    {
        return $this->yii2Request;
    }
}
