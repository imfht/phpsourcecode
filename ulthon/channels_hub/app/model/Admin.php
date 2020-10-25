<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Admin extends Model
{
    //

    public function verifyPassword($password)
    {
        return (md5($password.$this->getData('salt')) === $this->getData('password'));
    }
}
