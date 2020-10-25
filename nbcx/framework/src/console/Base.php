<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\console;

use nb\Console;
use nb\console\input\Input;
use nb\console\input\Argument;
use nb\console\input\Definition;
use nb\console\input\Option;
use nb\console\output\Output;

/**
 * Class Console
 * @package nb
 *
 * @property  Definition $definition
 * @property  boolean $catchExceptions
 * @property  string $help
 * @property  boolean $autoExit 是否自动退出
 * @property  string $name
 * @property  string $version 版本号
 */
class Base extends Driver {

    //private $name;
    //private $version;

    /** @var Driver[] */
    private $commands = [];

    //private $wantHelps = false;

    //private $catchExceptions = true;
    //private $autoExit = true;
    //private $definition;
    //private $defaultCommand;

    public $config = [
        'name'    => 'NB Console',
        'version' => '0.1',
        'user'    => null,
    ];

    private $defaultCommands = [
        'nb\Server',
        'nb\dispatcher\Command'
    ];

    /**
     * Console constructor.
     * @access public
     * @param  string $name 名称
     * @param  string $version 版本
     * @param null|string $user 执行用户
     */
    public function __construct($config=[]) {
        //$name = 'UNKNOWN', $version = 'UNKNOWN', $user = null
        $config = array_merge($this->config,$config);
        if(isset($config['commands']) && is_array($config['commands'])) {
            $this->defaultCommands = array_merge(
                $this->defaultCommands,
                $config['commands']
            );
            unset($config['commands']);
        }

        $config['user'] and $this->user = $config['user'];

        $this->config = $config;

        //$this->name = $name;
        //$this->version = $version;


        //$this->defaultCommand = 'list';
        $this->definition = $this->getDefaultInputDefinition();

        foreach ($this->getDefaultCommands() as $command) {
            $this->add($command);
        }
    }

    protected function _name() {
        return $this->config['name'];
    }

    protected function _version() {
        return $this->config['version'];
    }

    /**
     * 设置执行用户
     * @param $user
     */
    public function ___user($user) {
        $user = posix_getpwnam($user);
        if ($user) {
            posix_setuid($user['uid']);
            posix_setgid($user['gid']);
        }
    }

    /**
     * 初始化 Console
     * @access public
     * @param  bool $run 是否运行 Console
     * @return int|Console
     */
    public static function run($run = true) {
        $console = Pool::value(get_called_class(),function () {
            $config = Config::$o->console;
            // 实例化 console
            $console = new self($config['name'], $config['version'], $config['user']);
            if(isset($config['commands']) && is_array($config['commands'])) {
                $console->defaultCommands = array_merge(
                    $console->defaultCommands,
                    $config['commands']
                );
            }
            return $console;
        });
        if ($run) {
            // 运行
            return $console->execute();
        }
        return $console;
    }

    /**
     * 执行当前的指令
     * @access public
     * @return int
     * @throws \Exception
     * @api
     */
    public function execute(Input $input, Output $output) {
        //$input = Console::input();//new Input();
        //$output = Console::output();//new Output();

        $this->configureIO($input, $output);

        $exitCode = $this->doRun($input, $output);

        return $exitCode;

        try {
            $exitCode = $this->doRun($input, $output);
        }
        catch (\Exception $e) {
            if (!$this->catchExceptions) {
                throw $e;
            }

            $output->renderException($e);

            $exitCode = $e->getCode();
            if (is_numeric($exitCode)) {
                $exitCode = (int)$exitCode;
                if (0 === $exitCode) {
                    $exitCode = 1;
                }
            }
            else {
                $exitCode = 1;
            }
        }

        if ($this->autoExit) {
            if ($exitCode > 255) {
                $exitCode = 255;
            }

            exit($exitCode);
        }

        return $exitCode;
    }

    /**
     * 执行指令
     * @access public
     * @param  Input $input
     * @param  Output $output
     * @return int
     */
    public function doRun(Input $input, Output $output) {

        //获取执行命令的名字
        $name = $this->getCommandName($input);

        if ($name) {

            $command = $this->find($name);

            //显示帮助
            if(true === $input->hasParameterOption(['--help', '-h'])) {
                $output->describe($command);
                //, [
                    //'raw_text' => $input->getOption('raw'),
                //]
                return 0;
            }

            $exitCode = $command->run($input, $output);// $this->doRunCommand($command, $input, $output);

            return $exitCode;
        }

        //检测版本
        if (true === $input->hasParameterOption(['--version', '-v'])) {
            $output->writeln($this->longVersion);
            return 0;
        }

        $input->bind(new Definition([
            new Argument('namespace', Argument::OPTIONAL, 'The namespace name'),
            new Option('raw', null, Option::VALUE_NONE, 'To output raw command list')
        ]));
        $output->describe($this, [
            'raw_text' => $input->getOption('raw'),
            'namespace' => $input->getArgument('namespace'),
        ]);
        return 0;
    }

