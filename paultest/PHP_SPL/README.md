# SPL
SPL的常用数据结构以及常用迭代器等，主要是参考慕课网的《站在巨人的肩膀上写代码-SPL》教程

## 1-1 概述
####  1. 什么是SPL

* SPL是Standard PHP Library的缩写
* 官方定义：The Standard PHP Library（SPL） is a collection of interfaces and classes that are meant to solve common problems。它是用于解决典型（常见）问题（common problems）的一组接口与类的集合。

####  2. Common Problem

数据建模/数据结构
* 解决数据怎么存储的问题

元素遍历
* 数据怎么查看的问题

常用方法的统一调用：
* 通用方法（数组、集合的大小）
* 自定义遍历

类定义的自动装载
* 让PHP程序适应大型项目的管理要求，把功能的实现分散到不同的文件中

#### 3. SPL的基本框架
![Alt1](/img/1.jpg)

## 2-1 SPL数据结构简介
####  1. 数据结构：
* 数据结构是计算机存储、组织数据的方式。数据结构是指相互之间存在一种或多种特定关系的数据元素的集合。
* 解决的是软件开发过程中的数据如何存储和表示的问题

####  2. SPL的数据结构有：
双向链表、堆栈、队列、堆、降序堆、升序堆、优先级队列、定长数组、对象容器

如图：

![Alt2](/img/2.jpg)

## 2-2 SPL数据结构之双向链表简介
#### 1. 单向链表，如图：
![Alt3](/img/3.jpg)

#### 2. 双向链表，如图：
![Alt4](/img/4.jpg)

#### 3. 基本概念：
* Bottom：最先添加到链表中的节点叫做Bottom（底部），也称为头部（Head），比如说上图中的节点1
* Top：最后添加到链表中的节点叫做Top（顶部），也称为尾部，比如说上图中的节点3
* 链表指针：是一个当前关注的节点的标识，可以指向任意节点
* 当前节点：链表指针指向的节点称为当前节点

#### 4. 每一个节点又可以细分化为：
* 节点名称：可以在链表中唯一标识一个节点的名称，通常又称为节点的key或者offset
* 节点数据：存放在链表中的应用数据，通常称为value

比如：

![Alt5](/img/5.jpg)

## 2-3 SPL数据结构之SplDoublyLinkedList类
#### 1. 当前节点操作：
* rewind（使当前节点指向头部）
* current（获得当前节点）
* next（使当前节点到下一个节点）
* prev（使当前节点到上一个节点）
#### 2. 增加节点操作：
* push（尾部添加一个节点）
* unshift（头部添加一个节点）
#### 3. 删除节点操作
* pop（删除尾部的第一个节点）
* shift（删除头部的第一个节点）
#### 4. 定位操作：
* bottom（获取头部的元素）
* top（获取底部的元素）
#### 5. 特定节点操作：
* offsetExists（判断特定节点是否存在）
* offsetGet（获得某个节点的数据）
* offsetSet（设置某个节点的数据）
* offsetUnset（释放某个节点的数据）

## 2-4 SPL数据结构之双向链表的代码实现
具体见2_4_SplDoublyLinkedList.php

## 2-6 SPL数据结构之堆栈的简介
1. 堆栈：最后进入到堆栈里的数据最先拿出堆栈，即First In Last Out（FILO）

如图：

![Alt6](/img/6.jpg)

2. PHP的堆栈类是继承自SplDoublyLinkenList类的SplStack类
操作：
* push：压入堆栈（存入）
* pop：退出堆栈（取出）

## 2-7 SPL数据结构之堆栈的代码实现
具体见2_7_SplStack.php

## 2-8 SPL数据结构之队列
1. 队列正好和堆栈相反，最先进入队列的元素会最先走出队列

2. 队列继承自SplDoublyLinkedList类的SplQueue类

操作：
* enqueue：进入队列
* dequeue：退出队列

3. 队列的操作和堆栈很类似

代码见：2_8_SplQueue.php

## 3-1 SPL迭代器概述
1. 什么是迭代器：通过某种统一的方式遍历链表或者数组中的元素的过程叫做遍历，而这种统一的遍历工具叫做迭代器

2. PHP中的迭代器是通过Iterator接口去定义的，Iterator的操作方法有：
* current(): mixed
* key(): scalar
* next(): void
* rewind(): void
* valid(): bool

## 3-2 Arraylterator迭代器
1. ArrayInterator迭代器：用于遍历数组

