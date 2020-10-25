<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/12/21
 * Time: 上午10:14
 */

namespace ga\captcha;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\web\Response;

class CaptchaAction extends Action
{

    public $width = 140;

    public $height = 50;

    public $fontFile = '@vendor/ga/captcha/font/HansKendrickV-Regular.ttf';

    public $maxAngle = 10;

    public $minAngle = -10;

    /**
     * @var int If set more than six, it still is six.
     */
    public $maxChar = 6;
    /**
     * @var int If 0 not limit the number of verification.
     */
    public $maxVerification = 0;

    public $maxFontSize = 25;

    public $minFontSize = 20;

    protected $randomString = '';

    protected $imageResource;

    private $noisyPoints = 50;

    private $maxLine = 3;

    public $marginLR = 10;

    public $sessionKey = 'ga/Captcha';

    protected function beforeRun(){

        parent::beforeRun();
        if(extension_loaded('gd') === false){
            throwException(new Exception("GA Library not found."));
        }
        return true;
    }

    public function run(){
        $this->changeSession();
        return $this->createImage();
    }

    public function changeSession(){

        $session = Yii::$app->session;
        $session->open();
        $name = $this->sessionKey;
        $session[$name] = $this->getRandomString();

        return $session[$name];
    }

    public function createImage(){

        $this->changeHTTPHeader();

        $image = $this->getImageResource();

        $this->draw($image);

        $this->noisyImage($image);

        return $this->outputImage($image);
    }

    public function getImageResource(){

        if(empty($this->imageResource)){
            $this->imageResource = imagecreatetruecolor($this->width, $this->height);
        }
        return $this->imageResource;
    }


    public function draw($image){


        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $bgColor);

        if($this->maxChar > 6){
            $this->maxChar = 6;
        }

        $width = ($this->width - 2 * $this->marginLR) / 6;
        for($i = 0; $i < $this->maxChar; $i++){
            $size = mt_rand($this->minFontSize, $this->maxFontSize);
            $angle = mt_rand($this->minAngle, $this->maxAngle);
            $x = $i * $width + $this->marginLR;
            $fontHeight = imagefontheight($size);
            imagettftext($image ,$size ,$angle, $x, $this->height/2 + $fontHeight / 2, $this->randColor($image), Yii::getAlias($this->fontFile), substr($this->getRandomString(), $i, 1));
        }
    }

    public function noisyImage($image){

        for($i = 0; $i < $this->noisyPoints; $i++){
            imagesetpixel($image, mt_rand(0, $this->width), mt_rand(0, $this->height), $this->randColor($image));
        }

        for($i = 0; $i < $this->maxLine; $i++){
            imagearc($this->getImageResource(), mt_rand(0, $this->width), mt_rand(0, $this->height) , mt_rand(0, $this->width) / 2, mt_rand(0, $this->height) / 2, mt_rand($this->minAngle, $this->maxAngle), mt_rand($this->minAngle, $this->maxAngle), $this->randColor($this->getImageResource()));
        }
    }

    public function outputImage($image){

        ob_start();
        imagepng($image);
        imagedestroy($image);
        return ob_get_clean();
    }

    protected function randColor($image){

        return imagecolorallocate($image, mt_rand(0, 220), mt_rand(0, 220), mt_rand(0, 220));
    }

    public function getRandomString(){

        if(empty($this->randomString)){
            $chars = '123456789zxcvbnmasdfghjklqwertyuiopZXCVBNMASDFGHJKLQWERTYUIO';
            for ($i = 0; $i < $this->maxChar; $i++) {
                $this->randomString .= substr($chars, mt_rand(0, 59), 1);
            }
        }
        return $this->randomString;
    }

    public function changeHTTPHeader(){
        Yii::$app->getResponse()->getHeaders()
            ->set('Pragma', 'public')
            ->set('Expires', '0')
            ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->set('Content-Transfer-Encoding', 'binary')
            ->set('Content-type', 'image/png');
        Yii::$app->response->format = Response::FORMAT_RAW;
    }

    public function validate($value, $caseSensitive){
        $session = Yii::$app->session;
        if(!empty($session[$this->sessionKey . 'count']) && ($this->maxVerification != 0) && ($session[$this->sessionKey . 'count'] > $this->maxVerification)){
            return false;
        }
        $session[$this->sessionKey . 'count'] += 1;
        return $caseSensitive ? $value === $session->get($this->sessionKey) : strcasecmp($value , $session->get($this->sessionKey));
    }
}