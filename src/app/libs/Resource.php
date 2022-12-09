<?php


namespace easyadmin\app\libs;

/**
 * 资源文件相关方法
 * Class Resource
 * @package easyadmin\app\libs
 */
class Resource
{

    private $static_root = '/easy_admin_static/';


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
     * 私有属性，用于保存实例
     * @var Resource
     */
    private static $instance;

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
        //初始化 css
        $this->insertCssFile('css/layout.css');
        $this->insertCssFile('layui-v2.5.7/css/layui.css');
        //初始化 js
        $this->insertJsFile('js/copy.js');
        $this->insertJsFile('js/clipboard-polyfill.promise.js');
        $this->insertJsFile('js/layout.js');
        $this->insertJsFile('layui-v2.5.7/layui.js');
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->static_root;
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
        $path = $this->getRoot() . $path;
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
        $path = $this->getRoot() . $path;
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
        $path = $this->getRoot() . $path;
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
        $path = $this->getRoot() . $path;
        array_unshift($this->jsFiles, $path);
        return $this;
    }


}
