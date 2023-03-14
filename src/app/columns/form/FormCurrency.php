<?php


namespace easyadmin\app\columns\form;

/**
 * 货币输入框
 * Class FormText
 * @package easyadmin\app\columns\form
 */
class FormCurrency extends BaseForm
{

    protected array $options = [
        'icon' => 'layui-icon layui-icon-rmb',
        'type' => 'number'
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:text';


}
