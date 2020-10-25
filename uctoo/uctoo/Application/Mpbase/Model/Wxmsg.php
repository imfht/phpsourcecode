<?php
/**
 * Created by PhpStorm.
 * User: Uctoo-Near
 * Date: 2016/4/13
 * Time: 17:04
 */

namespace app\mpbase\model;
use think\Model;

class Wxmsg extends Model
{

    public function wxmsg($param){


        switch($param['type']){

            case 'text':

                $param['weObj']->text($param['replay_msg']['detile']);
                break;

            case 'picture':

                $i = 0;
                $res = $param['replay_msg'];
                $pic =explode(',',$res['pic']);

                $reData[0]['Title'] = $res['title0'];
                $reData[0]['Description'] = $res['detile0'];

                $reData[0]['PicUrl'] = get_cover_url($pic[$i]) ; //'http://images.domain.com/templates/domaincom/logo.png';

                $reData[0]['Url'] = $res['url0'];

                if($res['title1']){
                    $i++;
                    $reData[$i]['Title'] = $res['title1'];
                    $reData[$i]['Description'] = $res['detile1'];
                    $reData[$i]['PicUrl'] = get_cover_url($pic[$i]) ; //'http://images.domain.com/templates/domaincom/logo.png';
                    $reData[$i]['Url'] = $res['url1'];
                }

                if($res['title2']){
                    $i++;
                    $reData[$i]['Title'] = $res['title2'];
                    $reData[$i]['Description'] = $res['detile2'];
                    $reData[$i]['PicUrl'] = get_cover_url($pic[$i]) ; //'http://images.domain.com/templates/domaincom/logo.png';
                    $reData[$i]['Url'] = $res['url2'];
                }

                if($res['title3']){
                    $i++;
                    $reData[$i]['Title'] = $res['title3'];
                    $reData[$i]['Description'] = $res['detile3'];
                    $reData[$i]['PicUrl'] = get_cover_url($pic[$i]) ; //'http://images.domain.com/templates/domaincom/logo.png';
                    $reData[$i]['Url'] = $res['url3'];
                }

                if($res['title4']){
                    $i++;
                    $reData[$i]['Title'] = $res['title4'];
                    $reData[$i]['Description'] = $res['detile4'];
                    $reData[$i]['PicUrl'] = get_cover_url($pic[$i]) ; //'http://images.domain.com/templates/domaincom/logo.png';
                    $reDta[$i]['Url'] = $res['url4'];
                }

                $param['weObj']->news($reData);
                break;


        }







    }





}