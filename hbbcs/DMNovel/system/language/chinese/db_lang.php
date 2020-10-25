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

$lang['db_invalid_connection_str'] = '无法根据所提交的连接字符串来确定数据库设置。';
$lang['db_unable_to_connect'] = '使用所提供的设置无法连接到您的数据库服务器。';
$lang['db_unable_to_select'] = '无法选择指定的数据库: %s';
$lang['db_unable_to_create'] = '无法创建指定的数据库: %s';
$lang['db_invalid_query'] = '提交的查询无效。';
$lang['db_must_set_table'] = '您必须设置数据库表便于查询使用。';
$lang['db_must_use_set'] = '您必须使用“set”方法来更新输入。';
$lang['db_must_use_index'] = '你必须指定一个匹配的索引来进行批处理更新。';
$lang['db_batch_missing_index'] = '提交批处理更新的一个或多个行丢失了指定的索引。';
$lang['db_must_use_where'] = '除非它们包含“where”，更新是不允许的.';
$lang['db_del_must_use_where'] = '除非他们包含一个“where”或“like”，否则不允许删除。';
$lang['db_field_param_missing'] = '取字段需要将表的名称作为参数。';
$lang['db_unsupported_function'] = '在您正使用的数据库中此功能是不可用。';
$lang['db_transaction_failure'] = '事务故障：执行回滚。';
$lang['db_unable_to_drop'] = '无法删除指定的数据库。';
$lang['db_unsupported_feature'] = '您正在使用的数据库平台不支持此功能。';
$lang['db_unsupported_compression'] = '您的服务器不支持您选择的文件压缩格式。';
$lang['db_filepath_error'] = '无法将数据写入提交的文件路径。';
$lang['db_invalid_cache_path'] = '你提交的缓存路径无效或不可写。';
$lang['db_table_name_required'] = '该操作需要提供表名。';
$lang['db_column_name_required'] = '该操作需要提供列名称。';
$lang['db_column_definition_required'] = '该操作需要提供列定义。';
$lang['db_unable_to_set_charset'] = '无法设置客户端连接字符集: %s';
$lang['db_error_heading'] = '发生数据库错误';
