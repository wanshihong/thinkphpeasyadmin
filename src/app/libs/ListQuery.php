<?php


namespace easyadmin\app\libs;


use easyadmin\app\columns\ColumnClass;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\db\Query;
use think\Exception as ExceptionAlias;
use think\facade\Db;
use think\Request;

/**
 * 列表的查询类
 * Class ListQuery
 * @package easyadmin\app\libs
 */
class ListQuery
{


    /**
     * 查询的 query 对象
     * @var Query
     */
    private $query;

    /**
     * 主表的别名
     * @var string
     */
    private $alias = 't0';


    /**
     * 数据请求对象
     * @var Request
     */
    private $request;


    /**
     *
     * ListQuery constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * 获取当前表的别名
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * 设置当前查询主表的别名
     * @param string $alias
     * @return ListQuery
     */
    public function setAlias(string $alias): ListQuery
    {
        $this->alias = $alias;
        return $this;
    }


    /**
     * 获取查询 query
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * 创建一个查询
     * @param PageList $table
     * @return $this
     */
    public function createQuery(PageList $table): ListQuery
    {

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $this->query = Db::table($table->getTableName());
        $alias = $this->getAlias();

        //查询排序
        $this->_orderBy($table);

        //设置查询字段
        $fields = $table->getField()->getQueryField($alias);
        $this->query->field($fields);


        //过滤器参数
        $table->getFilter()->setFilterQuery($this->request, $this->query, $fields, $alias);

        //查询别名
        $this->query->alias($alias);

        return $this;
    }


    /**
     * 获取查询结果
     *
     * @param PageList $table
     * @return PageList
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias|ExceptionAlias
     */
    public function listResult(PageList $table): PageList
    {
        $countQuery = $this->getQuery();
        $searchQuery = $this->getQuery();

        //获取总的条目数据
        $total = $countQuery->count();
        $table->setTotal($total);

        //查询分页
        $page = $table->getPage(); //获取分页类
        $p = $this->request->get('page', $page->getCurrentPage());
        $page->setCurrentPage($p); //设置当前分页的页码
        $result = $searchQuery->page($p, $page->getPageSize())->select();


        //获取查询结果
        foreach ($result as $index => $item) {
            //实例化一行数据
            $listRow = new ListTableRow();
            //一行的初始化数据复制
            $listRow->setRow($item);
            //一行的主键值  设置
            if (array_key_exists($table->getPk(), $item)) {
                $listRow->setRowId($item[$table->getPk()]);
            } else {
                $listRow->setRowId($index);
            }

            //获取一行的数据, 一行中的列
            $this->_getColumn($table, $listRow);

            //表格中添加一行数据
            $table->addRow($listRow);

            //一行有哪些操作按钮
            $this->setRowActions($table, $listRow);
        }

        $table->setPage($page);
        return $table;
    }

    /**
     * @param PageList $table
     * @param ListTableRow $row
     * @throws ExceptionAlias
     */
    protected function setRowActions(PageList $table, ListTableRow $row)
    {
        $action = new Actions();
        foreach ($table->getActions() as $item) {

            $label = empty($item['label']) ? '' : $item['label'];
            $url = empty($item['url']) ? '' : $item['url'];
            $options = empty($item['options']) ? [] : $item['options'];


            $params = empty($options['params']) ? [] : $options['params'];
            $params[$table->getPk()] = $row->getRowId();


            $options['params'] = $params;
            $action->addAction($label, $url, $options);

        }
        $row->setActions($action);
    }


    /**
     * 列表查询排序
     * @param PageList $page
     * @return void
     */
    private function _orderBy(PageList $page)
    {

        if (!$page->getOrderBy()) return;

        /**
         * 例如
         * ["id" => "desc","a.id" => "asc"]
         * id 需要变成 t0.id
         * a.id 不用变
         */

        $ret = [];
        foreach ($page->getOrderBy() as $field => $type) {
            if (stripos($field, '.') === false) {
                $field = $this->alias . '.' . $field;
            }
            $ret[$field] = $type;
        }

        $this->query->order($ret);
    }

    /**
     * 如果有配置软删除, 查询没有被删除的数据
     * @param $field
     * @param $beforeVal
     */
    public function softDelete($field, $beforeVal)
    {
        if (empty($field)) return;
        $this->query->where($field, $beforeVal);
    }


    /**
     * 获取一行的数据, 一行中的列
     * @param PageList $table
     * @param ListTableRow $listRow
     */
    private function _getColumn(PageList $table, ListTableRow $listRow)
    {
        /** @var ColumnClass $field */
        foreach ($table->getField()->getFields() as $index => $item) {

            /** @var ColumnClass $field */
            $field = new $item['className']($item['field'], $item['label'], $item['options']);

            //设置一列的值
            $field->setValue($listRow->getRow());

            //记录一行的数据
            $field->setRow($listRow);

            //记录有哪些过滤
            $field->setFilters($table->getFilter()->getFilters());

            //把列添加到行中
            $listRow->addColumns($field);

        }
    }

}
