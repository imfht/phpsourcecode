---
layout: post
title: php性能优化的几个方法
description:   PHP是一种在服务器端执行的脚本语言，它开发了世界上许多知名的网站，包括雅虎和Facebook等。下面介绍的几条PHP代码、性能优化的技巧供读者参阅。
keywords: php性能优化
author: 网络技术test
category: [网络技术]
tags: [php, 编程]
---

网上有无数关于 @php@ 性能的优化技巧，有必要列出一张可供参考使用的清单。@hi@ 收集的这些技巧来源较广，完整性不能保证。 由于数量较多，这些优化技巧没有经过测试。请各位看官在使用之前自行测试，毕竟这些技巧是否能派上用场，还是需要由PHP所在的独特环境所决定的。

## 找到瓶颈（Finding the Bottleneck）

面对一个性能问题是，第一步永远是找到问题产生的原因，而不是去看技巧列表。搞明白产生瓶颈的原因，找到目标并且实施修复，然后再重新测试。查找瓶颈只是万里长征的第一步，这里有些常用技巧，希望对最重要的第一步找到瓶颈能有所帮助。

*   使用监控方法（比如监控宝），进行benchmark和监控，网络，特别是网络状况瞬息万变，做得好的话5分钟就可以找到瓶颈。
*   剖析代码。必须了解那部分代码耗时最多，在这些地方多多关注。
*   想找到瓶颈，请检查每个资源请求（比如，网络、CPU、内存、共享内存、文件系统、进程管理、网络连接等等……）
*   先对迭代结构和复杂的代码进行benchmark
*   在在真实负载下用真实数据进行真实测试，当然，如果可以最好用产品服务器。

## 缓存 （Caching）

有些人认为缓存是解决性能问题最有效的办法之一，试试这些：

*   使用OPCODE（操作码）缓存，这样脚本就不会在每次访问时重新编译一次。比如：启用Windows平台上的windows缓存扩展。可以缓存opcode，文件，相对路径，session数据和用户数据。
*   考虑在多服务器环境下使用分布式缓存
*   在调用imap_header()之前先调用imap_headers()

## 编译 vs. 解释（Compiling vs. Interpreting）

将PHP源码编译成机器码。动态解释执行同样的编译，但它是按行执行的。编译为opcode是折中选择，它可以将PHP源码翻译为opcode，之后opcode再转为机器码。以下为关于编译与解释的相关技巧：

