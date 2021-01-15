<?php


namespace easyadmin\app\libs;

use easyadmin\app\columns\ColumnClass;
use Iterator as IteratorAlias;

/**
 * 表格中的一行数据
 * Class ListTableRow
 * @package easyadmin\app\libs
 */
class ListTableRow implements IteratorAlias
{


    /**
     * 行标识,通常是一行的ID
     * @var array
     */
    private $row;

    /**
     * 一行中主键的值
     * @var int
     */
    private $rowId;

    /**
     * @var Actions
     */
    private $actions;


    /**
     * 一行中 列的存放
     * @var array
     */
    private $columns = [];

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
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
     * @return mixed|null
     */
    public function getRowValue($field)
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

    public function current()
    {
        return current($this->columns);
    }

    public function next()
    {
        return next($this->columns);
    }

    public function key()
    {
        return key($this->columns);
    }

    public function valid(): bool
    {
        return key($this->columns) !== null;
    }

    public function rewind()
    {
        return reset($this->columns);
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
        return $this->actions;
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