    /**
     * 查找指令
     * @access public
     * @param  string $name 名称或者别名
     * @return Driver
     * @throws \InvalidArgumentException
     */
    public function find($name) {
        $allCommands = array_keys($this->commands);

        $expr = preg_replace_callback('{([^:]+|)}', function ($matches) {
            return preg_quote($matches[1]) . '[^:]*';
        }, $name);

        $commands = preg_grep('{^' . $expr . '}', $allCommands);

        if (empty($commands) || count(preg_grep('{^' . $expr . '$}', $commands)) < 1) {
            if (false !== $pos = strrpos($name, ':')) {
                $this->findNamespace(substr($name, 0, $pos));
            }

            $message = sprintf('Command "%s" is not defined.', $name);

            if ($alternatives = $this->findAlternatives($name, $allCommands)) {
                if (1 == count($alternatives)) {
                    $message .= "\n\nDid you mean this?\n    ";
                }
                else {
                    $message .= "\n\nDid you mean one of these?\n    ";
                }
                $message .= implode("\n    ", $alternatives);
            }

            throw new \InvalidArgumentException($message);
        }

        if (count($commands) > 1) {
            $commandList = $this->commands;

            $commands = array_filter($commands, function ($nameOrAlias) use ($commandList, $commands) {
                $commandName = $commandList[$nameOrAlias]->getName();

                return $commandName === $nameOrAlias || !in_array($commandName, $commands);
            });
        }

        $exact = in_array($name, $commands, true);
        if (count($commands) > 1 && !$exact) {
            $suggestions = $this->getAbbreviationSuggestions(array_values($commands));

            throw new \InvalidArgumentException(sprintf('Command "%s" is ambiguous (%s).', $name, $suggestions));
        }

        return $this->get($exact ? $name : reset($commands));
    }

    /**
     * 获取指令
     * @access public
     * @param  string $name 指令名称
     * @return Driver
     * @throws \InvalidArgumentException
     */
    public function get($name) {
        if (!isset($this->commands[$name])) {
            throw new \InvalidArgumentException(sprintf('The command "%s" does not exist.', $name));
        }

        return $this->commands[$name];

        //if ($this->wantHelps) {
        //    $this->wantHelps = false;

            /** @var HelpCommand $helpCommand */
        //    $helpCommand = $this->get('help');
        //    $helpCommand->setCommand($command);

        //    return $helpCommand;
        //}

        //return $command;
    }


    /**
     * 设置输入参数定义
     * @access public
     * @param  Definition $definition
     */
    public function ___definition(Definition $definition) {
        return $definition;
    }


    /**
     * Gets the help message.
     * @access public
     * @return string A help message.
     */
    protected function _help() {
        return $this->longVersion;
    }

    /**
     * 设置是否捕获异常
     * @access public
     * @param  bool $boolean
     * @api
     */
    public function ___catchExceptions($boolean) {
        return (bool)$boolean;
    }

    /**
     * 默认捕获异常
     * @return bool
     */
    public function _catchExceptions() {
        return true;
    }


    /**
     * 是否自动退出
     * @access public
     * @param  bool $boolean
     * @api
     */
    public function ___autoExit($boolean) {
        return (bool)$boolean;
    }

    /**
     * 获取完整的版本号
     * @access public
     * @return string
     */
    public function _longVersion() {
        $logo = file_get_contents(__DIR__.DS.'html'.DS.'logo.tpl');
        if ($this->name && $this->version) {
            return $logo.sprintf('<info>%s</info> version <comment>%s</comment>', $this->name, $this->version);
        }
        return $logo.'<info>Console Tool</info>';
    }

    /**
     * 注册一个指令
     * @access public
     * @param  string $name
     * @return Driver
     */
    public function register(Command $cmd) {
        return $this->add(new Pack($cmd));//
    }

    /**
     * 添加指令
     * @access public
     * @param  Driver[] $commands
     */
    public function addCommands(array $commands) {
        foreach ($commands as $command) {
            $this->add($command);
        }
    }

    /**
     * 添加一个指令
     * @access public
     * @param  Command $command
     * @return Command
     */
    public function add(Pack $command) {
        $command->setConsole($this);

        if (!$command->isEnabled()) {
            $command->setConsole(null);
            return;
        }

        if (null === $command->getDefinition()) {
            throw new \LogicException(sprintf('Command class "%s" is not correctly initialized. You probably forgot to call the parent constructor.', get_class($command)));
        }

        $this->commands[$command->getName()] = $command;

        foreach ($command->getAliases() as $alias) {
            $this->commands[$alias] = $command;
        }

        return $command;
    }

