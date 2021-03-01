<?php


namespace easyadmin\app\libs;


use think\Exception;
use think\Template;

class Page
{
    /**
     * @var string 网站标题
     */
    private $siteName;

    /**
     * 表名称
     * @var string
     */
    private $tableName;

    /**
     * 页面名称, 标题
     * @var string
     */
    private $pageName;

    /**
     * 表的主键
     * @var string
     */
    private $pk = 'id';


    /**
     * 页面的面包屑
     * @var Breadcrumb;
     */
    private $breadcrumb;

    /**
     * 页面的操作按钮
     * @var Actions
     */
    private $action;


    /**
     * 模板路径
     * @var string
     */
    protected $template;


    /**
     * @var array
     * 赋值到页面的是变量都装在这个里面的
     * 外部自定义页面 赋值自定义变量也是用他
     */
    public $data = [];

    protected $jsFiles = [];
    protected $cssFiles = [];


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return Page
     */
    public function setTableName(string $tableName): Page
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPk(): string
    {
        return $this->pk;
    }

    /**
     * @param string $pk
     * @return Page
     */
    public function setPk(string $pk): Page
    {
        $this->pk = $pk;
        return $this;
    }


    /**
     * @return Breadcrumb
     */
    public function getBreadcrumb(): Breadcrumb
    {
        return $this->breadcrumb;
    }

    /**
     * @param Breadcrumb $breadcrumb
     * @return Page
     */
    public function setBreadcrumb(Breadcrumb $breadcrumb): Page
    {
        $this->breadcrumb = $breadcrumb;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     * @return Page
     */
    public function setPageName(string $pageName): Page
    {
        $this->pageName = $pageName;
        return $this;
    }


    /**
     * 设置模板路径
     * 设置列表的模板路径
     * @param string $template
     * @return Page
     */
    public function setTemplate(string $template): Page
    {
        $this->template = $template;
        return $this;
    }

    /**
     * 获取模板路径
     * @return Template
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return Actions
     */
    public function getAction(): Actions
    {
        return $this->action;
    }

    /**
     * @param Actions $action
     * @return Page
     */
    public function setAction(Actions $action): Page
    {
        $this->action = $action;
        return $this;
    }


    /**
     * 渲染页面
     * @param $pageName
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function fetch($pageName, $data): string
    {
        //页面名称
        $data['__page_name__'] = $pageName;

        //资源文件
        $resource = Resource::getInstance();

        foreach ($this->getCssFiles() as $css){
            $resource->appendCssFile($css);
        }

        foreach ($this->getJsFiles() as $js){
            $resource->appendJsFile($js);
        }

        $data['__css__'] = $resource->getCssFiles();
        $data['__js__'] = $resource->getJsFiles();

        //导航
        $data['__menu__'] = Menu::getInstance();
        $data['__breadcrumb__'] = $this->getBreadcrumb();
        $data['__site_name__'] = $this->getSiteName();

        //模板
        $path = $this->getTemplate();
        $template = new \easyadmin\app\libs\Template();

        $data = array_merge($this->data, $data);

        //渲染
        $template->fetch($path, $data);
        return '';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 添加一个值到页面
     * @param $key
     * @param $value
     */
    public function addDataToView($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->siteName;
    }

    /**
     * @param string $siteName
     */
    public function setSiteName(string $siteName): Page
    {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @return array
     */
    public function getJsFiles(): array
    {
        return $this->jsFiles;
    }

    /**
     * @param array $jsFiles
     */
    public function setJsFiles(array $jsFiles): void
    {
        $this->jsFiles = $jsFiles;
    }

    /**
     * @return array
     */
    public function getCssFiles(): array
    {
        return $this->cssFiles;
    }

    /**
     * @param array $cssFiles
     */
    public function setCssFiles(array $cssFiles): void
    {
        $this->cssFiles = $cssFiles;
    }

}
