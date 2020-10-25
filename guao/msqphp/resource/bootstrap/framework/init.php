<?php declare(strict_types = 1);

//智能加载缓存文件
$aiload_cache_file = \msqphp\Environment::getPath('storage') . 'framework/aiload_cache_file.php';

//缓存文件存在
if (is_file($aiload_cache_file)) {
    //载入对应缓存
    include $aiload_cache_file;
    //初始化
    msqphp\Environment::init();
    //随机值删除
    random_int(1, 1000000) === 1000 && msqphp\base\file\File::delete($aiload_cache_file, true);
} else {
    //创建一个新缓存
    $loader = app()->loader;
    $loader->key('environment')->load();

    //初始化
    msqphp\Environment::init();

    if ($loader->last()) {
        $needful_classes = $loader->getLastNeedfulClasses();
        msqphp\base\file\File::write($aiload_cache_file, empty($needful_classes) ? '' : '<?php include \'' . implode('\';include \'', $loader->getLastNeedfulClasses()) . '\';', true);
    } else {
        $loader->deleteAll();
        $loader->update();
    }
    unset($loader);
}

unset($aiload_cache_file);