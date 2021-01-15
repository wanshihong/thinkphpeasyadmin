<?php


namespace easyadmin\app\libs;

/**
 * 资源文件相关方法
 * Class Resource
 * @package easyadmin\app\libs
 */
class Resource
{

    /**
     * css 资源文件
     * @var array
     */
    private $cssFiles = [];

    /**
     * js 资源文件
     * @var array
     */
    private $jsFiles = [];


    /**
     * web根目录 资源文件根目录
     * @var string
     */
    private $publicPath;

    /**
     * 私有属性，用于保存实例
     * @var Resource
     */
    private static $instance;

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
        $this->installStatic();
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    /**
     * 公有方法，用于获取实例
     * @return Resource
     */
    public static function getInstance(): Resource
    {
        //判断实例有无创建，没有的话创建实例并返回，有的话直接返回
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * @return array
     */
    public function getCssFiles(): array
    {
        return array_unique($this->cssFiles);
    }

    /**
     * 添加一个 css 文件 到最后
     * @param $path
     * @return Resource
     */
    public function appendCssFile($path): Resource
    {
        array_push($this->cssFiles, $path);
        return $this;
    }

    /**
     * 添加一个 css 文件 到最前面
     * @param $path
     * @return Resource
     */
    public function insertCssFile($path): Resource
    {
        array_unshift($this->cssFiles, $path);
        return $this;
    }


    /**
     * @return array
     */
    public function getJsFiles(): array
    {
        return array_unique($this->jsFiles);
    }


    /**
     * 添加一个 css 文件
     * @param $path
     * @return Resource
     */
    public function appendJsFile($path): Resource
    {
        array_push($this->jsFiles, $path);
        return $this;
    }


    /**
     * 添加一个 js 文件 到最前面
     * @param $path
     * @return Resource
     */
    public function insertJsFile($path): Resource
    {
        array_unshift($this->jsFiles, $path);
        return $this;
    }


    /**
     * @return string
     */
    public function getPublicPath(): string
    {
        return (string)$this->publicPath;
    }

    /**
     * 设置 web根目录 资源文件根目录
     * @param string $publicPath
     * @return Resource
     */
    public function setPublicPath(string $publicPath): Resource
    {
        $this->publicPath = $publicPath;
        return $this;
    }

    /**
     * 安装资源文件
     */
    protected function installStatic(): bool
    {
        //web根目录
        $public_path = $this->getPublicPath();
        $public_path = $public_path ? $public_path : public_path();


        //目标文件
        $base = 'easy_admin_static/';
        $toDir = $public_path . $base;


        //初始化 css
        $this->insertCssFile('/' . $base . 'css/layout.css');
        $this->insertCssFile('/' . $base . 'layui-v2.5.7/css/layui.css');
        //初始化 js
        $this->insertJsFile('/' . $base . 'js/layout.js');
        $this->insertJsFile('/' . $base . 'layui-v2.5.7/layui.js');


        //安装过了 不再安装
        $lockPath = $toDir . 'is_install.lock';
        if (is_file($lockPath)) {
            return true;
        }

        //源文件
        $sourceDir = dirname(dirname(dirname(__FILE__))) . '/static/';

        //复制目录到 web 根目录
        $this->copyDir($sourceDir, $toDir);

        $handle = fopen($lockPath, "w");
        fwrite($handle, '资源安装锁文件,安装过之后不在安装;' . PHP_EOL . '更改资源文件后,没变化,删除后从新运行即可从新安装');
        fclose($handle);
        return true;
    }

    /**
     * @param $formDir
     * @param $toDir
     */
    protected function copyDir($formDir, $toDir)
    {
        if (!file_exists($toDir)) {
            mkdir($toDir);
        }
        $handle = opendir($formDir);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $_source = $formDir . '/' . $file;
            $_dest = $toDir . '/' . $file;
            if (is_file($_source)) copy($_source, $_dest);
            if (is_dir($_source)) $this->copyDir($_source, $_dest);
        }
        closedir($handle);
    }


}
