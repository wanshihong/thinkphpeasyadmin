<?php


namespace easyadmin\app\libs;


use easyadmin\app\columns\ColumnClass;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\db\Query;
use think\facade\Db;

/**
 * 查看详细页面
 * Class Table
 * @package easyadmin\app\libs
 */
class PageShow extends Page
{

    /**
     * @var Query
     */
    private $query;

    /**
     * 查询主表别名
     * @var string
     */
    private $alias = 't0';


    private $id = 0;


    /**
     * 页面字段类
     * @var ListField
     */
    protected $field;

    /**
     * 模板路径
     * @var string
     */
    protected $template = 'show:show';

    public function __construct($tableName, $pk = 'id')
    {
        $this->setTableName($tableName);
        $this->setPk($pk);
        $this->setField(new ListField());
        $this->setAction(new Actions());
    }


    /**
     * 创建查询
     * @param $id
     * @return Query|Db
     */
    public function createQuery($id)
    {
        $this->setId($id);
        $alias = $this->getAlias();
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $this->query = Db::table($this->getTableName())->alias($alias)->where("{$alias}.{$this->getPk()}", $id);
        return $this->query;
    }

    /**
     * 获取查询结果
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     */
    public function getResult(): ListTableRow
    {
        //查询字段
        $fields = $this->getField()->getQueryField($this->getAlias());
        $this->query->field($fields);

        $rowArr = $this->query->find();


        //实例化一行数据
        $listRow = new ListTableRow();
        //一行的初始化数据复制
        $listRow->setRow($rowArr);
        $listRow->setRowId($this->getId());


        array_map(function ($item) use ($rowArr, $listRow) {

            /** @var ColumnClass $column */
            $column = new $item['className']($item['field'], $item['label'], $item['options']);

            $column->setRow($listRow);
            //设置一列的值
            $column->setValue($rowArr);

            $listRow->addColumns($column);
            return $column;
        }, $this->getField()->getFields());

        return $listRow;

    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return PageShow
     */
    public function setAlias(string $alias): PageShow
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PageShow
     */
    public function setId(int $id): PageShow
    {
        $this->id = $id;
        return $this;
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
     * @return PageShow
     */
    public function setField(ListField $field): PageShow
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 添加一个列表 字段
     * @param string $field 数据表的字段
     * @param string $label
     * @param string $fieldClass 字段的class引用
     * @param array $options 字段的其他属性
     * @return PageShow
     */
    public function addField(string $field, string $label, string $fieldClass, $options = []): PageShow
    {
        $this->getField()->addField($field, $label, $fieldClass, $options);
        return $this;
    }

}
