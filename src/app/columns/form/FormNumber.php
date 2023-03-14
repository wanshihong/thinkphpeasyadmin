<?php


namespace easyadmin\app\columns\form;

/**
 * 数字输入框
 * Class FormText
 * @package easyadmin\app\columns\form
 */
class FormNumber extends BaseForm
{

    protected array $options = [
        'type' => 'number'
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:text';


}
