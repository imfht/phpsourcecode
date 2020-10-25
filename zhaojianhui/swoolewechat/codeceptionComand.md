#控制台命令

###DRYRUN
显示针对场景驱动测试的逐步执行过程，而不实际运行它们。
```sh
codecept dry-run acceptance
codecept dry-run acceptance MyCest
codecept dry-run acceptance checkout.feature
codecept dry-run tests/acceptance/MyCest.php
```
###GenerateSuite
创建新的测试套件。需要套件名称和演员姓名
```sh
codecept g:suite api -> api + ApiTester
codecept g:suite integration Code -> integration + CodeTester
codecept g:suite frontend Front -> frontend + FrontTester
```
###GherkinSnippets
为套件中匹配的功能文件生成代码段。代码漏洞预计将在Actor或PageOjects中实现
用法：
```sh
codecept gherkin:snippets acceptance - 从验收测试的所有功能的片段
codecept gherkin:snippets acceptance/feature/users- feature/users验收测试目录的片段
codecept gherkin:snippets acceptance user_account.feature - 单个功能文件的片段
codecept gherkin:snippets acceptance/feature/users/user_accout.feature - 目录中的功能文件的片段
```
##初始化
###Console
尝试在运行时执行测试命令。您可以在写测试之前尝试命令。
```sh
codecept console acceptance - 启动接受套房环境。如果您使用WebDriver，您可以使用Codeception命令来操作浏览器。
```
###ConfigValidate
验证并打印Codeception配置。使用它做调试Yaml配置

检查配置：
```sh
codecept config：检查全局配置
codecept config unit：检查套件配置
```
加载配置：
```sh
codecept config:validate -c path/to/another/config：从另一个目录
codecept config:validate -c another_config.yml：从另一个配置文件
```
检查覆盖配置值（如run命令中）
```sh
codecept config:validate -o "settings: shuffle: true"：启用随机播放
codecept config:validate -o "settings: lint: false"：禁用linting
codecept config:validate -o "reporters: report: \Custom\Reporter" --report：用自定义记者
```
###GenerateGroup
创建处理所有组事件的空GroupObject - 扩展。
```sh
codecept g:group Admin
```
###生成器
生成Cept（场景驱动测试）文件：
```sh
codecept generate:cept suite Login
codecept g:cept suite subdir/subdir/testnameCept.php
codecept g:cept suite LoginCept -c path/to/project
```
`generate:cept` 测试套件 文件名 -生成一个场景驱动测试<br>
`generate:cest` 测试套件 文件名 -生成一个场景驱动的面向对象的测试<br>
`generate:test` 测试套件 文件名 -生成Codeception挂钩样本PHPUnit的测试<br>
`generate:phpunit` 测试套件 文件名 -生成一个经典的PHPUnit测试<br>
`generate:feature` 测试套件 文件名 -生成小黄瓜功能文件<br>
`generate:suite` 测试套件 演员 -生成一个新的套件给定的演员类名<br>
`generate:scenarios` 套装 -生成一个包含从测试场景的文本文件<br>
`generate:helper` 文件名 -生成一个样本助手文件<br>
`generate:pageobject` 测试套件 文件名 -生成一个样本Page对象<br>
`generate:stepobject` 测试套件 文件名 -生成一个样本对象步<br>
`generate:environment` ENV -生成一个样本环境配置<br>
`generate:groupobject` 集团 -生成一个样本组扩展<br>

###运行测试
测试可以用来启动run命令：
```sh
用法：
vendor/bin/codecept run
vendor/bin/codecept run acceptance：运行所有验收测试
vendor/bin/codecept run tests/acceptance/MyCept.php：只运行MyCept
vendor/bin/codecept run acceptance MyCept：同上
vendor/bin/codecept run acceptance MyCest:myTestInIt：从Cest运行一个测试
vendor/bin/codecept run acceptance checkout.feature：运行功能文件
vendor/bin/codecept run acceptance -g slow：从慢组运行测试
vendor/bin/codecept run unit,functional：只运行单元和功能套件
详细模式：
vendor/bin/codecept run -v：
vendor/bin/codecept run --steps：打印分步执行
vendor/bin/codecept run -vv：
vendor/bin/codecept run --debug：打印步骤和调试信息
vendor/bin/codecept run -vvv：打印内部调试信息
加载配置：
codecept run -c path/to/another/config：从另一个目录
codecept run -c another_config.yml：从另一个配置文件
覆盖配置值：
codecept run -o "settings: shuffle: true"：启用随机播放
codecept run -o "settings: lint: false"：禁用linting
codecept run -o "reporters: report: \Custom\Reporter" --report：用自定义记者
运行特定扩展
codecept run --ext Recorder 运行记录器扩展启用
codecept run --ext DotReporter 运行DotReporter打印机
codecept run --ext "My\Custom\Extension" 运行与类名加载的扩展名
```
###自更新
```sh
php codecept.phar self-update
```
###GenerateTest
生成扩展的单元测试的骨架Codeception\TestCase\Test
```sh
codecept g:test unit User
codecept g:test unit "App\User"
```
###建立
从suite configs生成Actor类（最初是Guy类）。从Codeception开始，2.0 actor类是自动生成的。使用此命令手动生成它们。
```sh
codecept build
codecept build path/to/project
```
###GenerateHelper
创建空的助手类。
```sh
codecept g:helper MyHelper
codecept g:helper "My\Helper"
```
###引导
为当前项目创建默认配置，测试目录和样例套件。使用此命令开始构建测试套件。

默认情况下，它将创建3套套房验收，功能和单元。
```sh
codecept bootstrap- 创建tests目录和codeception.yml当前目录。
codecept bootstrap --empty- 创建tests没有套房的目录
codecept bootstrap --namespace Frontend- 创建测试，并Frontend为演员类和助手使用命名空间。
codecept bootstrap --actor Wizard- 将演员作为巫师，让TestWizard演员进行测试。
codecept bootstrap path/to/the/project - 为项目提供不同的路径，其中应该进行测试
```
###GenerateEnvironment
将空环境配置文件生成到envs dir中：
```sh
codecept g:env firefox
```
需要envs指定路径codeception.yml

###GenerateFeature
生成Feature文件（在Gherkin中）：
```sh
codecept generate:feature suite Login
codecept g:feature suite subdir/subdir/login.feature
codecept g:feature suite login.feature -c path/to/project
```
###GenerateScenarios
从场景驱动的测试（Cest，Cept）生成用户友好的文本场景。
```sh
codecept g:scenarios acceptance - 所有验收测试
codecept g:scenarios acceptance --format html - 以html格式
codecept g:scenarios acceptance --path doc- 生成场景到doc目录
```
###GenerateStepObject
生成StepObject类。系统会要求您提供您要实施的步骤。
```sh
codecept g:step acceptance AdminSteps
codecept g:step acceptance UserSteps --silent - 跳过动作问题
```
###清洁
清理output目录
```sh
codecept clean
codecept clean -c path/to/project
```
###GherkinSteps
从特定套件的所有Gherkin上下文中打印所有步骤
```sh
codecept gherkin:steps acceptance
```
##完成
###GenerateCest
生成Cest（场景驱动的面向对象测试）文件：
```sh
codecept generate:cest suite Login
codecept g:cest suite subdir/subdir/testnameCest.php
codecept g:cest suite LoginCest -c path/to/project
codecept g:cest "App\Login"
```
###GeneratePageObject
生成PageObject。可以在全局生成，也可以在一个套件中生成。如果PageObject是全局生成的，它将作为UIMap，没有任何逻辑。
```sh
codecept g:page Login
codecept g:page Registration
codecept g:page acceptance Login
```
