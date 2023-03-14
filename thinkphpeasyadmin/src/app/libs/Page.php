<?php


namespace easyadmin\app\libs;


use think\Template;

class Page
{

    /**
     * 表名称
     * @var string
     */
    private $tableName;


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

}
