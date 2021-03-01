<?php


namespace easyadmin\app\libs;


use Iterator as IteratorAlias;

/**
 * 列表页面 数据表格
 * Class Table
 * @package easyadmin\app\libs
 */
class PageList extends Page implements IteratorAlias
{

    /**
     * 总行数
     * @var int
     */
    private $total = 0;


    /**
     * 行
     * @var array
     */
    private $rows = [];


    /**
     * 分页类
     * @var Pagination
     */
    private $page;


    /**
     * 条件过滤类
     * @var ListFilter
     */
    private $filter;

    /**
     * 表格每一行的按钮
     * @var array
     */
    private $actions = [];


    /**
     * 添加按钮
     * @var Btn|null
     */
    private $actionAdd;

    /**
     * 模板路径
     * @var string
     */
    protected $template = 'list:list';


    /**
     * 页面字段类
     * @var ListField
     */
    protected $field;

    /**
     * 查询排序
     * @var array
     */
    private $orderBy = [];


    public function __construct($siteName, $tableName, $pageName, $pk)
    {
        $this->setPageName($pageName);
        $this->setTableName($tableName);
        $this->setSiteName($siteName);
        $this->setPk($pk);

        //初始化 列表字段
        $this->setField(new ListField());

        //初始化 过滤器字段
        $this->setFilter(new ListFilter());

        //初始化 分页类
        $this->setPage(new Pagination());

        //初始化 面包屑
        $this->setBreadcrumb(new Breadcrumb());
        $this->configBreadcrumb();

    }

    public function configBreadcrumb()
    {
        $this->getBreadcrumb()
            ->add('首页', url('home/index'), 'layui-icon-home')
            ->add("{$this->getPageName()}列表", url('lists'));
    }


    /**
     * @param ListTableRow $row
     * @return PageList
     */
    public function addRow(ListTableRow $row): PageList
    {
        array_push($this->rows, $row);
        return $this;
    }


    public function current()
    {
        return current($this->rows);
    }

    public function next()
    {
        return next($this->rows);
    }

    public function key()
    {
        return key($this->rows);
    }

    public function valid(): bool
    {
        return key($this->rows) !== null;
    }

    public function rewind()
    {
        return reset($this->rows);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return PageList
     */
    public function setTotal(int $total): PageList
    {
        $this->total = $total;
        return $this;
    }

    /**
     * 获取表格的表头 header title
     * @return array
     */
    public function getHeader(): array
    {
        return array_column($this->getField()->getFields(), 'label');
    }


    /**
     * @return Pagination
     */
    public function getPage(): Pagination
    {
        return $this->page;
    }

    /**
     * @param Pagination $page
     * @return PageList
     */
    public function setPage(Pagination $page): PageList
    {
        $page->setTotal($this->getTotal());
        $this->page = $page;
        return $this;
    }

    /**
     * @return ListFilter
     */
    public function getFilter(): ListFilter
    {
        return $this->filter;
    }

    /**
     * @param ListFilter $filter
     * @return PageList
     */
    public function setFilter(ListFilter $filter): PageList
    {
        $this->filter = $filter;
        return $this;
    }


    /**
     * 获取当前的 排序配置
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * 设置查询排序
     * @param array $orderBy
     * @return PageList
     */
    public function setOrderBy(array $orderBy): PageList
    {
        $this->orderBy = $orderBy;
        return $this;
    }


    /**
     * @param Btn $actionAdd
     * @return PageList
     */
    public function setActionAdd(Btn $actionAdd): PageList
    {
        $this->actionAdd = $actionAdd;
        return $this;
    }

    /**
     * @return Btn|null
     */
    public function getActionAdd(): ?Btn
    {
        return $this->actionAdd;
    }


    /**
     * 添加一个 操作按钮
     * @param $label
     * @param $url
     * @param array $options
     * @return $this
     */
    public function addAction($label, $url, $options = []): Page
    {
        array_push($this->actions, [
            'label' => $label,
            'url' => $url,
            'options' => $options
        ]);
        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return ListField
     */
    public function getField(): ListField
    {
        return $this->field;
    }

    /**
     * @param ListField $field
     * @return PageList
     */
    public function setField(ListField $field): PageList
    {
        $this->field = $field;
        return $this;
    }


}
