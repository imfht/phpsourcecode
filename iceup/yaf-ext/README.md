文档已经全部更新，参考地址：[php yaf框架扩展实践](http://www.01happy.com/php-yaf-ext-preface/)

早年使用zend framework开发的时候非常爽，各种类库非常齐全，官方文档介绍也很详细。但是当访问量上来的时候，每次到晚上服务器负载就直飙上来导致服务器挂掉。后来和大家一起去找原因，才发现原来zend framework性能非常低。一开始的时候尝试把访问频繁的接口进行裸写，但是这只是临时做法，长远来看没有框架的约束后续的开发就会越来越乱。

经过好友@老雷的介绍认识yaf框架，了解了一下yaf是c语言写的，测试了下hello world的性能确实非常快，和原生的php差不多。yaf的目录结构和zend framework又非常接近，开发人员迁移过来也很方便。后来就尝试在项目中使用yaf，确实非常不错，然后就一直用到现在。

备注：这里说的zend框架指的是1，zend 2在目录结构上变化很大，这里就不讨论了。关于zend framework和yaf等框架的性能测试，上次看到老外的一篇文章对各种php框架做了比较，可以参考下：http://www.ruilog.com/blog/view/b6f0e42cf705.html

虽然yaf性能很快，但是缺少诸如表单、数据库操作等类库的封装，在开发上不免带来不便。在长期的开发中自己封装了一些类库，总结了一套开发的想法分享出来。

#感谢
感谢以下贡献者：

    $ git summary

    project  : yaf-ext
    repo age : 2 years, 4 months
    active   : 37 days
    commits  : 82
    files    : 2415
    authors  : 
       78	chenjiebin  95.1%
        3	Richard     3.7%
        1	陈杰斌      1.2%