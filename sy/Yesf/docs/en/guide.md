# Yesf

![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg?maxAge=2592000)
![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.0-brightgreen.svg?maxAge=2592000)
![Packagist](https://img.shields.io/packagist/v/sylingd/yesf-framework.svg)
![license](https://img.shields.io/github/license/sylingd/Yesf.svg)

Yesf is a framework based on Swoole, for the website.
Advantages:

1. High performance
2. Flexible autoload
3. Flexible and scalable
4. Built-in multiple routes, compatible with the current common routing protocol
5. Support a variety of configuration

Yesf is based on Swoole, so it supports TCP and UDP listening, asynchronous tasks and other functions

# About documentiion

This document corresponds to Yesf version `1.0.0-rc5`. If there is any error, please submit an issue to [GitHub](https://github.com/sylingd/Yesf/issues/new) or [Gitee](https://gitee.com/sy/Yesf/issues/new).

# Naming convention

The current Yesf naming convention is as follows:

### Class naming
1. All libraries are in the `yesf\library` namespace
2. All libraries are in the `library` directory
3. Class are named after the big hump naming
4. Methods are named after the small hump naming

### Variable naming
1. Most variables are named after the small humping nomenclature
2. When some variables start with "_" (underline), they follow the nomenclature of the underline

### Constant naming
1. The basic constants of the frame begin with `YESF_`
2. Other constants are located in `yesf\Constant`, and basically follow the "module\_type\_description" of the nomenclature, such as "ROUTER\_ERR\_CONTROLLER"

### Other special naming
1. The abstract classes for inheritance are namespace `yesf\library\abstract`
2. The interfaces for the specification are namespace `yesf\library\interface`

