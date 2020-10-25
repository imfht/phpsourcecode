<?php

$app->get('/marketing/banner/all',function()use($app){
    $uti = $app->utility;
    $user = getCurrentUser($app);
    $bans = RjBanner::find();
    $bans = $uti->getDBResultArrays($bans);
    foreach($bans as &$o){
        if($o['upload_file_name'] != null){
            $o['image'] = array(
                'name' =>$o['upload_file_name'],
                'url' => getImageUrl($o['upload_file_name'])
            );
        }
    }
    $uti->setItem('banners',$bans);
    $uti->setSuccessTrue();
});

$app->get('/marketing/home',function()use($app){
    $uti = $app->utility;
    $uti->setSuccessTrue();
    $res = array();

    $cs = $uti->getDBResultArrays((RjCategory::find(array('public = 1'))));

    foreach($cs as &$c){
        $c['image'] = array(
            'name' => $c['upload_file_name'],
            'url' => getImageUrl($c['upload_file_name'])
        );
        $ps = $uti->getDBResultArrays((
            $app->modelsManager->createBuilder()
                ->from('RjProduct')
                ->where('category_id='.$c['id'])
                ->andWhere('public=1')
                ->andWhere('number>0')
                ->orderBy('create_date DESC')
                ->limit(3)
                ->getQuery()
                ->execute()
        ));

        $ids  = array();
        $ids[] = -1;
        foreach($ps as &$p){
            $p['images'] = getProductImages($app,$p['id']);
            $ids[] = $p['id'];
        }
        $c['newProducts'] = $ps;

        $ps = $uti->getDBResultArrays(($app->modelsManager->createBuilder()
            ->from('RjProduct')
            ->where('category_id='.$c['id'])
            ->andWhere('public=1')
            ->andWhere('number>0')
            ->notInWhere('id',$ids)
            ->limit(3)
            ->orderBy('sold_number DESC')
            ->getQuery()
            ->execute()));
        foreach($ps as &$p){
            $p['images'] =getProductImages($app,$p['id']);
        }
        $c['hotProducts'] = $ps;
    }
    $uti->setItem('categories',$cs);
});

