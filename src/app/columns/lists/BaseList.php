<?php


namespace easyadmin\app\columns\lists;


use easyadmin\app\columns\ColumnClass;
use easyadmin\app\libs\Template;
use think\Exception as ExceptionAlias;


class BaseList extends ColumnClass
{

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'list:field:text';


    /**
     * @return array
     */
    protected function getData(): array
    {
        $elemId = 'id_' . $this->getSelectAlias() . '_' . $this->row->getRowId();

        return [
            'field' => $this->getField(),//列的字段
            'row' => $this->row->getRow(), //行的数据 array
            'row_id' => $this->row->getRowId(), //行的主键值
            'attr' => $this->getOption('attr', ''), //列的属性
            'class' => $this->getOption('class', ''), //列的样式
            //dom 元素 id
            'elem_id' => str_replace(':', '_', $elemId),
        ];
    }

    /**
     * @return mixed
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {
        $template = new Template();

        $data = $this->getData();

        $data['value'] = $this->formatValue();
        $data['value'] = $this->filterHighlight($data['value']);

        $data = $this->formatData($data);
        $template->fetch($this->getTemplatePath(), $data);
        return '';
    }

}
