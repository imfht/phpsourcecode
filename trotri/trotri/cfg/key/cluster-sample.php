<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

return array (
  'auth_administrator' => array (
    'crypt' => '加密密钥',
    'sign' => '签名密钥',
    'expiry' => '加密串有效期，单位：秒',
    'rnd_len' => '随机密钥长度，取值 0-32，数字越大，加密串越长、随机性越大。'
  ),
  'auth_site' => array (
    'crypt' => '加密密钥',
    'sign' => '签名密钥',
    'expiry' => '加密串有效期，单位：秒',
    'rnd_len' => '随机密钥长度，取值 0-32，数字越大，加密串越长、随机性越大。'
  ),
  'cookie' => array (
    'crypt' => '加密密钥',
    'sign' => '签名密钥',
    'expiry' => '加密串有效期，单位：秒',
    'rnd_len' => '随机密钥长度，取值 0-32，数字越大，加密串越长、随机性越大。'
  ),
  'repwd' => array (
    'crypt' => '加密密钥',
    'sign' => '签名密钥',
    'expiry' => '加密串有效期，单位：秒',
    'rnd_len' => '随机密钥长度，取值 0-32，数字越大，加密串越长、随机性越大。'
  )
);

/**
 * 示例：
 * return array (
 *   'auth_administrator' => array (
 *     'crypt' => 'iTrJ8bvSNwpk5Sr9fY3D5c5GhvWFPraW',
 *     'sign' => '9TbFCc83f32jPaBjIm7Qz7geY9EsSRar',
 *     'expiry' => MONTH_IN_SECONDS,
 *     'rnd_len' => 16
 *   ),
 *   'auth_site' => array (
 *     'crypt' => 'B80I15CEA9e2B2Da8A28Ca611FbpF42C',
 *     'sign' => 'L713Cf59C4dBa39F141BeDE28A70R6e0',
 *     'expiry' => 2592000,
 *     'rnd_len' => 16,
 *   ),
 *   'cookie' => array (
 *     'crypt' => '5rfXDIaFhC9LqBhz',
 *     'sign' => 'E7cX4zV7pcffHfZF',
 *     'expiry' => DAY_IN_SECONDS,
 *     'rnd_len' => 8
 *   ),
 *   'repwd' => array (
 *     'crypt' => '95YkePQf7f07OeK2',
 *     'sign' => 'tI2v2IvL2D4L3Yx1',
 *     'expiry' => DAY_IN_SECONDS,
 *     'rnd_len' => 8
 *   )
 * );
 */
