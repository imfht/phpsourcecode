<?php declare (strict_types = 1);
namespace msqphp\core\loader;

final class AutoLoadRecord
{
    // 所有加载过的文件列表
    private static $loadedClasses = [];

    // 在智能加载范围内文件列表
    private static $aiLoadClasses = [];

    // 当前智能加载范围,默认全局
    private static $scope = 'default';

    // 添加一个自动加载文件记录
    public static function record(string $file_path): void
    {
        static::$loadedClasses[] = static::$aiLoadClasses[static::$scope][] = $file_path;
    }

    // 获取指定智能加载范围内的文件列表
    public static function getClassesInScope(string $scope): array
    {
        return static::$aiLoadClasses[$scope];
    }
    public static function getClassesInCurrnetScope(): array
    {
        return static::$aiLoadClasses[static::$scope];

    }

    // 获取所有加载过的文件
    public static function getAllLoadedClasses(): array
    {
        return static::$loadedClasses;
    }

    public static function setScope(string $scope): void
    {
        static::$scope = $scope;
    }
    public static function getScope(): string
    {
        return static::$scope;
    }
}
