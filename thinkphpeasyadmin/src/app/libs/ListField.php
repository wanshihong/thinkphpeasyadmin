<?php


namespace easyadmin\app\libs;


use easyadmin\app\columns\ColumnClass;

/**
 * 列表字段类
 * Class ListField
 * @package easyadmin\app\libs
 */
class ListField
{

    /**
     * 查询哪些字段
     * @var array
     */
    private $fields = [];

    /**
     * 获取列表有哪些字段
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * 添加一个列表 字段
     * @param string $field 数据表的字段
     * @param string $label
     * @param string $fieldClass 字段的class引用
     * @param array $options 字段的其他属性
     * @return ListField
     */
    public function addField(string $field, string $label, string $fieldClass, $options = []): ListField
    {
        array_push($this->fields, [
            'field' => $field,
            'label' => $label,
            'className' => $fieldClass,
            'options' => $options
        ]);
        return $this;
    }


    /**
     * 设置查询字段
     * @param $alias
     * @return array
     */
    public function getQueryField($alias): array
    {
        if (empty($this->getFields())) {
            return [];
        }

        $fields = [];
        array_map(function ($item) use ($alias, &$fields) {
            /** @var ColumnClass $column */
            $column = new $item['className']($item['field'], $item['label'], $item['options']);
            $column->getSelectField($alias, true, $fields);
        }, $this->getFields());


        return $fields;
    }


}
