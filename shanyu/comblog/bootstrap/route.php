<?php
return [
    ['GET', '/', 'Index@index'],
    ['GET', '/index[-{p:\d+}]', 'Index@index'],

    ['GET', '/about', 'Page@about'],
    ['GET', '/search', 'Search@index'],

    ['GET', '/article/index-{cid:\d+}[-{p:\d+}]', 'Article@index'],
    ['GET', '/article/detail-{id:\d+}', 'Article@detail'],
    ['GET', '/article/category', 'Article@category'],
    ['GET', '/article/archive', 'Article@archive'],
    ['GET', '/article/tag', 'Article@tag'],


    [["GET","POST"],"/admin","Admin@dispatch"],

    // ['GET',  'admin\login', 'Admin\Account@login'],
    // ['POST', 'admin\login', 'Admin\Account@loginHandle'],

    // ['GET',  'article\index', 'Admin\Article@index'],
    // ['GET',  'article\create', 'Admin\Article@create'],
    // ['POST', 'article\create', 'Admin\Article@save'],
    // ['GET',  'article\update\{id:\d+}', 'Admin\Article@update'],
    // ['POST', 'article\update\{id:\d+}', 'Admin\Article@edit'],
    // ['GET',  'article\delete\{id:\d+}', 'Admin\Article@delete'],


    // ['GET', 'style/index.html', 'Article@index?p=1&cid=1'],
    // ['GET', 'style/index-{p:\d+}.html', 'Article@index?cid=1'],
    // ['GET', 'style/{id:\d+}.html', 'Article@detail?cid=1'],

    // ['GET', 'javascript/index.html', 'Article@index?p=1&cid=2'],
    // ['GET', 'javascript/index-{p:\d+}.html', 'Article@index?cid=2'],
    // ['GET', 'javascript/{id:\d+}.html', 'Article@detail?cid=2'],

    // ['GET', 'program/index.html', 'Article@index?p=1&cid=3'],
    // ['GET', 'program/index-{p:\d+}.html', 'Article@index?cid=3'],
    // ['GET', 'program/{id:\d+}.html', 'Article@detail?cid=3'],

    // ['GET', 'server/index.html', 'Article@index?p=1&cid=4'],
    // ['GET', 'server/index-{p:\d+}.html', 'Article@index?cid=4'],
    // ['GET', 'server/{id:\d+}.html', 'Article@detail?cid=4'],

    // ['GET', 'life/index.html', 'Article@index?p=1&cid=5'],
    // ['GET', 'life/index-{p:\d+}.html', 'Article@index?cid=5'],
    // ['GET', 'life/{id:\d+}.html', 'Article@detail?cid=5'],

    // ['GET', 'form.html', 'Demo@form'],
    // ['POST', 'form', 'Demo@formHandle'],

    // ['GET', 'admin/login.html', ['Admin\Account','login']],
    // ['POST', 'admin/login', ['Admin\Account','loginHandle']],
    // ['GET', 'admin/index.html', ['Admin\Index','index']],
    // ['GET', 'admin/article/index.html', ['Admin\Article','index?p=1']],
    // ['GET', 'admin/article/index-{p:\d+}.html', ['Admin\Article','index']],
    // ['GET', 'admin/article/create.html', ['Admin\Article','create']],
    // ['POST', 'admin/article/create', ['Admin\Article','createHandle']],
    // ['GET', 'admin/article/update-{id:\d+}.html', ['Admin\Article','update']],
    // ['POST', 'admin/article/update-{id:\d+}', ['Admin\Article','updateHandle']],
    // ['GET', 'admin/article/delete-{id:\d+}', ['Admin\Article','deleteHandle']],
];