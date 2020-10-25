<?php

/**
 * Util
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\View;
use Madphp\View as ViewProvider;

class Util
{
    /**
     * 获取视图文件路径
     */
    public static function getFilePath(ViewProvider $viewProvider)
    {
        $path = $viewProvider->isLayoutFile ? $viewProvider->layout->layoutPath : $viewProvider->viewPath;
        return self::getPath($path, $viewProvider->viewName);
    }

    /**
     * 渲染模板文件
     */
    public static function render(ViewProvider $object, $data = null)
    {
        $data = array_merge((array) $object->data, (array) $data);

        // 开启Layout
        if ($object->isLayout) {
            $output = self::getOutput($object, $data);
            $object->layout->set('content', $output);
            $allData = array_merge($data, array('layoutData' => $object->layout->data));
            $layoutObject = ViewProvider::make($object->layout->layoutName, true);
            return self::getOutput($layoutObject, $allData);
        } else {
            return self::getOutput($object, $data);
        }
    }

    /**
     * 获取渲染模板文件的输出内容
     */
    protected static function getOutput(ViewProvider $object, $data = null)
    {
        if ($object->isCompiler) {
            $viewStr = file_get_contents($object->viewFile);
            $viewCompiledStr = $object->compiler->parse($viewStr);

            $compiledFile = self::getCompiledFile($object);
            if (!file_exists($compiledFile) or filemtime($object->viewFile) > filemtime($compiledFile)) {
                $compiledPath = pathinfo($compiledFile, PATHINFO_DIRNAME);
                mkdirs($compiledPath, 0777);
                file_put_contents ($compiledFile, $viewCompiledStr);
                chmod ($compiledFile, 0777);
            }

            return self::load($compiledFile, $data);
        } else {
            return self::load($object->viewFile, $data);
        }
    }

    /**
     * 加载文件
     */
    protected static function load($file, $data = null)
    {
        extract($data);
        ob_start();
        require $file;
        return ob_get_clean();
    }

    /**
     * 获取编译后的视图文件路径
     */
    protected static function getCompiledFile(ViewProvider $viewProvider)
    {
        $folder = $viewProvider->isLayoutFile ? $viewProvider->layout->layoutFolder : $viewProvider->viewFolder;
        $path = $viewProvider->compiler->compilerRoot . $folder . DIRECTORY_SEPARATOR;
        return self::getPath($path, $viewProvider->viewName);
    }

    /**
     * 获取文件路径
     */
    protected static function getPath($path, $viewName)
    {
        $filePath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);
        return $path . $filePath . '.php';
    }
}
