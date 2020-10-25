<?php
/**
 * 调用示例
 *
 * 使用方法 $this->load_class('verification_code');
 * */
 if(!defined('DIR')){
	exit('Please correct access URL.');
}

session_start();
$randCode = '';
$chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPRSTUVWXYZ23456789';
for ( $i = 0; $i < 4; $i++ )
{
    $randCode .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
}
$_SESSION['code'] = strtoupper($randCode);            // 记录session
ImageCode($randCode, 60);                // 显示GIF动画

/**
 * ImageCode 生成GIF图片验证
 * @param $string 字符串
 * @param $width 宽度
 * @param $height 高度
 * */
function ImageCode($string = '', $width = 75, $height = 25)
{
    $authstr = $string ? $string : ((time() % 2 == 0) ? mt_rand(1000, 9999) : mt_rand(10000, 99999));

    $board_width = $width;
    $board_height = $height;
    // 生成一个32帧的GIF动画
    for($i = 0; $i < 32; $i++)
    {
        ob_start();
        $image = imagecreate($board_width, $board_height);
        imagecolorallocate($image, 0,0,0);
        // 设定文字颜色数组
         $colorList[] = ImageColorAllocate($image, 15,73,210);
         $colorList[] = ImageColorAllocate($image, 0,64,0);
         $colorList[] = ImageColorAllocate($image, 0,0,64);
         $colorList[] = ImageColorAllocate($image, 0,128,128);
         $colorList[] = ImageColorAllocate($image, 27,52,47);
         $colorList[] = ImageColorAllocate($image, 51,0,102);
         $colorList[] = ImageColorAllocate($image, 0,0,145);
         $colorList[] = ImageColorAllocate($image, 0,0,113);
         $colorList[] = ImageColorAllocate($image, 0,51,51);
         $colorList[] = ImageColorAllocate($image, 158,180,35);
         $colorList[] = ImageColorAllocate($image, 59,59,59);
         $colorList[] = ImageColorAllocate($image, 0,0,0);
         $colorList[] = ImageColorAllocate($image, 1,128,180);
         $colorList[] = ImageColorAllocate($image, 0,153,51);
         $colorList[] = ImageColorAllocate($image, 60,131,1);
         $colorList[] = ImageColorAllocate($image, 0,0,0);
         $fontcolor = ImageColorAllocate($image, 0,0,0);
         $gray = ImageColorAllocate($image, 245,245,245);

        $color = imagecolorallocate($image, 255,255,255);
        $color2 = imagecolorallocate($image, 255,0,0);

        imagefill($image, 0, 0, $gray);

        $space = 15;        // 字符间距
        if($i > 0)            // 屏蔽第一帧
        {
            for ($k = 0; $k < strlen($authstr); $k++)
            {
                $colorRandom = mt_rand(0,sizeof($colorList)-1);
                $float_top = rand(0,4);
                $float_left = rand(0,3);
                imagestring($image, 6, $space * $k, $float_top, substr($authstr, $k, 1), $colorList[$colorRandom]);
            }
        }

        for ($k = 0; $k < 20; $k++)
        {
            $colorRandom = mt_rand(0,sizeof($colorList)-1);
             imagesetpixel($image, rand()%70 , rand()%15 , $colorList[$colorRandom]);

        }
        // 添加干扰线
        for($k = 0; $k < 3; $k++)
        {
            $colorRandom = mt_rand(0, sizeof($colorList)-1);
            // $todrawline = rand(0,1);
            $todrawline = 1;
            if($todrawline)
            {
                imageline($image, mt_rand(0, $board_width), mt_rand(0,$board_height), mt_rand(0,$board_width), mt_rand(0,$board_height), $colorList[$colorRandom]);
            }
            else
            {
                $w = mt_rand(0,$board_width);
                $h = mt_rand(0,$board_width);
                imagearc($image, $board_width - floor($w / 2) , floor($h / 2), $w, $h,  rand(90,180), rand(180,270), $colorList[$colorRandom]);
            }
        }
        imagegif($image);
        imagedestroy($image);
        $imagedata[] = ob_get_contents();
        ob_clean();
        ++$i;
    }

    $gif = new GIFEncoder($imagedata);
    Header ('Content-type:image/gif');
    echo $gif->GetAnimation();
}


