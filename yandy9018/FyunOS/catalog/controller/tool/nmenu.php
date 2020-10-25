<?php  
class ControllerToolNmenu extends Controller {
	public function index() {
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=-cftsb0ngBTVN2iZ16ySNaf3vuY18qFdVlDBmHmo4ALaB78KoXmhkhrznkJAdRXGX7JEdfygxLH260zE0L0zwPoqyKMNc6icAfQI4ihpbFY0xyLuQ5IiQV61I1zA7fKiIBXN5acIVBkpvXNRbFcAbA");

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$tmpInfo = curl_exec($ch);

if (curl_errno($ch)) {

 return curl_error($ch);

}

curl_close($ch);

return $tmpInfo;


$data = '{

    "button":[
    {

         "type":"view",

         "name":"首页",

         "url":"http://d.fyun.mobi/"

     },

     {

          "type":"view",

          "name":"开始点餐",

          "url":"http://d.fyun.mobi/index.php?route=account/order"

     },
	  {

         "type":"view",

         "name":"会员卡",

         "url":"http://d.fyun.mobi/index.php?route=account/reward"

     }]

}';

 

 

echo createMenu($data);



  	}
}
?>