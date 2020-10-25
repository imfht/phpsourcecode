<?php
function getRemoteLmlPhp(){
    $cache_filename = 'lml.min.php';
    $remotelib = 'http://git.oschina.net/leiminglin/LMLPHP/raw/master/lml.min.php';
    if( file_exists( $cache_filename ) ) {
        $cachemtime = filemtime($cache_filename);
        if( $cachemtime + 86400 > time() ){
            require $cache_filename;
            return;
        }
        $header = get_headers($remotelib);
        $lastmtime = 0;
        foreach ($header as $k){
            if( preg_match('/^Last-Modified:/i', $k) ){
                $lastmtime = strtotime(preg_replace('/^Last-Modified:/i', '', $k));
                break;
            }
        }
        if( $lastmtime <= $cachemtime ){
            touch($cache_filename);
            require $cache_filename;
            return;
        }
    }
    $code = file_get_contents( $remotelib );
    file_put_contents($cache_filename, $code);
    eval('?>'.$code);
}
getRemoteLmlPhp();

lml()->app()->addLastRouter(array('sqlexec'))->run();

function sqlexec(){

    $dbconfig = require APP_PATH.'conf/dbconfig.php';

    if(in_array(C_MODULE, array_keys($dbconfig))){
        $db = MysqlPdoEnhance::getInstance($dbconfig[C_MODULE]);
        $sql = isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:'';
        $rs = $db->query($sql);
        var_dump($rs);
    }else{
        echo 'not found!';
    }
}



