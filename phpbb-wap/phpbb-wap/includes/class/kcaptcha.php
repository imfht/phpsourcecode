<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* 验证码生成类
*/

class KCaptcha
{

	var $alphabet;
	var $allowed_symbols; 
	var $fontsdir;	
	var $length; 
	var $width;
	var $height;
	var $fluctuation_amplitude;
	var $no_spaces;
	var $show_credits; 
	var $credits = ''; 
	var $foreground_color;
	var $background_color;
	var $jpeg_quality;
	
	var $fonts = array();
	var $code = '';
	
	function __construct($code)
	{
		// 所有字母
		$this->alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
		
		// 允许出现的字母
		$this->allowed_symbols = '23456789abcdeghkmnpqsuvxyz'; 
		
		// 字体基础存放目录
		$this->fontsdir = ROOT_PATH . 'images/fonts';	
		
		// 验证码字符串长度
		$this->length = 5; 
		
		// 验证码图片的宽度
		$this->width = 90;
		
		// 验证码图片的高度
		$this->height = 50;
		
		// 波动幅度
		$this->fluctuation_amplitude = 10;
		
		// 不带空格
		$this->no_spaces = true;
		
		// ?
		$this->show_credits = false; 
		
		// ?
		$this->credits = 'abc'; 
		
		// 前景颜色
		$this->foreground_color = array(mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		
		// 背景颜色
		$this->background_color = array(mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
		
		// jpeg质量
		$this->jpeg_quality = 100;
		
		$this->code = $code;
		
		$this->generate_image();

	}
	
	function generate_image()
	{	
	
		$fontsdir_absolute = $this->fontsdir;
		
		if ($handle = opendir($fontsdir_absolute))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (preg_match('/\.png$/i', $file))
				{
					$this->fonts[] = $fontsdir_absolute . '/' . $file;
				}
			}
			closedir($handle);
		}
		
		$alphabet_length = strlen($this->alphabet);
		
		while(true)
		{
			$this->keystring 	= $this->code;
			$font_file 			= $this->fonts[mt_rand(0, count($this->fonts)-1)];
			$font				= imagecreatefrompng($font_file);
			imagealphablending($font, true);
			$fontfile_width		= imagesx($font);
			$fontfile_height 	= imagesy($font)-1;
			$font_metrics		= array();
			$symbol				= 0;
			$reading_symbol		= false;

			for($i=0; $i < $fontfile_width && $symbol<$alphabet_length; $i++)
			{
				$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

				if(!$reading_symbol && !$transparent)
				{
					$font_metrics[$this->alphabet{$symbol}] = array('start' => $i);
					$reading_symbol = true;
					continue;
				}

				if($reading_symbol && $transparent)
				{
					$font_metrics[$this->alphabet{$symbol}]['end'] = $i;
					$reading_symbol = false;
					$symbol++;
					continue;
				}
			}

			$img 	= imagecreatetruecolor($this->width, $this->height);
			imagealphablending($img, true);
			$white	= imagecolorallocate($img, 255, 255, 255);
			$black 	= imagecolorallocate($img, 0, 0, 0);

			imagefilledrectangle($img, 0, 0, $this->width-1, $this->height-1, $white);

			$x = 1;
			for($i=0; $i < $this->length; $i++)
			{
				$m = $font_metrics[$this->keystring{$i}];
				$y = mt_rand(-$this->fluctuation_amplitude, $this->fluctuation_amplitude)+($this->height-$fontfile_height)/2+2;

				if($this->no_spaces)
				{
					$shift = 0;
					
					if ( $i>0 )
					{
						$shift = 1000;
						
						for($sy = 7; $sy < $fontfile_height - 20; $sy += 1)
						{
							for($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1)
							{
								$rgb 		= imagecolorat($font, $sx, $sy);
								$opacity	= $rgb >> 24;
								
								if($opacity < 127)
								{
									$left 	= $sx - $m['start'] + $x;
									$py		= $sy + $y;
								
									if($py>$this->height)
									{
										break;
									}
									
									for($px = min($left, $this->width - 1); $px > $left - 12 && $px >= 0; $px -= 1)
									{
										$color = imagecolorat($img, $px, $py) & 0xff;
										
										if($color + $opacity < 190)
										{
											if($shift > $left - $px)
											{
												$shift = $left - $px;
											}
											break;
										}
									}
									break;
								}
							}
						}
						if($shift == 1000)
						{
							$shift = mt_rand(4, 6);
						}

					}
				}
				else
				{
					$shift = 1;
				}
				imagecopy($img, $font, $x-$shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontfile_height);
				$x+=$m['end']-$m['start']-$shift;
			}
			if($x < $this->width -10)
			{
				break;
			}
		}

		$center 		= $x / 2;
		$img2			= imagecreatetruecolor($this->width, $this->height + ($this->show_credits ? 12 : 0));
		$foreground		= imagecolorallocate($img2, $this->foreground_color[0], $this->foreground_color[1], $this->foreground_color[2]);
		$background		= imagecolorallocate($img2, $this->background_color[0], $this->background_color[1], $this->background_color[2]);
		imagefilledrectangle($img2, 0, $this->height, $this->width-1, $this->height+12, $foreground);
		$this->credits	= empty($this->credits) ? $_SERVER['HTTP_HOST'] : $this->credits;
		imagestring($img2, 2, $this->width / 2 - ImageFontWidth(2) * strlen($this->credits) / 2, $this->height - 2, $this->credits, $background);

		$rand1			= mt_rand(750000,1200000) / 10000000;
		$rand2			= mt_rand(750000,1200000) / 10000000;
		$rand3			= mt_rand(750000,1200000) / 10000000;
		$rand4			= mt_rand(750000,1200000) / 10000000;
		$rand5			= mt_rand(0, 3141592) / 500000;
		$rand6			= mt_rand(0, 3141592) / 500000;
		$rand7			= mt_rand(0, 3141592) / 500000;
		$rand8			= mt_rand(0, 3141592) / 500000;
		$rand9			= mt_rand(330, 420) / 110;
		$rand10			= mt_rand(330, 450) / 110;

		for($x = 0; $x < $this->width; $x++)
		{
			for($y = 0; $y < $this->height; $y++)
			{
				$sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $this->width / 2 + $center + 1;
				$sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

				if($sx < 0 || $sy < 0 || $sx >= $this->width - 1 || $sy >= $this->height - 1)
				{
					$color		= 255;
					$color_x	= 255;
					$color_y	= 255;
					$color_xy	= 255;
				}
				else
				{
					$color		= imagecolorat($img, $sx, $sy) & 0xFF;
					$color_x	= imagecolorat($img, $sx + 1, $sy) & 0xFF;
					$color_y	= imagecolorat($img, $sx, $sy + 1) & 0xFF;
					$color_xy 	= imagecolorat($img, $sx + 1, $sy + 1) & 0xFF;
				}

				if($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0)
				{
					$newred		=$this->foreground_color[0];
					$newgreen	=$this->foreground_color[1];
					$newblue	=$this->foreground_color[2];
				}
				else if($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255)
				{
					$newred		= $this->background_color[0];
					$newgreen	= $this->background_color[1];
					$newblue	= $this->background_color[2];	
				}
				else
				{
					$frsx		= $sx - floor($sx);
					$frsy		= $sy - floor($sy);
					$frsx1		= 1 - $frsx;
					$frsy1		= 1 - $frsy;

					$newcolor 	= ($color * $frsx1 * $frsy1 + $color_x * $frsx * $frsy1 + $color_y * $frsx1 * $frsy + $color_xy * $frsx * $frsy);

					if($newcolor > 255)
					{
						$newcolor = 255;
					}
					
					$newcolor 	= $newcolor / 255;
					$newcolor0	= 1 - $newcolor;

					$newred		= $newcolor0 * $this->foreground_color[0] + $newcolor * $this->background_color[0];
					$newgreen	= $newcolor0 * $this->foreground_color[1] + $newcolor * $this->background_color[1];
					$newblue	= $newcolor0 * $this->foreground_color[2] + $newcolor * $this->background_color[2];
				}

				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
			}
		}

		if(function_exists('imagejpeg'))
		{
			header('Content-Type: image/jpeg');
			imagejpeg($img2, null, $this->jpeg_quality);
		}
		else if(function_exists('imagegif'))
		{
			header('Content-Type: image/gif');
			imagegif($img2);
		}
		else if(function_exists('imagepng'))
		{
			header('Content-Type: image/x-png');
			imagepng($img2);
		}
	}
}
?>