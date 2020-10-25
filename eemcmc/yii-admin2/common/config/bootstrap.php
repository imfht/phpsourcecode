<?php

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');

Yii::$classMap['yii\helpers\Json'] = dirname(__DIR__) . '/helpers/Json.php';

//改写错误处理类
Yii::$classMap['yii\web\ErrorHandler'] = dirname(__DIR__) . '/errors/ErrorHandler.php';