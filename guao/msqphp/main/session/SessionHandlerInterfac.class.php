<?php declare(strict_types = 1);
/**
 * SessionHandlerInterface 例, 实际上直接实现函数\SessionHandlerInterface
 * 但是由于个坑, php 7.0 函数标量无法注册, 请按无处理
 */
Interface SessionHandlerInterface
{

    public function __construct(array $config) : void;

    public function close () : bool;

    public function destroy ( string $session_id ) : bool;

    public function gc ( int $maxlifetime ) : bool;

    public function open ( string $save_path , string $name ) : bool;

    public function read ( string $session_id ) : string;

    public function write ( string $session_id , string $session_data ) : bool;
}