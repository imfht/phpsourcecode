# LessPLS
LessPLS是一个使用PHP实现的轮询编译LESS文件的快速工具！
https://packagist.org/packages/medz/lesspls
## 如何获得LessPLS
```shell
composer global require --prefer-dist medz/lesspls dev-master
```
 * 确保PATH变量已经添加了*~/.composer/vendor/bin* 这样LessPLS就会被系统检测到，方便快速使用工具。
 * widowns的PATH变量位置 *C:\Users\你的系统用户名称\AppData\Roaming\Composer\vendor\bin*
 * 重要的一点，确保php在你的path变量中！
 

## LessPLS演示

### Windows
```shell
LessPLS -iC:\Less
```
### MAC or Linux
```shell
LessPLS -i/less
```
## 参数详解
* -i 输入目录，该目录为less文件所在目录（必选）。
* -o 输出目录，该目录为编译后的css文件存放目录。
* -r 是否需要递归，检查输入目录子目录，如果使用该参数，程序会递归检查输入目录的子目录下的less文件，并在输出目录下递归创建。
* -e less文件拓展名，LessPLS中默认是.less后缀，如果你使用了自定义后缀，需要传入该参数，参数后面跟上你的自定义后缀，无需加“.”符号。
* -n css文件拓展名，LeeePLS中默认是.css后缀，如果你像使用你的自定义后缀，只需要传入该参数即可，参数后面跟上自定义后缀名，也无需加上“.”符号。

## 参数注意事项
+ 在LessPLS中，启动的时候传递参数不可传递重复参数，否则程序无法运行，内部做了判断，会直接退出。
+ 在LessPLS中传递参数，参数类型和参数值中间不能用空格或者tab分割，LessPLS也不支持分隔开后的参数读取。
+ -r的值是固定的，只能是1或者0，1代表开启递归，0表示不开启，传入大于0以上的数字或者字符都表示开启。
+ 如果是CMD常规运行，请不要关闭CMD窗口，否则程序失效，也不要输入Ctrl+C，也会中端LessPLS运行。
+ 如果使用 start 来运行LessPLS，可以关闭原有的cmd窗口，新的CMD窗口无需关闭，当关闭LessPLS的时候，这个CMD窗口会自动消失。
+ 如果你希望在less文件原为止编译创建对应的css，无需传入-o参数，LessPLS会在less文件的同位置下创建编译后的less文件供使用。

## 完整的CMD下启动演示(其他系统同理)
```shell
LessPLS -iC:\less\input -oC:\less\output -r1 -eless -ncss
```
## 如何退出LessPLS
进入输出目录，在LessPLS运行的时候，会在输出目录下创建一个叫做.LessPLS的目录，只需要删除该目录，LessPLS会自动释放退出。
