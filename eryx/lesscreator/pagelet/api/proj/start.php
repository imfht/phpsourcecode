<?php


$rsp = array(
    'status'  => 200,
    'message' => null,
    'data'    => array(
        'info'  => array(),
        'menus' => array()
    ),
);


try {

    if (!isset($this->req->proj) || strlen($this->req->proj) < 1) {
        throw new Exception("Project Not Found", 400);
    }

    $projInfo = lesscreator_proj::info($this->req->proj);
    if (!isset($projInfo['projid'])) {
        throw new Exception("Project Not Found", 400);
    }

    // Project tioninformation
    $rsp['data']['info'] = array(
        'projid'  => $projInfo['projid'],
        'name'    => $projInfo['name'],
        'version' => $projInfo['version'],
    );

    // Core Extension
    $rsp['data']['menus'][] = array(
        'fn'    => 'lcProjSet()',
        'title' => $this->T('Settings'),
        'ico'   => 'std/set2-32.png',
    );
    $rsp['data']['menus'][] = array(
        'fn'    => '#',
        'title' => $this->T('Packages'),
        'ico'   => 'std/gift2-32.png',
    );
    $rsp['data']['menus'][] = array(
        'fn'    => 'lcProjLaunch(\'Run and Deply\')',
        'title' => $this->T('Run and Deply'),
        'ico'   => 'std/play-32.png',
    );

    // Extension
    $rsp['data']['exts'][] = array(
        'fn'    => '_lc_nav_terminal()',
        'title' => $this->T('Terminal'),
        'ico'   => 'std/term-32.png',
    );
    $rsp['data']['exts'][] = array(
        'fn'    => '_proj_plugins_lessdata()',
        'title' => $this->T('Database'),
        'ico'   => 'std/ext-db2-32.png',
    );
    $rsp['data']['exts'][] = array(
        'fn'    => '_proj_plugins_phpyaf()',
        'title' => $this->T('Yaf Framework'),
        'ico'   => 'std/ext-yaf-32.png',
    );

} catch (Exception $e) {

    $rsp['status']  = $e->getCode();
    $rsp['message'] = $e->getMessage();
}

die(json_encode($rsp));


