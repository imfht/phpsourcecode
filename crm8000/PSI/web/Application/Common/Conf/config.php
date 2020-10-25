<?php

function getHost()
{
  $result = getenv("PSI_DB_HOST") ?? $_SERVER["PSI_DB_HOST"];
  if (!$result) {
    $result = "127.0.0.1";
  }

  return $result;
}

function getDbName()
{
  $result = getenv("PSI_DB_NAME") ?? $_SERVER["PSI_DB_NAME"];
  if (!$result) {
    $result = "psi";
  }

  return $result;
}

function getDbUserName()
{
  $result = getenv("PSI_DB_USER_NAME") ?? $_SERVER["PSI_DB_USER_NAME"];
  if (!$result) {
    $result = "root";
  }

  return $result;
}

function getDbPassword()
{
  $result = getenv("PSI_DB_PASSWORD") ?? $_SERVER["PSI_DB_PASSWORD"];
  if (!$result) {
    $result = "";
  }

  return $result;
}

function getDbPort()
{
  $result = intval(getenv("PSI_DB_PORT")) ?? $_SERVER["PSI_DB_PORT"];
  if (!$result) {
    $result = 3306;
  }

  return $result;
}

return [
  'URL_CASE_INSENSITIVE' => false,
  'SHOW_ERROR_MSG' => true,
  'DB_TYPE' => 'mysql', // 数据库类型
  'DB_HOST' => getHost(), // 服务器地址
  'DB_NAME' => getDbName(), // 数据库名
  'DB_USER' => getDbUserName(), // 用户名
  'DB_PWD' => getDbPassword(), // 密码
  'DB_PORT' => getDbPort() // 端口
];
