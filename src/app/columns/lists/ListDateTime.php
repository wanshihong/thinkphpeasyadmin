<?php


namespace easyadmin\app\columns\lists;


class ListDateTime extends BaseList
{

    protected $options = [
        'format' => 'Y-m-d H:i:s',
        'in_format' => 'strtotime',//用户输入的值,用什么格式化  strtotime
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'list:field:text';

    public function formatValue()
    {
        $format = $this->getOption('format', 'Y-m-d H:i:s');
        return date($format, $this->getValue());
    }

}
