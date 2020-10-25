<?php 
 return array (
  'dux.upload_driver' => 
  array (
    'local' => 
    array (
      'driver' => 'local',
    ),
    'qiniu' => 
    array (
      'access_key' => '',
      'secret_key' => '',
      'bucket' => '',
      'domain' => '',
      'url' => '',
      'driver' => 'qiniu',
    ),
    'oss' => 
    array (
      'driver' => 'oss',
      'access_id' => '',
      'secret_key' => '',
      'bucket' => '',
      'domain' => '',
      'url' => '',
    ),
    'cos' => 
    array (
      'SecretId' => '',
      'SecretKey' => '',
      'bucket' => '',
      'domain' => '',
      'url' => '',
      'driver' => 'cos',
    ),
  ),
);