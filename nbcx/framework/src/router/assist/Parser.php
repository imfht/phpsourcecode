<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\router\assist;

/**
 * Parser
 *
 * @package nb\router\assist
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Parser {

    /**
     * 默认匹配表
     *
     * @access private
     * @var array
     */
    private $defaultRegx = [
        'string' => '(.%s)',
        'char' => '([^/]%s)',
        'digital' => '([0-9]%s)',
        'alpha' => '([=_0-9a-zA-Z-]%s)',
        'alphaslash' => '([!=_0-9a-zA-Z-/]%s)',
        'split' => '((?:[^/]+/)%s[^/]+)',
    ];

    /**
     * 路由器映射表
     *
     * @access private
     * @var array
     */
    private $routingTable;

    /**
     * 参数表
     *
     * @access private
     * @var array
     */
    private $params;

    /**
     * 设置路由表
     *
     * @access public
     * @param array $routingTable 路由器映射表
     */
    public function __construct(array $routingTable) {
        $this->routingTable = $routingTable;
        /*
        $this->_defaultRegx = [
            'string' => '(.%s)',
            'char' => '([^/]%s)',
            'digital' => '([0-9]%s)',
            'alpha' => '([_0-9a-zA-Z-]%s)',
            'alphaslash' => '([_0-9a-zA-Z-/]%s)',
            'split' => '((?:[^/]+/)%s[^/]+)',
        ];
        */
    }

    /**
     * 局部匹配并替换正则字符串
     *
     * @access public
     * @param array $matches 匹配部分
     * @return string
     */
    public function match(array $matches) {
        $params = explode(' ', $matches[1]);
        $paramsNum = count($params);
        $this->params[] = $params[0];

        if (1 == $paramsNum) {
            return sprintf($this->defaultRegx['char'], '+');
        }
        else if (2 == $paramsNum) {
            return sprintf($this->defaultRegx[$params[1]], '+');
        }
        else if (3 == $paramsNum) {
            return sprintf($this->defaultRegx[$params[1]], $params[2] > 0 ? '{' . $params[2] . '}' : '*');
        }
        else if (4 == $paramsNum) {
            return sprintf($this->defaultRegx[$params[1]], '{' . $params[2] . ',' . $params[3] . '}');
        }
    }

    /**
     * 解析路由表
     *
     * @access public
     * @return array
     */
    public function parse() {
        $result = [];
        foreach ($this->routingTable as $key => $route) {
            $this->params = [];
            $route['regx'] = preg_replace_callback(
                "/%([^%]+)%/",
                [$this, 'match'],
                preg_quote(str_replace(['[', ']', ':'], ['%', '%', ' '], $route['url']))//$route['url']
            );

            /** 处理斜线 */
            $route['regx'] = rtrim($route['regx'], '/');
            $route['regx'] = '|^' . $route['regx'] . '[/]?$|';

            $route['format'] = preg_replace("/\[([^\]]+)\]/", "%s", $route['url']);//$route['url']
            $route['params'] = $this->params;

            $result[$key] = $route;
        }

        return $result;
    }
}