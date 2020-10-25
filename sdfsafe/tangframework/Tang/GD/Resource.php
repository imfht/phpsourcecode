<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\GD;
use Tang\GD\Exceptions\ImageFileNotFoundException;
use Tang\GD\Exceptions\ImageTypeNotSupportException;
use Tang\Services\FileService;

/**
 * 图形资源管理
 * @package Tang\GD
 */
class Resource
{
    /**
     * 资源
     * @var resource
     */
    public $resource;
    /**
     * 宽度
     * @var int
     */
    private $width = 0;
    /**
     * 高度
     * @var int
     */
    private $height = 0;
    /**
     * 图像类型
     * @var string
     */
    private $type = '';

    private function __construct($resource,$width,$height,$type)
    {
        $this->resource = $resource;
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
    }

    /**
     * 创建真彩色资源
     * @param $width
     * @param $height
     * @param $type
     * @return \Tang\GD\Resource
     */
    public static function createByTrueColor($width,$height,$type)
    {
        $type = strtolower($type);
        !in_array($type, array('jpeg','png','gif','bmp')) ? $type = 'png' : '';
        return new self(imagecreatetruecolor($width,$height), $width, $height, $type);
    }

    /**
     * 根据文件创建资源
     * @param $path
     * @return \Tang\GD\Resource
     * @throws Exceptions\ImageFileNotFoundException
     * @throws Exceptions\ImageTypeNotSupportException
     */
    public static function createByFile($path)
    {
        $file = FileService::getService();
        if(!$file->exists($path))
        {
            throw new ImageFileNotFoundException('[%s] graphics file does not exist',array($path));
        }
        $type = $file->getExtension($path);
        switch ($type)
        {
            case 'jpeg':case 'jpg':
            $type = 'jpeg';
            break;
            case 'gif':
            case 'png':
            case 'bmp':
                break;
            default:
                throw new ImageTypeNotSupportException('The %s file format is not supported',array($type));
        }
        $func = 'imagecreatefrom'.$type;
        $resource = $func($path);
        return new self($resource, imagesx($resource),imagesy($resource),$type);
    }

    /**
     * 浏览器输出图片
     */
    public function browseImage()
    {
        header('Content-type:image/'.$this->type);
        $this->makeFile();
    }

    /**
     * 输出文件
     * @param null $filePath
     */
    public function makeFile($filePath = null)
    {
        $func = 'image'.$this->type;
        $func($this->resource,$filePath);
    }
    /**
     * 获取宽度
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * 获取高度
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * 获取类型
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    public function __destruct()
    {
        if(is_resource($this->resource))
        {
            imagedestroy($this->resource);
        }
    }
}

/**
 * BMP 创建函数 因为PHP本身没有imagecreatefrombmp函数。所以要引用一下
 * @author simon
 * @param string $filename path of bmp file
 * @example who use,who knows
 * @return resource of GD
 */
function imagecreatefrombmp( $filename ){
    if ( !$f1 = fopen( $filename, "rb" ) )
        return FALSE;

    $FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
    if ( $FILE['file_type'] != 19778 )
        return FALSE;

    $BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
    $BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
    if ( $BMP['size_bitmap'] == 0 )
        $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
    $BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
    $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
    $BMP['decal'] = 4 - (4 * $BMP['decal']);
    if ( $BMP['decal'] == 4 )
        $BMP['decal'] = 0;

    $PALETTE = array();
    if ( $BMP['colors'] < 16777216 ){
        $PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
    }

    $IMG = fread( $f1, $BMP['size_bitmap'] );
    $VIDE = chr( 0 );

    $res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
    $P = 0;
    $Y = $BMP['height'] - 1;
    while( $Y >= 0 ){
        $X = 0;
        while( $X < $BMP['width'] ){
            if ( $BMP['bits_per_pixel'] == 32 ){
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
                $B = ord(substr($IMG, $P,1));
                $G = ord(substr($IMG, $P+1,1));
                $R = ord(substr($IMG, $P+2,1));
                $color = imagecolorexact( $res, $R, $G, $B );
                if ( $color == -1 )
                    $color = imagecolorallocate( $res, $R, $G, $B );
                $COLOR[0] = $R*256*256+$G*256+$B;
                $COLOR[1] = $color;
            }elseif ( $BMP['bits_per_pixel'] == 24 )
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
            elseif ( $BMP['bits_per_pixel'] == 16 ){
                $COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 8 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 4 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 2) % 2 == 0 )
                    $COLOR[1] = ($COLOR[1] >> 4);
                else
                    $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 1 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 8) % 8 == 0 )
                    $COLOR[1] = $COLOR[1] >> 7;
                elseif ( ($P * 8) % 8 == 1 )
                    $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                elseif ( ($P * 8) % 8 == 2 )
                    $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                elseif ( ($P * 8) % 8 == 3 )
                    $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                elseif ( ($P * 8) % 8 == 4 )
                    $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                elseif ( ($P * 8) % 8 == 5 )
                    $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                elseif ( ($P * 8) % 8 == 6 )
                    $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                elseif ( ($P * 8) % 8 == 7 )
                    $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }else
                return FALSE;
            imagesetpixel( $res, $X, $Y, $COLOR[1] );
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P += $BMP['decal'];
    }
    fclose( $f1 );
    return $res;
}