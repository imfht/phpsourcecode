<?php declare (strict_types = 1);
namespace msqphp\main\session\scopeHandlers;

use msqphp\base;

final class File implements ScopeHandlerInterface
{
    private $file_path = '';
    private $content   = [];

    public function __construct(string $session_ID, string $scope, array $config)
    {
        $this->file_path = $config['path'] . $scope . DIRECTORY_SEPARATOR . $session_ID . $config['extension'];
        if (is_file($this->file_path)) {
            $this->content = unserialize(base\file\File::get($this->file_path));
        }
    }
    public function exists(string $key): bool
    {
        return isset($this->content[$key]);
    }
    public function get(string $key)
    {
        return $this->content[$key];
    }
    public function set(string $key, $value): void
    {
        $this->content[$key] = $value;
    }
    public function delete(string $key): void
    {
        unset($this->content[$key]);
    }

    public function __destruct()
    {
        base\file\File::write($this->file_path, serialize($this->content));
    }
}
