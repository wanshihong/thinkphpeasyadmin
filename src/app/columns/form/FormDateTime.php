<?php


namespace easyadmin\app\columns\form;

use think\Exception;

class FormDateTime extends BaseForm
{

    protected $jsFiles = ['js/laydate.js'];

    protected $options = [
        'jsFiles' => [],
        // layui-date 文档 https://www.layui.com/doc/modules/laydate.html#use
        // options 使用官方参数
        'options' => [
            'type' => 'datetime'
        ]
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:datetime';


    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data)
    {
        $data['options'] = json_encode($this->getOption('options', []));
        $render_class = 'lay-date' . time() . mt_rand(10000, 99999);
        $data['class'] .= " lay-date {$render_class}";
        $data['render_class'] = $render_class;
        return $data;
    }


    /**
     * 格式化数据
     * 格式化给用户看的
     * @return bool|int|mixed|string|null
     */
    public function formatValue()
    {
        $ret = $this->getValue();

        if ($ret === null) {
            return null;
        }

        $format = $this->getOption('format');
        if ($format === null) {
            return $ret;
        }

        if (is_callable($format) || function_exists($format)) {
            $ret = call_user_func($format, $ret);
        } elseif (is_string($format)) {
            $ret = date($format, $ret);
        }
        return $ret;
    }

    /**
     * 格式化数据
     * 格式化后存入数据库,或者操作数据库
     * @param $val
     * @return mixed
     */
    public function inFormat($val)
    {
        $ret = $val;

        $format = $this->getOption('in_format');
        if ($format === null) {
            $ret = $val;
        }

        if (is_callable($format) || function_exists($format)) {
            $ret = call_user_func($format, $val);
        }

        return $ret;
    }


}