    /**
     * 某个指令是否存在
     * @access public
     * @param  string $name 指令名称
     * @return bool
     */
    public function has($name) {
        return isset($this->commands[$name]);
    }

    /**
     * 获取所有的命名空间
     * @access public
     * @return array
     */
    public function getNamespaces() {
        $namespaces = [];
        foreach ($this->commands as $command) {
            $namespaces = array_merge($namespaces, $this->extractAllNamespaces($command->getName()));

            foreach ($command->getAliases() as $alias) {
                $namespaces = array_merge($namespaces, $this->extractAllNamespaces($alias));
            }
        }

        return array_values(array_unique(array_filter($namespaces)));
    }

    /**
     * 查找注册命名空间中的名称或缩写。
     * @access public
     * @param  string $namespace
     * @return string
     * @throws \InvalidArgumentException
     */
    public function findNamespace($namespace) {
        $allNamespaces = $this->getNamespaces();
        $expr = preg_replace_callback('{([^:]+|)}', function ($matches) {
            return preg_quote($matches[1]) . '[^:]*';
        }, $namespace);
        $namespaces = preg_grep('{^' . $expr . '}', $allNamespaces);

        if (empty($namespaces)) {
            $message = sprintf('There are no commands defined in the "%s" namespace.', $namespace);

            if ($alternatives = $this->findAlternatives($namespace, $allNamespaces)) {
                if (1 == count($alternatives)) {
                    $message .= "\n\nDid you mean this?\n    ";
                }
                else {
                    $message .= "\n\nDid you mean one of these?\n    ";
                }

                $message .= implode("\n    ", $alternatives);
            }

            throw new \InvalidArgumentException($message);
        }

        $exact = in_array($namespace, $namespaces, true);
        if (count($namespaces) > 1 && !$exact) {
            throw new \InvalidArgumentException(sprintf('The namespace "%s" is ambiguous (%s).', $namespace, $this->getAbbreviationSuggestions(array_values($namespaces))));
        }

        return $exact ? $namespace : reset($namespaces);
    }

    /**
     * 获取所有的指令
     * @access public
     * @param  string $namespace 命名空间
     * @return Command[]
     * @api
     */
    public function all($namespace = null) {
        if (null === $namespace) {
            return $this->commands;
        }

        $commands = [];
        foreach ($this->commands as $name => $command) {
            if ($this->extractNamespace($name, substr_count($namespace, ':') + 1) === $namespace) {
                $commands[$name] = $command;
            }
        }

        return $commands;
    }

    /**
     * 获取可能的指令名
     * @access public
     * @param  array $names
     * @return array
     */
    //public static function getAbbreviations($names) {
    //    $abbrevs = [];
    //    foreach ($names as $name) {
    //        for ($len = strlen($name); $len > 0; --$len) {
    //            $abbrev = substr($name, 0, $len);
    //            $abbrevs[$abbrev][] = $name;
    //        }
    //    }

    //    return $abbrevs;
    //}

    /**
     * 配置基于用户的参数和选项的输入和输出实例。
     * @access protected
     * @param  Input $input 输入实例
     * @param  Output $output 输出实例
     */
    protected function configureIO(Input $input, Output $output) {
        if (true === $input->hasParameterOption(['--ansi'])) {
            $output->setDecorated(true);
        }
        elseif (true === $input->hasParameterOption(['--no-ansi'])) {
            $output->setDecorated(false);
        }

        if (true === $input->hasParameterOption(['--no-interaction', '-n'])) {
            $input->interactive = false;
        }

        if (true === $input->hasParameterOption(['--quiet', '-q'])) {
            $output->setVerbosity(Output::VERBOSITY_QUIET);
        }
        else {
            if ($input->hasParameterOption('-vvv') || $input->hasParameterOption('--verbose=3') || $input->getParameterOption('--verbose') === 3) {
                $output->setVerbosity(Output::VERBOSITY_DEBUG);
            }
            elseif ($input->hasParameterOption('-vv') || $input->hasParameterOption('--verbose=2') || $input->getParameterOption('--verbose') === 2) {
                $output->setVerbosity(Output::VERBOSITY_VERY_VERBOSE);
            }
            elseif ($input->hasParameterOption('-v') || $input->hasParameterOption('--verbose=1') || $input->hasParameterOption('--verbose') || $input->getParameterOption('--verbose')) {
                $output->setVerbosity(Output::VERBOSITY_VERBOSE);
            }
        }
    }

    /**
     * 获取指令的基础名称
     * @access protected
     * @param  Input $input
     * @return string
     */
    protected function getCommandName(Input $input) {
        return $input->getFirstArgument();
    }

