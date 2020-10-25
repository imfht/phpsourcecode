<?php
/*
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/9/27
 * Time: 15:56
 */

namespace Yan\Core;


class Model extends \Illuminate\Database\Eloquent\Model
{
    /** @var string model关联的表名 */
    protected $table;

    /** @var string 主键 */
    protected $primaryKey = 'id';

    /** @var string 主键类型 */
    protected $keyType = 'int';

    protected $connection = 'default';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        Log::debug('Init Model ' . static::class);
    }
}