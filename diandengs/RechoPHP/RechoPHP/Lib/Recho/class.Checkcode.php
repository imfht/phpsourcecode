<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * configure class
 * $Author: Recho $
 * $create time: 2010-02-13 09:27
 * $last update time: 2010-02-13 09:27 Recho $
 */
class Checkcode{
	
   private $FontSize;
   private $Yzmcount;
   private $YzmFontArray;
   private $BGcolor;
   private $strings;
   private $strArray;
   private $NumArray;
   private $NumStr;
   private $sessStr;
   private $image;
   
   //构造函数
   function Checkcode($Yzmcount=4,$Fonts="ariblk.TTF",$sessStr="getImage",$FontSize=NULL, $secureStr='')
   {
       $this->FontSize=mt_rand(18,28);
       $this->Yzmcount=NULL;
       $this->YzmFontArray=array();
       $this->BGcolor=255;
       $this->strings=NULL;
       $this->strArray=NULL;
       $this->NumArray=NULL;
       $this->NumStr=NULL;
       $this->sessStr=NULL;
       $this->image=NULL;
   	   $this->getDatas( $Yzmcount, $Fonts, $sessStr, $FontSize);
	   $this->createImage();
       $this->createganYao();
       $this->showPic( $secureStr);
   }
   
   //接收需要的数据
   function getDatas($Yzmcount=4,$Fonts="ariblk.TTF",$sessStr="getImage",$FontSize=NULL){
	   $Fonts = dirname( __FILE__).'/fonts/'.$Fonts;
       $this->FontSize=$FontSize;
       $this->Yzmcount=$Yzmcount;
       $this->YzmFontArray=explode(",",$Fonts);
       $this->strings="0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
       $this->strArray=explode(",",$this->strings);
       $this->sessStr=$sessStr;
   }
   
   //绘制图像
   function createImage(){
       $this->image=imagecreate(23*$this->Yzmcount,39);
       imagefill($this->image,0,0,imagecolorallocate($this->image,$this->BGcolor,$this->BGcolor,$this->BGcolor));
       for($i=0;$i<$this->Yzmcount;$i++)
       {
           $this->NumArray[$i]=$this->strArray[mt_rand(0,count($this->strArray)-1)];
           $this->NumStr.=$this->NumArray[$i];
       }
       for($i=0;$i<$this->Yzmcount;$i++)
           imagettftext($this->image,$this->FontSize==NULL?mt_rand(18,28):$this->FontSize,mt_rand(-12,12),5+$i*20,mt_rand(28,39),imagecolorallocate($this->image,mt_rand(0,180),mt_rand(0,180),mt_rand(0,180)),$this->YzmFontArray[mt_rand(0,count($this->YzmFontArray)-1)],$this->NumArray[$i]);
   }
   
   //绘制噪扰
   function createganYao(){
       for($i=0;$i<333;$i++)
           imagesetpixel($this->image,mt_rand()%23*$this->Yzmcount,mt_rand()%39,imagecolorallocate($this->image,mt_rand(100,200),mt_rand(100,200),mt_rand(100,200)));
   }
   
   //输出图像
   function showPic( $secureStr){
       ob_clean();
       Header("content-type: image/png");
       $_SESSION[$this->sessStr]=md5( $secureStr.strtolower($this->NumStr));
       imagepng($this->image);
       imagedestroy($this->image);
   }
}