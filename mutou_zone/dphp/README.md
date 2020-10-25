# Thinker

已经不更新了，现在很多框架已经非常的现代化了，包括thinkphp已经能是实现本框架思想了，异曲同工之妙。

# 如何选择一款好的框架

- 数据来源皆不可信，完善的人性化数据校验机制
- 灵活的路由控制
- 优雅的架构设计模式
- 极度方便的ORM操作
- 丰富的工具链

# 环境要求
- php >= 7.0 
- composer > 1.4
# 安裝
```bash
git clone https://github.com/diiyw/thinker.git
```

```bash
# cd thinker
# composer install
```
#### 目录结构
```
.
├── app
│   ├── composer.json
│   ├── composer.lock
│   ├── database
│   │   ├── migrations
│   │   ├── phinxConfig.php
│   │   └── seeds
│   ├── modules
│   │   └── home
│   │       ├── controller
│   │       │   ├── Index.php
│   │       │   └── IndexFilter.php
│   │       └── HomeConst.php
│   └── plugins
│       └── thinker
│           ├── Session.php
│           └── Whoops.php
├── README.md
├── views
│   └── default
│       ├── common
│       │   └── footer.phtml
│       └── home
│           └── index.phtml
└── www
    ├── cache
    │   └── default
    │       ├── common
    │       │   └── footer.phtml
    │       └── home
    │           └── index.phtml
    ├── index.php
    └── logs
        └── sys.log
```

# 快速创建模块
```
# cd app/module
# ../vendor/bin/thinker create
```
按照提示即可一步创建
