<?php
/**
 * authentication.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 14:54
 * @modified 2020-06-2020/6/29 14:54
 */

namespace Models\Customer;


use Models\Base;
use Models\Customer;

class Authentication extends Base
{
    protected $table = 'customer_authentication';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }
}