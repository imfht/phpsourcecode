<?php
/*
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/9/3
 * Time: 16:53
 */

namespace App\Cgi\Model;

use Illuminate\Support\Collection;
use Yan\Core\Model;

class User extends Model
{
    protected $table = 'user';

    protected $primaryKey = 'uid';

    protected $keyType = 'int';

    public function getById($id): Collection
    {
        return $this->where([$this->primaryKey => $id])->get();
    }

    public function getByCond($cond): Collection
    {
        return $this->where($cond)->get();
    }

    public function updateByCond($cond, $update): bool
    {
        return $this->where($cond)->update($update);
    }

    public function deleteById($id)
    {
        return $this->where($id)->delete();
    }

    public function switchConnection(string $connectionName){
        $this->setConnection($connectionName);
    }
}