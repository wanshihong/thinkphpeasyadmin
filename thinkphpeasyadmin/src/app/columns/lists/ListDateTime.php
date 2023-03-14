<?php


namespace easyadmin\app\columns\lists;


class ListDateTime extends BaseList
{

    protected array $options = [
        'format' => 'Y-m-d H:i:s',
        'in_format' => 'strtotime',//用户输入的值,用什么格式化  strtotime
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'list:field:text';

    /**
     * 格式化数据
     * 格式化给用户看的
     * @return mixed
     */
    public function formatValue() :mixed
    {
        $ret = $this->getValue();

        if ($ret === null) {
            return null;
        }

        $format = $this->getOption('format');
        if ($format === null) {
            return $ret;
        }

        if ($format && (is_callable($format) || function_exists($format))) {
            return call_user_func($format, $ret,$this->row->getRow());
        }

        if ($ret > 0) {
            return date($format, $ret);
        }
        return $this->getOption('default');

    }

}
