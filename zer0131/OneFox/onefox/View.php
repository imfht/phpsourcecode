<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 视图类
 */

namespace onefox;

class View {

    protected $tplVal = [];
    protected $ext = '';

    public function __construct($ext = '') {
        if ($ext) {
            $this->ext = '.' . $ext;
        } else {
            $this->ext = '.php';
        }
    }

    /**
     * 模板赋值
     * 传入数组则为批量赋值
     * @param array|string $name
     * @param string $value
     */
    public function assign($name, $value = '') {
        if (is_array($name)) {
            $this->tplVal = array_merge($this->tplVal, $name);
        } else {
            $this->tplVal[$name] = $value;
        }
    }

    /**
     * 输出模板
     * @param string $tplFile
     */
    public function render($tplFile = '') {
        $content = $this->_getFetch($tplFile, $this->tplVal);
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-control: private');  // 页面缓存控制
        header('X-Powered-By: OneFox');
        header('SN: ' . REQUEST_ID);
        Response::setResponseData([
            'template' => $this->_parsePath($tplFile),
            'template_value' => $this->tplVal
        ]);
        Response::setResponseType('text/html');
        echo $content;
    }

    public function fetch($tplFile = '') {
        return $this->_getFetch($tplFile, $this->tplVal);
    }

    //获取模板内容
    private function _getFetch($tplFile, $data) {
        $tplFile = $this->_parsePath($tplFile);
        if (!is_file($tplFile)) {
            throw new \RuntimeException('模板文件未找到');
        }
        ob_start();
        ob_implicit_flush(0);
        extract($data);
        include $tplFile;
        $content = ob_get_clean();
        return $content;
    }

    /**
     * 用于包含模板
     * 示例：$this->import('header', ['title'=>'xxxx']);
     * @param string $path
     * @param array $newVal
     */
    public function import($path, $newVal = []) {
        if (!$path) {
            throw new \RuntimeException('模板路径不正确');
        }
        $path = $this->_parsePath($path);
        echo $this->_getFetch($path, array_merge($this->tplVal, $newVal));
    }

    /**
     * 解析模板路径
     * @param string $path
     * @return string
     */
    private function _parsePath($path) {
        if (is_file($path)) {
            return $path;
        }
        if ('' === $path) {
            $controllerName = strtolower(CURRENT_CONTROLLER);
            if (MODULE_MODE) {
                $path = CURRENT_MODULE . DS . $controllerName . DS . CURRENT_ACTION;
            } else {
                $path = $controllerName . DS . CURRENT_ACTION;
            }
        }
        return TPL_PATH . DS . $path . $this->ext;
    }

}