2. 如果要来遍历数组的话，传统的方法是用foreach即可
```
    $fruits = [
        'apple'   => 'apple value',
        'orange'  => 'orange value',
        'grape'   => 'grape value',
        'plum'    => 'plum value'
    ];
    
    // 使用传统的foreach遍历数组
    echo "使用传统的foreach遍历数组:" . PHP_EOL;
    foreach ($fruits as $key => $value) {
        echo $key . " : " . $value . PHP_EOL;
    }
```
也可以使用ArrayInterator迭代器来遍历数组：
```
    $fruits = [
        'apple'   => 'apple value',
        'orange'  => 'orange value',
        'grape'   => 'grape value',
        'plum'    => 'plum value'
    ];
    


    // 实例化ArrayIterator
    $obj = new ArrayObject($fruits);
    $it = $obj->getIterator();
    
    // 使用ArrayIterator来遍历数组（foreach）
    echo PHP_EOL . "使用ArrayIterator来遍历数组（foreach）:" . PHP_EOL;
    foreach ($it as $key => $value) {
        echo $key . " : " . $value  . PHP_EOL;
    }
    
    // 使用ArrayIterator来遍历数组（while）
    echo PHP_EOL . "使用ArrayIterator来遍历数组（while）:" . PHP_EOL;
    $it->rewind();
    while ($it->valid()) {
        echo $it->key() . " : " . $it->current() . PHP_EOL;
        $it->next();
    }
```

3. 如果是要在数组中取出部分数据的话，用ArrayIterator迭代器的话会更方便一些：
```
    // 跳过某些元素进行打印
    echo PHP_EOL . "使用ArrayIterator来遍历数组（跳过某些元素）:" . PHP_EOL;
    $it->rewind();
    if ($it->valid()) {
        $it->seek(2);    // 设置指针的位置，为n时即跳过前面n-1的元素
        while ($it->valid()) {
            echo $it->key() . " : " . $it->current() . PHP_EOL;
            $it->next();
        }
    }
    /*
    grape : grape value
    plum : plum value
     */
```

4. ArrayIterator迭代器也可以很方便的对数组进行排序：
```
    // 对key进行字典序排序
    echo PHP_EOL . "使用ArrayIterator来遍历数组（对key排序）:" . PHP_EOL;
    $it->ksort();
    $it->rewind();
    while ($it->valid()) {
        echo $it->key() . " : " . $it->current() . PHP_EOL;
        $it->next();
    }
    /*
    apple : apple value
    grape : grape value
    orange : orange value
    plum : plum value
     */
    
    // 对value进行字典序排序
    echo PHP_EOL . "使用ArrayIterator来遍历数组（对value排序）:" . PHP_EOL;
    $it->asort();
    $it->rewind();
    while ($it->valid()) {
        echo $it->key() . " : " . $it->current() . PHP_EOL;
        $it->next();
    }
    /*
    apple : apple value
    grape : grape value
    orange : orange value
    plum : plum value
     */
```

5. 完整的代码见：3_2_ArrayIterator.php

6. PHP的简单的数组代码例子，见3_2_Array.php

## 3-3 Appendlterator迭代器
1. AppendIterator迭代器：陆续遍历几个迭代器

2. AppendIterator迭代器可以通过append方法把多个ArrayIterator迭代器对象放到一起来遍历

3. 代码见：3_3_Appendlterator.php

## 3-4 Multiplelterator迭代器
1. MultipleIterator迭代器用于把多个Iterator里面的数据组合成为一个整体来访问

2. MultipleIterator迭代器可以按数字作为数组的key，代码见：3_4_MultipleIterator_1.php

3. 也可以按字符串作为数组的key，代码见3_4_MultipleIterator_2.php

## 3-5 Filesystemlterator迭代器
1. Filesystemlterator迭代器用于遍历文件系统

2. 代码见3_5_Filesystemlterator.php

## 4-1 SPL接口简介
1. SPL的基础接口里面定义了最常用的接口：
* Countable：继承了该接口的类可以直接调用count()得到元素个数
* OuterIterator：如果想对迭代器进行一定的处理之后再返回，可以用这个接口
* RecursiveIterator：可以对多层结构的迭代器进行迭代，比如遍历一棵树
* SeekableIterator：可以通过seek方法定位到集合里面的某个特定元素

## 4-2 Countable接口 
1. count()方法是对象继承Countable后必须实现的方法，即某个类继承Countable，类中必须定义count方法。这样的话，直接使用count方法时会调用对象自身的count方法。

2. 代码见4_2_Countable.php

## 4-3 Outerlterator接口
1. OuterIterator接口：可以对迭代器进行一定的处理后返回

2. IteratorIterator类是OuterIterator接口的实现，扩展的时候可以直接继承IteratorIterator类。

3. 代码见4_3_OuterIterator.php

## 4-4 Recursivelterator接口
1. Recursivelterator接口：可以对多层结构的迭代器进行迭代，比如说遍历一棵树。所有具有层次结构特点的数据都可以用这个接口遍历，比如说文件夹。

2. 关键方法：
* hasChildren方法：用于判断当前节点是否存在子节点
* getChildren方法：用于得到当前节点子节点的迭代器

3. SPL中实现该接口的类：
RecursiveArrayIterator、RecursiveCachingIterator、RecursiveDirectoryIterator等以Recursive开头的类都能够进行多层次结构化的遍历

## 4-5 Seekablelterator接口
1. Seekablelterator可以通过seek方法定位到集合里面的某个特定元素

