<?php


namespace easyadmin\app\columns\form;

use think\Exception;

class FormDateTime extends BaseForm
{

    protected $jsFiles = ['/easy_admin_static/js/laydate.js'];

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


}
