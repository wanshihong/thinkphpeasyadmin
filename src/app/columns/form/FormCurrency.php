<?php


namespace easyadmin\app\columns\form;

/**
 * 货币输入框
 * Class FormText
 * @package easyadmin\app\columns\form
 */
class FormCurrency extends BaseForm
{

    protected $options = [
        'icon' => 'layui-icon-rmb',
        'type' => 'number'
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:text';


}