2. seek方法：参数是元素的位置，从0开始计算（在3_2_ArrayIterator.php有使用）

3. SPL中实现该接口的类：
ArrayIterator、DirectoryIterator、FilesystemIterator、GlobIterator、RecursiveArrayIterator、RecursiveDirectoryIterator

## 5-1 SPL使用spl_autoload_register函数装载类
1. Autoload：为了初始化PHP中的对象，需要通过一定的方法定位到类的定义。通常情况下，类会定义在一个单独的文件中。Autoload就是找到这些类文件的方法，即类自动加载函数

2. 先在当前目录定义一个新的目录，命名为：5_1_Class，里面放入两个php，内容都为：
```
<?php
class Test
{
    public function __construct()
    {
        echo "加载Test.class.php的Test Class，这是初始化";
    }
}
```

稍微echo的内容不一样，其他的都是一样的，都是Test方法，两个php一个命名为Test.php，另一个是Test.class.php。如图：

![Alt7](/img/7.png)

然后目录外定义一个PHP(5_1_autoload_register.php)，来演示自动装载类：
```
<?php
/**
 * spl_autoload_register函数
 * 类自动加载函数
 */

// 注册并返回spl_autoload函数使用的默认文件扩展名，可以有多个，先找第一个，如没有才找接下来的后缀
spl_autoload_extensions('.php, .class.php');

// 加载类的路径
set_include_path(get_include_path() . PATH_SEPARATOR . "5_1_Class/");

// 让类的自动加载生效
spl_autoload_register();

// 调用自动加载的类
new Test();
```
运行：
加载Test.php的Test，这是初始化

如果代码改成：
````
spl_autoload_extensions('.class.php, .php');
````
那么就会输出：
加载Test.class.php的Test Class，这是初始化

3. spl_autoload_register自定义自动加载函数，首先定义一个自动加载的函数，然后使用spl_auto_register([自动加载函数名])来调用该函数

## 5-2 SPL使用__autoload装载类
1. 除了上面的使用spl_autoload_register来实现自动装载类，还可以使用__autoload函数来装载类

2. 5_2_autoload_1.php:
````
<?php
/**
 * autoload装载类
 */

/**
 * 定义__autoload函数，可以自动完成类的装载
 * @param $class_name
 */
function __autoload($class_name)
{
    echo "__autoload class:" . $class_name . PHP_EOL;

    // 装载类
    require_once('5_1_Class/' . $class_name . ".php");
}

new Test();
/**
__autoload class:Test
加载Test.php的Test，这是初始化
 */
````

3. 如果spl_autoload_register函数和__autoload函数同时存在的时候，原来的 __autoload()方法将不会再调用

比如：

5_2_autoload_2.php:

```
<?php
/**
 * 如果spl_autoload_register函数和__autoload函数同时存在的时候，原来的 __autoload()方法将不会再调用
 */

/**
 * 定义__autoload函数，可以自动完成类的装载
 * @param $class_name
 */
function __autoload($class_name)
{
    echo "__autoload class:" . $class_name . PHP_EOL;

    // 装载类
    require_once('5_1_Class/' . $class_name . ".php");
}

/**
 * 定义一个用来替换__autoload函数的类文件装载函数
 * 需要使用spl_autoload_register('classLoader')来实现自动装载
 * @param $class_name
 */
function classLoader($class_name)
{
    echo "classLoader() load class:" . $class_name . PHP_EOL;

    // 装载类
    require_once('5_1_Class/' . $class_name . ".php");
}

// 传入定义好的类文件装载函数来实现自动装载
spl_autoload_register('classLoader');

new Test();
/*
classLoader() load class:Test
加载Test.php的Test，这是初始化
 */
```

## 5-3 SPL通过自定义的__autoload函数装载类
1. 上面的例子是通过require_once函数来载入类文件，如果我们不想通过require或者是require_once来载入类文件时，而是想通过系统自动查找文件名来装载类的时候，必须显式调用spl_autoload函数，参数为类的名称来重启类文件的自动查找（装载），另外，当使用 spl_autoload函数的时候，require函数会失去作用了

2. 代码见：5_3_splautoload.php

## 5-4 SPL其他函数
1. SPL类载入基本流程：

![Alt8](/img/8.jpg)

2. 迭代器相关函数：
* iterator_apply：为迭代器中每个元素调用一个用户自定义函数（比如说对数组迭代器中每一个元素进行平方等）
* iterator_count：计算迭代器中元素的个数
* iterator_to_array：将迭代器中的元素拷贝到数组

3. 类信息相关函数：
* class_implements：返回指定的类实现的所有接口
* instanceof：判断某个对象是否实现了某个接口或者是某个类的实例
* class_parents：返回指定类的父类，如果继承了多次，会把所有的父类都打印了出来

## 6-1 SPL的文件处理类库
1. 文件处理类库：
* SplFileInfo：用于获得文件的基本信息，比如修改时间、大小、目录等信息
* SplFileObject：用于操作文件的内容，比如读取、写入

2. 代码见：6_1_SplFile.php