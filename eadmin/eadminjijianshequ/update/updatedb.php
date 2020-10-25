<?php
set_time_limit(0);


$site_config = webconfig('SYS_VERSION');

$site_config = str_replace('.', '', $site_config);

$site_config = (int)$site_config;

if ($site_config < 180) {

    return json(['code' => 0, 'msg' => '该补丁包需要先升级到1.80版本再升级']);

}
if ($site_config == 182) {

    return json(['code' => 0, 'msg' => '您已经是最新版本']);

}


$dirname     = dirname(__FILE__);
$install_sql = $dirname . DS . 'update.sql';

if (file_exists($install_sql)) {
    $sql_string = file_get_contents($install_sql);


    $sql_string = str_replace("\r", "\n", $sql_string);
    $sql_string = str_replace('es_', DB_PREFIX, $sql_string);
    $charset[1] = substr($sql_string, 0, 1);
    $charset[2] = substr($sql_string, 1, 1);
    $charset[3] = substr($sql_string, 2, 1);

    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        $sql_string = substr($sql_string, 3);

    }
    $sql = explode(";\n", $sql_string);


    foreach ($sql as $value) {
        if (!empty(trim($value))) {

            $this->query($value);

        }
    }


}
db('config')->where(['name' => 'SYS_VERSION'])->update(['value' => '1.82']);

return json(['code' => 200, 'msg' => '更新完毕，请清理缓存并重新登录']);
