<?php


namespace easyadmin\app\libs;


use easyadmin\app\columns\form\BaseForm;
use easyadmin\app\columns\form\FormHidden;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\db\Query;
use think\facade\Db;

/**
 * 添加编辑页面
 * Class Table
 * @package easyadmin\app\libs
 */
class PageForm extends Page
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
     * @var array
     */
    private $fields = [];

    /**
     * 模板路径
     * @var string
     */
    protected $template = 'form:form';


    public function __construct($tableName, $pk = 'id')
    {
        $this->setTableName($tableName);
        $this->setPk($pk);
        $this->setAction(new Actions());
        $this->setBreadcrumb(Breadcrumb::getInstance());
        $resource = Resource::getInstance();
        $resource->appendJsFile('/easy_admin_static/js/form.js');
    }


    /**
     * 创建查询
     * @return Query|null
     */
    public function createQuery(): ?Query
    {
        $id = $this->getId();
        if (empty($id)) {
            return null;
        }
        $alias = $this->getAlias();
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $this->query = Db::table($this->getTableName())->alias($alias)->where("{$alias}.{$this->getPk()}", $id);
        return $this->query;
    }

    /**
     * 获取查询结果
     * @return null
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     */
    public function getResult()
    {
        if (!$this->getQuery()) {
            return null;
        }
        //查询字段
        $this->query->field($this->getQueryFields());

        $data = $this->query->find();
        if ($data) {
            $this->formatFieldValue($data);
        }
        return $data;
    }

    /**
     * 把查询出来的数据设置到字段上面
     * @param array $data 查询出来的数据
     */
    protected function formatFieldValue(array $data)
    {
        $fields = $this->getFields();

        /** @var BaseForm $field */
        foreach ($fields as $field) {
            $field->setValue($data);
        }
    }


    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }


    /**
     * 添加一个表单 字段
     * @param string $field 数据表的字段
     * @param string $label
     * @param string $fieldClass 字段的class引用
     * @param array $options 字段的其他属性
     * @return PageForm
     */
    public function addField(string $field, string $label, string $fieldClass, $options = []): PageForm
    {
        /** @var BaseForm $column */
        $column = new $fieldClass($field, $label, $options);

        array_push($this->fields, $column);
        return $this;
    }

    /**
     * 获取查询字段
     * @return array|string[]
     */
    public function getQueryFields(): array
    {
        $fields = [];
        array_map(function ($item) use (&$fields) {
            /** @var BaseForm $item */
            return $item->getSelectField($this->alias, true, $fields);
        }, $this->getFields());

        return $fields;
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
     * @return PageForm
     */
    public function setAlias(string $alias): PageForm
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
     * @return PageForm
     */
    public function setId(int $id): PageForm
    {
        $this->id = $id;
        $this->addField($this->getPk(), 'id', FormHidden::class, ['default' => $id]);
        return $this;
    }

}
