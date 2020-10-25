<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 验证码扩展类
 *
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 */

// ------------------------------------------------------------------------

/**
 * Create CAPTCHA
 *
 * @access	public
 * @param	array	array of data for the CAPTCHA
 * @param	string	path to create the image in
 * @param	string	URL to the CAPTCHA image folder
 * @param	string	server path to font
 * @return	string
 */
if (!function_exists('create_captcha')) {
    function create_captcha($data = '',$word='', $img_path = '', $img_url = '', $font_path = '',$img_width='120',$img_height='30',$img_class='',$expiration=120) {
        $defaults = array('word' => $word, 'img_path' => '', 'img_url' => '', 'img_class' => '', 'img_width' => $img_width, 'img_height' => $img_height, 'font_path' => '', 'expiration' => $expiration);

		foreach ($defaults as $key => $val) {
			if (!is_array($data)) {
				if (!isset($$key) OR $$key == '') {
					$$key = $val;
				}
			} else {
				$$key = (!isset($data[$key])) ? $val : $data[$key];
			}
		}

		if ($img_path == '' OR $img_url == '') {
			return FALSE;
		}

		if (!@is_dir($img_path)) {
			if (!file_exists($img_path)) {
				mkdir($img_path);
			}
			//return FALSE;
		}

		if (!is_writable($img_path)) {
			if (!file_exists($img_path)) {
				mkdir($img_path);
			}
			//return FALSE;
		}

		if (!extension_loaded('gd')) {
			return FALSE;
		}

		// -----------------------------------
		// Remove old images
		// -----------------------------------

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		$current_dir = @opendir($img_path);

		while ($filename = @readdir($current_dir)) {
			if ($filename != "." and $filename != ".." and $filename != "index.html") {
				$name = str_replace(".jpg", "", $filename);

				if (($name + $expiration) < $now) {
					@unlink($img_path . $filename);
				}
			}
		}

		@closedir($current_dir);

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------

		if ($word == '') {
			$pool = '3456789abcdefghijkmnpqrstwxyABCDEFGHIJKMNPQRSTUVWXY';

			$str = '';
			for ($i = 0; $i < 4; $i++) {
				$str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
			}

			$word = $str;
		}

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------

		$length = strlen($word);
		$angle = ($length >= 6) ? rand(-($length - 6), ($length - 6)) : 0;
		$x_axis = rand(6, (360 / $length) - 16);
		$y_axis = ($angle >= 0) ? rand($img_height, $img_width) : rand(6, $img_height);

		// -----------------------------------
		// Create image
		// -----------------------------------

		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		if (function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($img_width, $img_height);
		} else {
			$im = imagecreate($img_width, $img_height);
		}

		// -----------------------------------
		//  Assign colors
		// -----------------------------------

		$bg_color = imagecolorallocate($im, 230, 230, 230);
		$border_color = imagecolorallocate($im, 255, 255, 255);
		$text_color = imagecolorallocate($im, 169, 169, 169);
		$grid_color = imagecolorallocate($im, 255, 255, 255);
		$shadow_color = imagecolorallocate($im, 255, 255, 255);

		// -----------------------------------
		//  Create the rectangle
		// -----------------------------------

		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------

		$theta = 1;
		$thetac = 7;
		$radius = 16;
		$circles = 20;
		$points = 32;

		for ($i = 0; $i < ($circles * $points) - 1; $i++) {
			$theta = $theta + $thetac;
			$rad = $radius * ($i / $points);
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta)) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------

		$use_font = ($font_path != '' AND file_exists($font_path) AND function_exists('imagettftext')) ? TRUE : FALSE;

		if ($use_font == FALSE) {
			$font_size = 12;
			$x = rand(0, 18);
			$y = 0;
		} else {
			$font_size = 10;
			$x = rand(0, 18);
			$y = $font_size + 2;
		}

		for ($i = 0; $i < strlen($word); $i++) {
			if ($use_font == FALSE) {
				$y = rand(0, $img_height / 2);
				imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
				$x += ($font_size * 2);
			} else {
				$y = rand($img_height / 2, $img_height - 3);
				imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
				$x += $font_size;
			}
		}

		// -----------------------------------
		//  Create the border
		// -----------------------------------

		imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $border_color);

		// -----------------------------------
		//  Generate the image
		// -----------------------------------

		$img_name = $now . '.jpg';

		ImageJPEG($im, $img_path . $img_name);

		$img = "<img src=\"$img_url$img_name\" width=\"$img_width\" height=\"$img_height\" id=\"codeg\" class=\"$img_class\" onclick=\"change_valicode();\"  alt=\"点击刷新验证码 \" />";

		ImageDestroy($im);

		return array('word' => $word, 'time' => $now, 'image' => $img);
	}

}

// ------------------------------------------------------------------------

/* End of file my_valicode_helper.php */
/* Location: ../app/admin/heleprs/my_valicode_helper.php */
