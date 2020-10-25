<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

use nb\console\input\Input;
use nb\console\input\Definition;
use nb\console\output\Formatter;
use nb\console\output\Output;

/**
 * 控制台入口类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/7/25
 *
 * @property  Definition $definition
 * @property  boolean $catchExceptions
 * @property  string $help
 * @property  boolean $autoExit 是否自动退出
 * @property  string $name
 * @property  string $version 版本号
 */
class Console extends Component {

    public static function config() {
        return  Config::$o->console;
    }

    /**
     * 初始化 Console
     * @access public
     * @param  bool $run 是否运行 Console
     * @return int|Console
     */
    public static function run($command = null) {
        $parameters = null;
        if ($command) {
            $parameters = $_SERVER['argv'];

            // 去除命令名
            array_shift($parameters);

            //添加指定命令到参数里
            array_unshift($parameters, $command);
        }
        $input = self::input($parameters);//new Input($parameters);
        $output = self::output();//new Output();
        $exitCode = self::driver()->execute($input,$output);
        exit($exitCode);
    }

    /**
     * @param null $argv
     * @return Input
     * @throws \ReflectionException
     */
    public static function input($argv = null) {
        return Pool::object(
            'nb\console\input\Input',
            [$argv]
        );
    }

    /**
     * @param string $driver
     * @return Output
     * @throws \ReflectionException
     */
    public static function output($driver = 'console') {
        return Pool::object(
            'nb\console\output\Output',
            [$driver]
        );
    }

    /**
     * @access public
     * @param  string $command
     * @param  array $parameters
     * @param  string $driver
     * @return Output|Buffer
     */
    public static function call($command, array $parameters = [], $driver = 'buffer') {
        $console = self::driver();

        array_unshift($parameters, $command);

        $input = new Input($parameters);
        $output = new Output($driver);

        $console->catchExceptions=false;
        $console->find($command)->run($input, $output);

        return $output;
    }

    public static function strlenWithoutDecoration(Formatter $formatter, $string) {
        return self::strlen(self::removeDecoration($formatter, $string));
    }

    public static function removeDecoration(Formatter $formatter, $string) {
        $isDecorated = $formatter->isDecorated();
        $formatter->setDecorated(false);

        // remove <...> formatting
        $string = $formatter->format((string)$string);

        // remove already formatted characters
        $string = preg_replace("/\033\[[^m]*m/", '', $string);
        $formatter->setDecorated($isDecorated);

        return $string;
    }

    /**
     * Returns the subset of a string, using mb_substr if it is available.
     *
     * @param string $string String to subset
     * @param int $from Start offset
     * @param int|null $length Length to read
     *
     * @return string The string subset
     */
    public static function substr($string, $from, $length = null) {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return substr($string, $from, $length);
        }

        return mb_substr($string, $from, $length, $encoding);
    }

    public static function formatTime($secs) {
        static $timeFormats = [
            [0, '< 1 sec'],
            [1, '1 sec'],
            [2, 'secs', 1],
            [60, '1 min'],
            [120, 'mins', 60],
            [3600, '1 hr'],
            [7200, 'hrs', 3600],
            [86400, '1 day'],
            [172800, 'days', 86400],
        ];

        foreach ($timeFormats as $index => $format) {
            if ($secs >= $format[0]) {
                if ((isset($timeFormats[$index + 1]) && $secs < $timeFormats[$index + 1][0])
                    || $index == count($timeFormats) - 1
                ) {
                    if (2 == count($format)) {
                        return $format[1];
                    }

                    return floor($secs / $format[2]) . ' ' . $format[1];
                }
            }
        }
    }
    public static function formatMemory($memory) {
        if ($memory >= 1024 * 1024 * 1024) {
            return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
        }

        if ($memory >= 1024 * 1024) {
            return sprintf('%.1f MiB', $memory / 1024 / 1024);
        }

        if ($memory >= 1024) {
            return sprintf('%d KiB', $memory / 1024);
        }

        return sprintf('%d B', $memory);
    }

}