/**
 * GIFEncoder类
 * */
Class GIFEncoder
{
    var $GIF = "GIF89a";                /* GIF header 6 bytes        */
    var $VER = "GIFEncoder V2.06";        /* Encoder version             */

    var $BUF = Array ( );
    var $LOP =  0;
    var $DIS =  2;
    var $COL = -1;
    var $IMG = -1;

    var $ERR = Array (
        'ERR00' =>"Does not supported function for only one image!",
        'ERR01' =>"Source is not a GIF image!",
        'ERR02' =>"Unintelligible flag ",
        'ERR03' =>"Could not make animation from animated GIF source",
    );

    function __construct($GIF_src, $GIF_dly = 100, $GIF_lop = 0, $GIF_dis = 0,  $GIF_red = 0, $GIF_grn = 0, $GIF_blu = 0, $GIF_mod = 'bin' )
    {
        if (!is_array($GIF_src) && !is_array($GIF_tim))
        {
            printf ( "%s: %s", $this->VER, $this->ERR['ERR00']);
            exit( 0 );
        }
        $this->LOP = ($GIF_lop > -1) ? $GIF_lop : 0;
        $this->DIS = ($GIF_dis > -1) ? (( $GIF_dis < 3 ) ? $GIF_dis : 3) : 2;
        $this->COL = ($GIF_red > -1 && $GIF_grn > -1 && $GIF_blu > -1) ? ($GIF_red | ($GIF_grn << 8) | ($GIF_blu << 16)) : -1;

        for ($i = 0, $src_count = count($GIF_src); $i < $src_count; $i++ )
        {
            if (strToLower( $GIF_mod ) == "url")
            {
                $this->BUF[] = fread (fopen($GIF_src [$i], "rb"), filesize ($GIF_src [$i]));
            }
            elseif(strToLower($GIF_mod) == "bin")
            {
                $this->BUF [ ] = $GIF_src [ $i ];
            }
            else
            {
                printf("%s: %s ( %s )!", $this->VER, $this->ERR [ 'ERR02' ], $GIF_mod);
                exit(0);
            }
            if (substr($this->BUF[$i], 0, 6) != "GIF87a" && substr($this->BUF [$i], 0, 6) != "GIF89a")
            {
                printf( "%s: %d %s", $this->VER, $i, $this->ERR ['ERR01']);
                exit(0);
            }
            for ($j = (13 + 3 * (2 << (ord($this->BUF[$i]{10}) & 0x07 ))), $k = TRUE; $k; $j++)
            {
                switch ($this->BUF [$i]{$j})
                {
                    case "!":
                        if ((substr($this->BUF[$i], ($j + 3), 8)) == "NETSCAPE")
                        {
                                printf( "%s: %s ( %s source )!", $this->VER, $this->ERR ['ERR03'], ($i + 1));
                                exit( 0 );
                        }
                        break;
                    case ";":
                        $k = FALSE;
                        break;
                }
            }
        }
        GIFEncoder::GIFAddHeader();
        for($i = 0, $count_buf = count($this->BUF); $i < $count_buf; $i++)
        {
            GIFEncoder::GIFAddFrames($i, $GIF_dly[$i]);
        }
        GIFEncoder::GIFAddFooter();
    }

    function GIFAddHeader ( )
    {
        $cmap = 0;

        if (ord($this->BUF[0]{10}) & 0x80 )
        {
            $cmap = 3 * ( 2 << ( ord ( $this->BUF [ 0 ]{10} ) & 0x07 ));

            $this->GIF .= substr ( $this->BUF [ 0 ], 6, 7);
            $this->GIF .= substr ( $this->BUF [ 0 ], 13, $cmap);
            $this->GIF .= "!\377\13NETSCAPE2.0\3\1" . GIFEncoder::GIFWord ( $this->LOP ) . "\0";
        }
    }

    function GIFAddFrames ( $i, $d )
    {

        $Locals_str = 13 + 3 * (2 <<(ord($this->BUF[$i]{10}) & 0x07));

        $Locals_end = strlen($this->BUF[$i]) - $Locals_str - 1;
        $Locals_tmp = substr ($this->BUF[$i], $Locals_str, $Locals_end);

        $Global_len = 2 << (ord( $this->BUF [0]{10} ) & 0x07 );
        $Locals_len = 2 << (ord( $this->BUF[$i]{10}) & 0x07);

        $Global_rgb = substr($this->BUF[0], 13, 3 * (2 << ( ord ( $this->BUF[0]{10} ) & 0x07)));
        $Locals_rgb = substr ( $this->BUF[$i], 13, 3 * (2 << ( ord ( $this->BUF[$i]{10} ) & 0x07)));

        $Locals_ext = "!\xF9\x04" . chr(($this->DIS << 2) + 0) . chr(($d >> 0) & 0xFF) . chr(($d >> 8) & 0xFF) . "\x0\x0";

        if ( $this->COL > -1 && ord($this->BUF[$i]{10}) & 0x80)
        {
            for($j = 0; $j < (2 << (ord( $this->BUF[$i]{10}) & 0x07)); $j++ )
            {
                if(ord ($Locals_rgb{3 * $j + 0}) == ($this->COL >>  0) & 0xFF && ord ( $Locals_rgb { 3 * $j + 1 } ) == ( $this->COL >>  8 ) & 0xFF && ord ( $Locals_rgb { 3 * $j + 2 } ) == ( $this->COL >> 16 ) & 0xFF )
                {
                    $Locals_ext = "!\xF9\x04" . chr(($this->DIS << 2) + 1) . chr (( $d >> 0) & 0xFF) . chr (( $d >> 8) & 0xFF) . chr ($j) . "\x0";
                    break;
                }
            }
        }
        switch ( $Locals_tmp { 0 } )
        {
            case "!":
                $Locals_img = substr($Locals_tmp, 8, 10);
                $Locals_tmp = substr($Locals_tmp, 18, strlen ($Locals_tmp) - 18);
                break;
            case ",":
                $Locals_img = substr($Locals_tmp, 0, 10);
                $Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);
                break;
        }
        if ( ord ( $this->BUF[$i]{10} ) & 0x80 && $this->IMG > -1 )
        {
            if ( $Global_len == $Locals_len )
            {
                if ( GIFEncoder::GIFBlockCompare ( $Global_rgb, $Locals_rgb, $Global_len ) )
                {
                    $this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
                }
                else
                {
                    $byte  = ord ( $Locals_img{9});
                    $byte |= 0x80;
                    $byte &= 0xF8;
                    $byte |= ( ord ( $this->BUF [ 0 ]{10}) & 0x07);
                    $Locals_img{9} = chr($byte);
                    $this->GIF .= ($Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp);
                }
            }
            else
            {
                $byte  = ord($Locals_img{9});
                $byte |= 0x80;
                $byte &= 0xF8;
                $byte |= (ord($this->BUF[$i]{10}) & 0x07);
                $Locals_img {9} = chr($byte);
                $this->GIF .= ($Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp);
            }
        }
        else
        {
            $this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
        }
        $this->IMG  = 1;
    }

    function GIFAddFooter ( ) {
        $this->GIF .= ";";
    }

    function GIFBlockCompare ( $GlobalBlock, $LocalBlock, $Len )
    {
        for ( $i = 0; $i < $Len; $i++ )
        {
            if($GlobalBlock { 3 * $i + 0 } != $LocalBlock { 3 * $i + 0 } || $GlobalBlock { 3 * $i + 1 } != $LocalBlock { 3 * $i + 1 } || $GlobalBlock { 3 * $i + 2 } != $LocalBlock{3 * $i + 2})
            {
                return ( 0 );
            }
        }
        return ( 1 );
    }

    function GIFWord ( $int )
    {
       return ( chr ( $int & 0xFF ) . chr ( ( $int >> 8 ) & 0xFF ) );
    }

    function GetAnimation ( )
    {
        return ($this->GIF);
    }
}
