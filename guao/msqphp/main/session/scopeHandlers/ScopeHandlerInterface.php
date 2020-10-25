<?php declare (strict_types = 1);
namespace msqphp\main\session\scopeHandlers;

interface ScopeHandlerInterface
{
    public function __construct(string $session_ID, string $scope, array $config);
    public function exists(string $key): bool;
    public function get(string $key);
    public function set(string $key, $value): void;
    public function delete(string $key): void;
}
