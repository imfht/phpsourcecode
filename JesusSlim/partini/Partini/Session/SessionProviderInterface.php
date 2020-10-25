<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/4
 * Time: 下午10:20
 */

namespace Partini\Session;


interface SessionProviderInterface
{

    public function open($path,$name);
    public function close();
    public function read($id);
    public function write($id,$data);
    public function destroy($id);
    public function gc($maxLifeTime);

}