*   上线之前将PHP代码编译为机器码。opcode缓存尽管并不是最好的选择，但依旧比解释型来得强。或者，考虑将PHP代码编译成一个C扩展。
*   PHP的opcode编译器（bcompiler)还不能在产品环境中使用，但是开发者应该关注[http://php.net/manual/en/book.bcompiler.php](http://php.net/manual/en/book.bcompiler.php).

## 代码减肥 （Content Reduction）

越少越块。 这些技巧可以帮助减少代码：

*   每页提供更少的功能
*   清理网页内容
*   如果解释型执行，请清理注释和其他空白
*   减少数据库查询

## 多线程与多进程（Multithreading &amp; Multiprocessing）

由快到慢依次为：

1.  多线程（单一进程中）
2.  多进程（比如，pcntl_fork，计划任务）
3.  单进程（一行又一行）

PHP不支持多线程，但是可以用C写多线程的PHP扩展。有一些办法可以使用多进程或模拟多进程，但支持的并不是很好，没准儿比单进程还慢。

## 字符串（Strings）

字符串处理，是大多数编程语言中最常用的操作之一。这里有些技巧可以帮我们让字符串处理速度更快一些：

*   PHP的连接运算（点运算），是最快的链接方式
*   避免在print中链接字符串，用逗号分割后用ECHO
*   尽可能使用str_前缀的字符串函数替代正则表达式
*   pos()比preg_mach()和ereg()都快
*   有人说单引号包裹字符串比双引号更快，有人说没有区别。当然，如果想在字符串中引用变量，单引号没戏。
*   如果想判断字符串长度是否小于某值（比如5）,请使用isset($s[4])&lt;5。
*   如需将多个小字符串连接成一个大字符串，试着先开启ob_start输出缓存，再用echo输出到缓冲区，完成后使用ob_get_contents读取字符串

## 正则表达式（Regular Expressions）

正则表达式为们带来了灵活多样的比较与查找字符串的方法，单他的性能开销却着实不低

*   尽可能使用STR_前缀的字符串处理函数代替正则表达式
*   使用[aeiou]的不是(a|e|i|o|u)
*   正则表达式越简单速度越快
*   尽可能不要设置PCRE_DOTALL修饰符
*   用 `^.*` 代替 `.*`
*   简化正则表达式。（比如使用`a*` 代替 `(a+)*`

## 迭代结构 （Iteration Constructs (for, while)）

迭代（重复，循环）是最基本的结构化编程方法，很难想像有不使用它的程序。这里有些技巧，帮助我们改进迭代结构的性能：

*   尽可能讲代码移出到循环外（函数调用、SQL查询等等……）
*   使用i=maxval;while(i&#8211;)代替for(i=0;i&lt;maxval;i++），这样可以减少一个操作，如果maxval是一个函数调用就更明显了。
*   使用foreach迭代集合与数组

## 选择结构 （Selection Constructs (if, switch)）

与迭代结构相同，选择结构也是最基本的结构化变成方法。以下技巧或许能改善性能：

*   switches和else-if中，应该将最近常出现true的列在前面，较少出现true的请靠后
*   有人说if-else比swtich/case快，当然，有人反对。
*   用elseif替代else if.

## 函数与参数 （Functions &amp; Parameters）

将函数的代码分解成小函数代码可以消除冗余，让代码具有可读性，但代价是什么？这里有些技巧，以帮助更好的使用函数：

*   引用传递出对象和数组，而不是传值
*   如果只在一个地方使用，使用内联。如果在多个地方调用，考虑内联，但请注意可维护性
*   了解你所用函数的复杂度。比如similar_text()为O(N^3)，这意味着字符串长度增加一倍，处理时间将增加8倍
*   不要通过“返回引用”来提升性能，引擎会自动优化它。
*   以常规方式调用函数，而不是使用call_user_func_array()或eval()

## 面向对象结构 （Object-Oriented Constructs）

PHP的面向对象特性，可能会影响到性能。以下提示可以帮助我们尽量减少这种影响：

*   不是一切都需要面向对象， 性能的损失可能会超过其优点本身
*   创建对象比较慢
*   如果可以，尽可能时候用数组而不是对象
*   如果一个方法可以静态化，请静态声明
*   函数调用比派生类方法调用要快，派生类方法调用比基类调用要快
*   考虑将基类中最常用的代码复制到派生类中，但要注意维护性隐患
*   避免使用原生的getters与setters。如果不需要他们，请删除并且属性公开
*   创建复杂的PHP类时，考虑使用单件模式

## Session处理 （Session Handling）

创建sessions有很多好处，但有时会产生没必要的性能开支。以下技巧可以帮助我们最大限度减少性能开支：

*   不要使用auto_start
*   不要启用use_trans_sid
*   将session_cache_limited设置为private_no_expire
*   为虚拟主机(vhost)中的每个用户分配自己的目录
*   使用基于内存的session处理，而不是基于文件的session处理

## 类型转换 （Type Casting）

从一种类型转换为另一种类型需要成本

## 压缩（Compression）

在传输前，压缩文本和数据：

*   使用ob_start()在代码起始处
*   使用ob_gzhandler()可以下载提速，但是注意CPU开支
*   Apache的mod_gzip模块可以即使压缩

## 错误处理（Error Handling）

错误处理影响性能。我们能做的是：

*   记录错误日志，别再使用“@”抑制错误报告，抑制对性能一样有影响
*   不要只检查错误日志，警告日志一样需要处理

## 声明、定义与范围（Declarations, Definitions, &amp; Scope）

创建一个变量、数组或者对象，对性能都有影响：

*   有人说，声明和使用全局变量/对象，比局部变量/对象要快，有人反对。请测试再决定。
*   在使用变量前声明所有变量，不要声明不使用的变量
*   在循环中尽可能使用$a[]，避免使用$a=array(&#8230;)

## 内存泄漏（Memory Leaks）

如果内存分配后不释放，这绝对是个问题：

*   坚持释放资源，不要指望自带/自动的垃圾回收
*   使用完后尽量注销(unset)变量，尤其是资源类和大数组类型的
*   使用完毕就关闭数据库连接
*   每次使用ob_start()，记得ob_end_flush()或者ob_end_clean()

## 不要重复发明轮子（Don’t Reinvent the Wheel）

为什么要花费时间去解决别人已经解决的问题？

*   了解PHP，了解它的功能和扩展。如果你不知道，可能会无法利用一些现成的功能
*   使用自带的数组和字符串函数，它们绝对是性能最好的。
*   前人发明的轮子，并不意味着在你的环境下吸能是最好的，多多测试

## 代码优化（Code Optimization）

*   使用一个opcode optimizer
*   如果将被解释运行，请精简源码

## 使用RAM（Using RAM Instead of DASD）

RAM比磁盘快很多很多，使用RAM可以提升一些性能：

*   移动文件到Ramdisk
*   使用基于内存的session处理，而不是基于文件的session处理

## 使用服务（Using Services (e.g., SQL)）

SQL经常被用来访问关系型数据库，但我们的PHP代码可以访问许多不同的服务。下面是一些访问服务是需要牢记的：

*   不要一遍又一遍地问服务器向东的事情。使用memoization缓存第一次的结果，以后访问直奔缓存；
*   在SQL中，使用mysql_fetch_assoc()代替mysql_fetch_array()，可以减少结果集中的整数索引。以字段名访问结果集，而不用索引数字。
*   对于Oracle数据库，如果没有足够的可用内存，增加oci8.default_prefetch。将oci8.statement_cache_size设置为应用中的语句数
*   请使用mysqli_fetch_array()替换mysqli_fetch_all()， 除非结果集将发送到其他层进行处理。

## 安装与配置（Installation &amp; Configuration）

安装与配置PHP时，请考虑性能：

*   添加更多内存
*   删除竞争性的应用与服务
*   只编译所需要用的扩展
*   将PHP静态编译进APACHE
*   使用-O3 CFLAGS开启所有编译器优化
*   只安装所需使用的模块
*   升级到最新版本的次要版本。主板本升级，等到第一次bug修复后再进行，当然，也别等太久
*   为多CPU环境进行配置
*   使用 -enable-inline-optimization
*   设置session.save_handler=mm ，以 -with-mmto编译，使用共享内存
*   使用RAM disk
*   关闭resister_global和magic_quotes_*
*   关闭expose_php
*   关闭 always_populate_raw_post_data 除非你必须使用它
*   非命令行模式下请关闭register_argc_argv
*   只在.php文件中使用PHP
*   优化max_execution_time, max_input_time, memory_limit与output_buffering的参数
*   将Apache配置文件中allowoverride设置为none提升文件/目录的访问速度
*   使用-march, -mcpu, -msse, -mmmx, and -mfpmath=sseto使CPU最优化
*   使用MySQL原生驱动（mysqlnd)替换libmysql、mysqli扩展以及PDO MYSQL驱动
*   关闭 register_globals、register_long_arrays以及register_argc_argv. 开启auto_globals_jit.

## 其他（Other）

还有些技巧比较难归类：

*   使用include()、require()，避免使用include_once()和require_once()
*   在include()/require()中使用绝对路径
*   静态HTML被PHP生成的HTML要快
*   使用ctype_alnum、ctype_alpha以及ctype_digit代替正则表达式
*   使用简单的servlets或CGI
*   代码在产品环境中使用时，尽可能写日志
*   使用输出缓冲
*   请使用isset($a)代替比较$a==null；请使用$a===null代替is_nul($a)
*   需要脚本开始执行时间，请直接读取$_SERVER[’REQUEST_TIME’]，而不是使用time()
*   使用echo替代print
*   使用前自增(++i)代替后自增(i++)，大多数编译器现在都会优化，但是他们不优化时，请保持这样的写法。
*   处理XML，使用正则表达式代替DOM或者SAX
*   HASH算法：md4, md5, crc32, crc32b, sha1比其他的散列速度都要快
*   使用spl_autoload_extensions时，文件扩展名请按最常用&#8211;&gt;最不常用的顺序，尽量排除掉压根不用的。
*   使用fsockopen或fopen时，使用IP地址代替域名；如果只有一个域名，使用gethostbyname()可以获取IP地址。使用cURL速度会更快。
*   但凡可能，用静态内容代替动态内容。

