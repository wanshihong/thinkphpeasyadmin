<?php


namespace easyadmin\app\libs;

use easyadmin\app\columns\ColumnClass;
use Iterator as IteratorAlias;

/**
 * 表格中的一行数据
 * Class ListTableRow
 * @package easyadmin\app\libs
 */
class ListTableRow
{


    /**
     * 行标识,通常是一行的ID
     * @var array
     */
    private array $row;

    /**
     * 一行中主键的值
     * @var int
     */
    private int $rowId;

    /**
     * @var Actions
     */
    private Actions $actions;


    /**
     * 一行中 列的存放
     * @var array
     */
    private array $columns = [];

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * 获取某一列的值
     * @param string $key
     * @return mixed
     */
    public function getColumnVal(string $key): mixed
    {
        if ($this->row && array_key_exists($key, $this->row)) {
            return $this->row[$key] ?: '';
        }
        return '';
    }

    /**
     * @param $row
     * @return ListTableRow
     */
    public function setRow($row): ListTableRow
    {
        $this->row = $row;
        return $this;
    }

    /**
     * 或者一列的值
     * @param $field
     * @return mixed
     */
    public function getRowValue($field): mixed
    {
        return array_key_exists($field, $this->row) ? $this->row[$field] : null;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param ColumnClass $column
     * @return ListTableRow
     */
    public function addColumns(ColumnClass $column): ListTableRow
    {
        array_push($this->columns, $column);
        return $this;
    }


    /**
     * @return int
     */
    public function getRowId(): int
    {
        return $this->rowId;
    }

    /**
     * @param int $rowId
     */
    public function setRowId(int $rowId)
    {
        $this->rowId = $rowId;
    }

    /**
     * @return Actions
     */
    public function getActions(): Actions
    {

        $ret = [];
        /** @var Btn $action */
        foreach ($this->actions->getActions() as $action) {

            // 是否显示这个按钮
            $call = $action->getCondition();
            if (is_callable($call)) {
                $isShowAction = call_user_func($call, $this->row);
                if ($isShowAction) {
                    array_push($ret, $action);
                }
            } else {
                array_push($ret, $action);
            }
        }
        return $this->actions->setActions($ret);
    }

    /**
     * @param Actions $actions
     * @return ListTableRow
     */
    public function setActions(Actions $actions): ListTableRow
    {
        $this->actions = $actions;
        return $this;
    }


}
