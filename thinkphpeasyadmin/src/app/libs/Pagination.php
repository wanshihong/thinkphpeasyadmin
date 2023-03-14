<?php


namespace easyadmin\app\libs;


use think\Exception as ExceptionAlias;

/**
 * 分页类
 * Class Page
 * @package easyadmin\app\libs
 */
class Pagination
{
    /**
     * 当前页码
     * @var int
     */
    private $currentPage = 1;

    /**
     * 一共有多少条数据
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    private $pageSize = 10;

    /**
     * 分页额外,条件
     * @var array
     */
    private $options = [];

    /**
     * 分页模板路径
     * @var string
     */
    private $template = "list:page";


    /**
     * 获取分页的大小
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * 设置分页的大小
     * @param int $pageSize
     * @return Pagination
     */
    public function setPageSize(int $pageSize): Pagination
    {
        $this->pageSize = $pageSize;
        return $this;
    }


    /**
     * 设置当前一共多少条数据
     * @param int $total
     * @return Pagination
     */
    public function setTotal(int $total): Pagination
    {
        $this->total = $total;
        return $this;
    }

    /**
     * 获取一共多少条数据
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }


    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return Pagination
     */
    public function setCurrentPage(int $currentPage): Pagination
    {
        $this->currentPage = $currentPage;
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
     * @return Pagination
     */
    public function setTemplate(string $template): Pagination
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    /**
     * 渲染分页
     * @return string
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {
        $template = new Template();
        $options = $this->getOptions();

        $gets = request()->get();
        unset($gets['page']);

        $options = array_merge($gets, $options);

        $template->fetch($this->getTemplate(), [
            'page' => $this->getCurrentPage(),
            'total' => $this->getTotal(),
            'pageTotal' => ceil($this->getTotal() / $this->getPageSize()),
            'options' => $options ? http_build_query($options) : ''
        ]);
        return '';
    }


}
