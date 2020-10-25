<?php
/**
 * customer.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 14:52
 * @modified 2020-06-2020/6/29 14:52
 */

namespace Models;


use Models\Customer\Authentication;

class Customer extends Base
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';

    public function authentications()
    {
        return $this->hasMany(Authentication::class);
    }
}