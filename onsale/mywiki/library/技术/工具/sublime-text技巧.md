## 技巧
### 文件记录Recent Files
`File/Open Recent`可以打开最近打开的文件。但是记录的数量很少，参照[覆盖菜单](http://stackoverflow.com/questions/20540492/how-to-increase-number-of-recent-files-in-sublime-text-3)方案以解决。

### 自定义快捷键

菜单栏点击`Preferences/Key Bindings`，即可打开快捷键配置文件。

- 快速切换project
```json
{"keys": ["ctrl+alt+p"], "command": "prompt_select_workspace"}
```
- Alignment插件
```json
{ "keys": ["ctrl+shift+f"], "command": "alignment" }
```

- 设置
```
// 最后一行自动留空
"ensure_newline_at_eof_on_save": true
// 去除行尾多余空格
"trim_trailing_white_space_on_save": true
```

## 插件
安装插件必先安装Package Control。其本身也是插件。
在st3中，`ctrl+shift+p`打开命令面板，搜索`install package control`，即可安装。

其它必选插件：

### Alignment
代码对齐
### DocBlockr
代码注释
### Emmet
强大到不解释！需要自定义扩展，可以在Emmet插件设置中，添加snippets。下面以增加thinkphp模板标签为例：
```json
{
    "snippets": {
        "html": {
         "abbreviations": {
             "doc": "html>(head>meta[charset=${charset}]+meta[name=viewport content=\"width=device-width,initial-scale=1.0\"]+title{${1:Document}})+body",
             "if": "<if condition=''>",
             "eq": "<eq name='' value=''>",
             "lt": "<lt name='' value=''>",
             "gt": "<gt name='' value=''>",
             "empty": "<empty name=''>",
             "notempty": "<notempty name=''>",
             "present": "<present name=''>",
             "foreach": "<foreach name='' item='vo'>"
         }
        }
    }
}
```
### SFTP
SFTP、FTP一键上传文件
### TypeScript
TypeScript
### IMESupport
st中文输入法文字框跟随光标有问题，需要此插件以解决
### Vue Syntax
vue.js单文件组件高亮、提示插件