    /**
     * 获取默认输入定义
     * @access protected
     * @return Definition
     */
    protected function getDefaultInputDefinition() {
        return new Definition([
            new Argument('command', Argument::REQUIRED, 'The command to execute'),
            new Option('--help', '-h', Option::VALUE_NONE, 'Display this help message'),
            new Option('--version', '-v', Option::VALUE_NONE, 'Display this console version'),
            new Option('--quiet', '-q', Option::VALUE_NONE, 'Do not output any message'),
            //new Option('--verbose', '-v|vv|vvv', Option::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new Option('--ansi', '', Option::VALUE_NONE, 'Force ANSI output'),
            new Option('--no-ansi', '', Option::VALUE_NONE, 'Disable ANSI output'),
            new Option('--no-interaction', '-n', Option::VALUE_NONE, 'Do not ask any interactive question'),
        ]);
    }

    /**
     * 设置默认命令
     * @access protected
     * @return driver[] An array of default Command instances
     */
    protected function getDefaultCommands() {
        $defaultCommands = [];
        /*
        foreach (self::$defaultCommands as $classname) {
            if (class_exists($classname) && is_subclass_of($classname, "nb\\console\\Driver")) {
                $defaultCommands[] = new $classname();
            }
        }
        */
        //更换为接口
        //self::$defaultCommands
        foreach ($this->defaultCommands as $classname) {
            if (class_exists($classname) && is_subclass_of($classname, "nb\\console\\Command")) {

                $defaultCommands[] = new Pack(new $classname());
            }
        }

        return $defaultCommands;
    }

    //public static function addDefaultCommands(array $classnames) {
    //    self::$defaultCommands = array_merge(self::$defaultCommands, $classnames);
    //}

    /**
     * 获取可能的建议
     * @access private
     * @param  array $abbrevs
     * @return string
     */
    private function getAbbreviationSuggestions($abbrevs) {
        return sprintf('%s, %s%s', $abbrevs[0], $abbrevs[1], count($abbrevs) > 2 ? sprintf(' and %d more', count($abbrevs) - 2) : '');
    }

    /**
     * 返回命名空间部分
     * @access public
     * @param  string $name 指令
     * @param  string $limit 部分的命名空间的最大数量
     * @return string
     */
    public function extractNamespace($name, $limit = null) {
        $parts = explode(':', $name);
        array_pop($parts);

        return implode(':', null === $limit ? $parts : array_slice($parts, 0, $limit));
    }

    /**
     * 查找可替代的建议
     * @access private
     * @param  string $name
     * @param  array|\Traversable $collection
     * @return array
     */
    private function findAlternatives($name, $collection) {
        $threshold = 1e3;
        $alternatives = [];

        $collectionParts = [];
        foreach ($collection as $item) {
            $collectionParts[$item] = explode(':', $item);
        }

        foreach (explode(':', $name) as $i => $subname) {
            foreach ($collectionParts as $collectionName => $parts) {
                $exists = isset($alternatives[$collectionName]);
                if (!isset($parts[$i]) && $exists) {
                    $alternatives[$collectionName] += $threshold;
                    continue;
                }
                elseif (!isset($parts[$i])) {
                    continue;
                }

                $lev = levenshtein($subname, $parts[$i]);
                if ($lev <= strlen($subname) / 3 || '' !== $subname && false !== strpos($parts[$i], $subname)) {
                    $alternatives[$collectionName] = $exists ? $alternatives[$collectionName] + $lev : $lev;
                }
                elseif ($exists) {
                    $alternatives[$collectionName] += $threshold;
                }
            }
        }

        foreach ($collection as $item) {
            $lev = levenshtein($name, $item);
            if ($lev <= strlen($name) / 3 || false !== strpos($item, $name)) {
                $alternatives[$item] = isset($alternatives[$item]) ? $alternatives[$item] - $lev : $lev;
            }
        }

        $alternatives = array_filter($alternatives, function ($lev) use ($threshold) {
            return $lev < 2 * $threshold;
        });
        asort($alternatives);

        return array_keys($alternatives);
    }

    /**
     * 设置默认的指令
     * @access public
     * @param  string $commandName The Command name
     */
    //public function setDefaultCommand($commandName) {
    //    $this->defaultCommand = $commandName;
    //}

    /**
     * 返回所有的命名空间
     * @access private
     * @param  string $name
     * @return array
     */
    private function extractAllNamespaces($name) {
        $parts = explode(':', $name, -1);
        $namespaces = [];

        foreach ($parts as $part) {
            if (count($namespaces)) {
                $namespaces[] = end($namespaces) . ':' . $part;
            }
            else {
                $namespaces[] = $part;
            }
        }

        return $namespaces;
    }

}
