<?php


namespace easyadmin\app\columns\form;

/**
 * 数字输入框
 * Class FormText
 * @package easyadmin\app\columns\form
 */
class FormNumber extends BaseForm
{

    protected $options = [
        'type' => 'number'
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:text';


}
