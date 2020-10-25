<?php

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';


load()->classs('wxapp/wxappcloud');
load()->func('communication');
//
$tester = new \Testify\Testify('hello');
$tester->test('getComponentToken', function(){
	$wxauth = new WxAppAuthApi('wx991ec14508b7d1e7',
		'deba8d99fd614522bf6f9c074f7801c9',
		'ticket@@@S6joQZVTx_FsUrf8iMwV_OXjiNDCMj-ITTKk4-mhoed_c0yNR0jTAPd3LjRtvuFFTecrjEsHAR5YWa98VQD5TA');


	$token  = $wxauth->getComponentAccessToken();
	var_dump($token);
});
$tester->test('xml解密', function(){

	/**
	 * 公众号消息校验Token
	htdfYfdQPw277jTt274J7ltyRQ2BL72q
	公众号消息加解密Key
	MrQMqLsKCpUxOeNd2McXN4g54WLyqrUBED7BnvxWQhB
	 *
	 *
	 * <xml><AppId><![CDATA[wx991ec14508b7d1e7]]></AppId>
	<CreateTime>1504073875</CreateTime>
	<InfoType><![CDATA[component_verify_ticket]]></InfoType>
	<ComponentVerifyTicket><![CDATA[ticket@@@7CiP6eLB1jG1jG_MEQXDOi3dmWe2uBvOX_y-OsbxlPh9R0Ds9HtwApjNYutja0mtM5i5XdrOIFb4kl_uezA8_Q]]></ComponentVerifyTicket>
	</xml>
	 */
	$XML = <<<EOT
	<xml>
    <AppId><![CDATA[wx991ec14508b7d1e7]]></AppId>
    <Encrypt><![CDATA[3qhCNQ1S8a3Nkc+10qbtHTZf/xmPuUeT+TYGI+wwtIWSyEvkOh+WS1bE/EXi9d4Ve9nbm5lnXBcD/RO5FuNnMAuDw2vbnne8oOfpaRKpUVyToKsupvZL1krPMhmzyKt39+ueoy3XDMO5jfNJ2pjZa+S7dscFLPGFDJN3FUE61VMsUmYtAu8WE/6WGTpvkUiA9kLW8BqGA6Bo+3CmiwhZQqqLUce7MSIX7cioybafhTKTfbLfLsJjsUj5nPu1CEFgsJDMYzvatQ3RQVPap531LNHFJZMAmOAux4rIW+oEoETxgMvgkLhLhGOgzdCbsCOluobBGzngAwpb2jt3HGbZ4NtY3n+ZcD6kvIaNGdSA+lz74pp8kNAXCqpSiqPhkYqSPhawODg/X1784smsksmVYaLVEZ9CGLw+5RX7HYwhmneuTRVGwSaP1tmPRLFckwvABJgqhrb1WYVcvHhzGByNiQ==]]></Encrypt>
</xml>
EOT;
	$xmlobj = simplexml_load_string($XML);
	$data = aes_decode($xmlobj->Encrypt, 'MrQMqLsKCpUxOeNd2McXN4g54WLyqrUBED7BnvxWQhB');
	var_dump($data);
});

//wxb0e582aaff9a169d
//$codetoken = 'lDfF4QvjIoSArGa9Gu0OsMhVAOwY-19zKFcw6kUN5O37TfgDKlRn2OoCvOYyFemZd_16_IXb1N6M3ZZMsGll0LJ1nAQcGzqudWj-iYJuSwrDZeGz-nCRrjuttwsVktUkOOAgAIDIIU';
//$codeapi = new WxAppCodeApi($codetoken);
//$extjson = array('ext'=>array('a'=>1,'b'=>2), 'extAppid'=> 'wxb0e582aaff9a169d');
//$result = $codeapi->commitCode(2, $extjson, '0.0.1', '测试提交代码');
//var_dump($result);

$XML = <<<EOT
	<xml>
    <AppId><![CDATA[wx991ec14508b7d1e7]]></AppId>
    <Encrypt><![CDATA[3qhCNQ1S8a3Nkc+10qbtHTZf/xmPuUeT+TYGI+wwtIWSyEvkOh+WS1bE/EXi9d4Ve9nbm5lnXBcD/RO5FuNnMAuDw2vbnne8oOfpaRKpUVyToKsupvZL1krPMhmzyKt39+ueoy3XDMO5jfNJ2pjZa+S7dscFLPGFDJN3FUE61VMsUmYtAu8WE/6WGTpvkUiA9kLW8BqGA6Bo+3CmiwhZQqqLUce7MSIX7cioybafhTKTfbLfLsJjsUj5nPu1CEFgsJDMYzvatQ3RQVPap531LNHFJZMAmOAux4rIW+oEoETxgMvgkLhLhGOgzdCbsCOluobBGzngAwpb2jt3HGbZ4NtY3n+ZcD6kvIaNGdSA+lz74pp8kNAXCqpSiqPhkYqSPhawODg/X1784smsksmVYaLVEZ9CGLw+5RX7HYwhmneuTRVGwSaP1tmPRLFckwvABJgqhrb1WYVcvHhzGByNiQ==]]></Encrypt>
</xml>
EOT;
//WxAppCloud::updateThreePlatform($XML);
//
$cloud = new WxAppCloud('wx991ec14508b7d1e7');
//$cloud->
//ChmA85nLAZ_gqI1PRT9ajHEXLRwepfCY3qbGEqTtxoGlYL7oNIJmR7xPudRIe-4WkwItqxg2vqqsJJJrXT8c4CgG2o0rAJjqxWtuKBa8rCbyULvru93uxfd04LmKHLJYCXNcACAYTJ
$cloud->getThreeAccessToken();
//$repo = new WxAppRepository('wx991ec14508b7d1e7');
//$repo->updateThreePlatformToken('ChmA85nLAZ_gqI1PRT9ajHEXLRwepfCY3qbGEqTtxoGlYL7oNIJmR7xPudRIe-4WkwItqxg2vqqsJJJrXT8c4CgG2o0rAJjqxWtuKBa8rCbyULvru93uxfd04LmKHLJYCXNcACAYTJ');
//$dbdata = array(
//	'authorizer_appid' => 'wxb0e582aaff9a169d',
//	'authorizer_access_token'=>'2',
//	'authorizer_refresh_token'=>'2',
//	'expires_in'=> '3',
//	'three_platform_appid'=> 'wx991ec14508b7d1e7',
//);
//$result =$repo->updateOrCreate($dbdata,'wxb0e582aaff9a169d');
////	$dbdata);
//
//var_dump($result);


//$repo = new WxAppRepository('wx991ec14508b7d1e7');
//
//$update = $repo->updateThreePlatformTicket('123333');
//
//var_dump($update);



