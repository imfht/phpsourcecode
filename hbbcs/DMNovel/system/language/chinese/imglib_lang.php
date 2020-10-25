<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['imglib_source_image_required'] = '您必须在首选项中指定源图像。';
$lang['imglib_gd_required'] = '此功能需要GD图像库。';
$lang['imglib_gd_required_for_props'] = '您的服务器必须支持的GD图像库，以确定图像属性。';
$lang['imglib_unsupported_imagecreate'] = '您的服务器不支持处理这种类型的图像所需的GD函数。';
$lang['imglib_gif_not_supported'] = '由于许可限制，GIF图像往往不被支持。你可以使用JPG或PNG图像代替。';
$lang['imglib_jpg_not_supported'] = '不支持JPG图像。';
$lang['imglib_png_not_supported'] = '不支持PNG图像。';
$lang['imglib_jpg_or_png_required'] = '在你设定的图像调整协议只适用于JPEG或PNG图像类型。';
$lang['imglib_copy_error'] = '试图替换文件时遇到错误。请确保你的文件目录是可写的。';
$lang['imglib_rotate_unsupported'] = '您的服务器不支持图像旋转。';
$lang['imglib_libpath_invalid'] = '你的图像库的路径是不正确的。请在图像首选项中设置正确的路径。';
$lang['imglib_image_process_failed'] = '图像处理失败。请验证您的服务器是否支持所选的协议，并且您的图像库的路径是否正确。';
$lang['imglib_rotation_angle_required'] = '旋转图像需要旋转的角度。';
$lang['imglib_invalid_path'] = '图像的路径是不正确的。';
$lang['imglib_copy_failed'] = '图像复制失败。';
$lang['imglib_missing_font'] = '找不到字体来使用。';
$lang['imglib_save_failed'] = '无法保存图像。请确保图像和文件目录是可写的。';
