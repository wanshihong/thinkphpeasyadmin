<?php


namespace easyadmin\app\libs;


use easyadmin\app\columns\form\BaseForm;
use think\db\Query;
use think\Exception as ExceptionAlias;

/**
 * 列表过滤器类
 * Class ListFilter
 * @package easyadmin\app\libs
 */
class ListFilter
{
    /**
     * 查询过滤器,字段信息
     * @var array
     */
    private $filters = [];

    /**
     * 过滤器模板文件
     * @var string
     */
    private $template = 'list:filters';


    /**
     * 资源文件管理器
     * @var Resource
     */
    private $resource;

    public function __construct()
    {
        $this->resource = Resource::getInstance();
    }

    /**
     * 添加 js 文件
     * @param $path
     * @return $this
     */
    public function addJsFile($path): ListFilter
    {
        $this->resource->appendJsFile($path);
        return $this;
    }

    /**
     * 添加 css 文件
     * @param $path
     * @return $this
     */
    public function addCssFile($path): ListFilter
    {
        $this->resource->appendCssFile($path);
        return $this;
    }


    /**
     * 添加一个过滤器 字段
     * @param string $field 数据表的字段
     * @param string $label
     * @param string $fieldClass 字段的class引用
     * @param array $options 字段的其他属性
     * @return BaseForm
     */
    public function addFilter(string $field, string $label, string $fieldClass, $options = []): BaseForm
    {
        /** @var BaseForm $column */
        $column = new $fieldClass($field, $label, $options);

        array_push($this->filters, $column);
        return $column;
    }

    /**
     * 获取所有的过滤器
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }


    /**
     * 用户输入筛选条件以后,设置查询过滤
     *
     * @param Query $query 查询 query
     * @param array $fields 列表的所有字段
     * @param string $alias 主表别名
     * @return ListFilter
     */
    public function setFilterQuery( Query $query, array $fields, string $alias): ListFilter
    {

        $this->filters = array_map(function ($filter) use ( $query, $fields, $alias) {
            /** @var BaseForm $filter */

            //接收用户传参
            $filter->requestValue();
            $val = $filter->getValue();
            if ($val === null) {
                return $filter;
            }

            // _query_ 表示全部的字段查询, 相当于一个输入框, 搜索全部的字段
            if ($filter->getField() === '_query_') {
                //like 查询 全部的字段
                $query->whereOr(function ($query) use ($fields, $val) {
                    foreach ($fields as $field) {
                        $field = explode('as', $field);
                        if (empty($val)) continue;
                        $query->whereOr(trim($field[0]), 'like', "%{$val}%");
                    }
                });
            } else {
                //查询当前的一个字段
                $filter->filterQuery($query, $alias);
            }

            return $filter;

        }, $this->filters);

        return $this;
    }


    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return ListFilter
     */
    public function setTemplate(string $template): ListFilter
    {
        $this->template = $template;
        return $this;
    }


    /**
     * 渲染过滤器
     * @return string
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {
        $template = new Template();

        $template->fetch($this->getTemplate(), [
            'filters' => $this->getFilters()
        ]);
        return '';
    }

    /**
     * @param array $filters
     * @return ListFilter
     */
    public function setFilters(array $filters): ListFilter
    {
        $this->filters = $filters;
        return $this;
    }


}
